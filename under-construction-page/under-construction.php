<?php
/*
  Plugin Name: Under Construction
  Plugin URI: https://underconstructionpage.com/
  Description: Put your site behind a great looking under construction, coming soon, maintenance mode or landing page.
  Author: WebFactory Ltd
  Version: 3.92
  Requires at least: 4.0
  Requires PHP: 5.2
  Tested up to: 5.8
  Author URI: https://www.webfactoryltd.com/
  Text Domain: under-construction-page

  Copyright 2015 - 2022  WebFactory Ltd  (email: ucp@webfactoryltd.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// this is an include only WP file
if (!defined('ABSPATH')) {
    die;
}


define('UCP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('UCP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('UCP_OPTIONS_KEY', 'ucp_options');
define('UCP_META_KEY', 'ucp_meta');
define('UCP_POINTERS_KEY', 'ucp_pointers');
define('UCP_NOTICES_KEY', 'ucp_notices');
define('UCP_SURVEYS_KEY', 'ucp_surveys');


// main plugin class
class UCP
{
    static $version = 0;
    static $licensing_servers = array('https://license1.underconstructionpage.com/', 'https://license2.underconstructionpage.com/');


    // get plugin version from header
    static function get_plugin_version()
    {
        $plugin_data = get_file_data(__FILE__, array('version' => 'Version'), 'plugin');
        self::$version = $plugin_data['version'];

        return $plugin_data['version'];
    } // get_plugin_version


    // hook things up
    static function init()
    {
        // check if minimal required WP version is present
        if (false === self::check_wp_version(4.0)) {
            return false;
        }

        if (is_admin()) {
            // if the plugin was updated from ver < 1.20 upgrade settings array
            self::maybe_upgrade();

            // add UCP menu to admin tools menu group
            add_action('admin_menu', array(__CLASS__, 'admin_menu'));

            // settings registration
            add_action('admin_init', array(__CLASS__, 'register_settings'));
        
            add_filter('admin_footer_text', array(__CLASS__, 'admin_footer_text'));
            add_filter('admin_footer', array(__CLASS__, 'admin_footer'));

            // manages admin header notifications
            add_action('admin_notices', array(__CLASS__, 'admin_notices'));
            add_action('admin_action_ucp_dismiss_notice', array(__CLASS__, 'dismiss_notice'));
            add_action('admin_action_ucp_change_status', array(__CLASS__, 'change_status'));
            add_action('admin_action_ucp_reset_settings', array(__CLASS__, 'reset_settings'));
            add_action('admin_action_install_wpfssl', array(__CLASS__, 'install_wpfssl'));

            // enqueue admin scripts
            add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_enqueue_scripts'), 100, 1);

            // AJAX endpoints
            add_action('wp_ajax_ucp_dismiss_pointer', array(__CLASS__, 'dismiss_pointer_ajax'));
            add_action('wp_ajax_ucp_dismiss_survey', array(__CLASS__, 'dismiss_survey_ajax'));
            add_action('wp_ajax_ucp_submit_survey', array(__CLASS__, 'submit_survey_ajax'));
        } else {
            // main plugin logic
            add_action('wp', array(__CLASS__, 'display_construction_page'), 0, 1);

            // show under construction notice on login form
            add_filter('login_message', array(__CLASS__, 'login_message'));

            // disable feeds
            add_action('do_feed_rdf', array(__CLASS__, 'disable_feed'), 0, 1);
            add_action('do_feed_rss', array(__CLASS__, 'disable_feed'), 0, 1);
            add_action('do_feed_rss2', array(__CLASS__, 'disable_feed'), 0, 1);
            add_action('do_feed_atom', array(__CLASS__, 'disable_feed'), 0, 1);

            add_action('wp_footer', array(__CLASS__, 'whitelisted_notice'));
        } // if not admin

        // admin bar notice for frontend & backend
        add_action('wp_before_admin_bar_render', array(__CLASS__, 'admin_bar'));
        add_action('wp_head', array(__CLASS__, 'admin_bar_style'));
        add_action('admin_head', array(__CLASS__, 'admin_bar_style'));
    } // init


    // check if user has the minimal WP version required by UCP
    static function check_wp_version($min_version)
    {
        if (!version_compare(get_bloginfo('version'), $min_version,  '>=')) {
            add_action('admin_notices', array(__CLASS__, 'notice_min_wp_version'));
            return false;
        } else {
            return true;
        }
    } // check_wp_version


    // display error message if WP version is too low
    static function notice_min_wp_version()
    {
        echo '<div class="error"><p>' . sprintf(esc_attr__('UnderConstruction plugin <b>requires WordPress version 4.0</b> or higher to function properly. You are using WordPress version %s. Please <a href="%s">update it</a>.', 'wpdevhelper'), get_bloginfo('version'), admin_url('update-core.php')) . '</p></div>';
    } // notice_min_wp_version_error


    // some things have to be loaded earlier
    static function plugins_loaded()
    {
        self::get_plugin_version();

        load_plugin_textdomain('under-construction-page');
    } // plugins_loaded


    // activate doesn't get fired on upgrades so we have to compensate
    public static function maybe_upgrade()
    {
        $meta = self::get_meta();
        $options = self::get_options();

        // added in v1.70 to rename roles to whitelisted_roles
        if (isset($options['roles'])) {
            $options['whitelisted_roles'] = $options['roles'];
            unset($options['roles']);
            update_option(UCP_OPTIONS_KEY, $options);
        }

        // check if we need to convert options from the old format to new, or maybe it is already done
        if (isset($meta['options_ver']) && $meta['options_ver'] == self::$version) {
            return;
        }

        if (get_option('set_size') || get_option('set_tweet') || get_option('set_fb') || get_option('set_font') || get_option('set_msg') || get_option('set_opt') || get_option('set_admin')) {
            // convert old options to new
            $options['status'] = (get_option('set_opt') === 'Yes') ? '1' : '0';
            $options['content'] = trim(get_option('set_msg'));
            $options['whitelisted_roles'] = (get_option('set_admin') === 'No') ? array('administrator') : array();
            $options['social_facebook'] = trim(get_option('set_fb'));
            $options['social_twitter'] = trim(get_option('set_tweet'));
            update_option(UCP_OPTIONS_KEY, $options);

            delete_option('set_size');
            delete_option('set_tweet');
            delete_option('set_fb');
            delete_option('set_font');
            delete_option('set_msg');
            delete_option('set_opt');
            delete_option('set_admin');

            self::reset_pointers();
        }

        // we update only once
        $meta['options_ver'] = self::$version;
        update_option(UCP_META_KEY, $meta);
    } // maybe_upgrade


    // get plugin's options
    static function get_options()
    {
        $options = get_option(UCP_OPTIONS_KEY, array());

        if (!is_array($options)) {
            $options = array();
        }
        $options = array_merge(self::default_options(), $options);

        return $options;
    } // get_options


    // get plugin's meta data
    static function get_meta()
    {
        $meta = get_option(UCP_META_KEY, array());

        if (!is_array($meta) || empty($meta)) {
            $meta['first_version'] = self::get_plugin_version();
            $meta['first_install'] = time();
            update_option(UCP_META_KEY, $meta);
        }

        return $meta;
    } // get_meta


    // fetch and display the construction page if it's enabled or preview requested
    static function display_construction_page()
    {
        $options = self::get_options();
        $request_uri = trailingslashit(strtolower(@parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));

        // just to be on the safe side
        if (defined('DOING_CRON') && DOING_CRON) {
            return false;
        }
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return false;
        }
        if (defined('WP_CLI') && WP_CLI) {
            return false;
        }

        // some URLs have to be accessible at all times
        if (
            $request_uri == '/wp-admin/' ||
            $request_uri == '/feed/' ||
            $request_uri == '/feed/rss/' ||
            $request_uri == '/feed/rss2/' ||
            $request_uri == '/feed/rdf/' ||
            $request_uri == '/feed/atom/' ||
            $request_uri == '/admin/' ||
            $request_uri == '/wp-login.php'
        ) {
            return;
        }

        if (true == self::is_construction_mode_enabled(false) || (is_user_logged_in() && isset($_GET['ucp_preview']))) {
            header(self::wp_get_server_protocol() . ' 200 OK');
            if ($options['end_date'] && $options['end_date'] != '0000-00-00 00:00') {
                header('Retry-After: ' . date('D, d M Y H:i:s T', strtotime($options['end_date'])));
            } else {
                header('Retry-After: ' . DAY_IN_SECONDS);
            }

            $themes = self::get_themes();
            if (!empty($_GET['theme']) && substr(sanitize_text_field($_GET['theme']), 5) != '_pro_' && !empty($themes[sanitize_text_field($_GET['theme'])])) {
                $theme = sanitize_text_field($_GET['theme']);
            } else {
                $theme = $options['theme'];
            }

            self::wp_kses_wf(self::get_template($theme));
            die();
        }
    } // display_construction_page


    // keeping compatibility with WP < v4.4
    static function wp_get_server_protocol()
    {
        $protocol = $_SERVER['SERVER_PROTOCOL'];
        if (!in_array($protocol, array('HTTP/1.1', 'HTTP/2', 'HTTP/2.0'))) {
            $protocol = 'HTTP/1.0';
        }

        return $protocol;
    } // wp_get_server_protocol


    // disables feed if necessary
    static function disable_feed()
    {
        if (true == self::is_construction_mode_enabled(false)) {
            echo '<?xml version="1.0" encoding="UTF-8" ?><status>Service unavailable.</status>';
            exit;
        }
    } // disable_feed


    // enqueue CSS and JS scripts in admin
    static function admin_enqueue_scripts($hook)
    {
        $surveys = get_option(UCP_SURVEYS_KEY);
        $meta = self::get_meta();
        $pointers = get_option(UCP_POINTERS_KEY);

        // auto remove welcome pointer when options are opened
        if (self::is_plugin_page()) {
            unset($pointers['welcome']);
            update_option(UCP_POINTERS_KEY, $pointers);
        }

        // survey is shown min 5min after install
        // DISABLED
        if (0 && empty($surveys['usage']) && time() - $meta['first_install'] > 300) {
            $open_survey = true;
        } else {
            $open_survey = false;
        }

        $countdown = 0;

        $js_localize = array(
            'undocumented_error' => esc_attr__('An undocumented error has occured. Please refresh the page and try again.', 'wpdevhelper'),
            'plugin_name' => esc_attr__('UnderConstructionPage', 'wpdevhelper'),
            'settings_url' => admin_url('options-general.php?page=ucp'),
            'whitelisted_users_placeholder' => esc_attr__('Select whitelisted user(s)', 'wpdevhelper'),
            'open_survey' => $open_survey,
            'is_activated' => true,           
            'nonce_dismiss_survey' => wp_create_nonce('ucp_dismiss_survey'),
            'nonce_submit_survey' => wp_create_nonce('ucp_submit_survey'),
            'nonce_submit_support_message' => wp_create_nonce('ucp_submit_support_message'),
            'deactivate_confirmation' => esc_attr__('Are you sure you want to deactivate UnderConstruction plugin?' . "\n" . 'If you are removing it because of a problem please contact our support. They will be more than happy to help.', 'wpdevhelper')
        );

        if (self::is_plugin_page()) {
            remove_editor_styles();
            wp_enqueue_style('wp-jquery-ui-dialog');
            wp_enqueue_style('ucp-select2', UCP_PLUGIN_URL . 'css/select2.min.css', array(), self::$version);
            wp_enqueue_style('ucp-admin', UCP_PLUGIN_URL . 'css/ucp-admin.css', array(), self::$version);

            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-dialog');
            wp_enqueue_script('ucp-jquery-plugins', UCP_PLUGIN_URL . 'js/ucp-jquery-plugins.js', array('jquery'), self::$version, true);
            wp_enqueue_script('ucp-select2', UCP_PLUGIN_URL . 'js/select2.min.js', array(), self::$version, true);
            wp_enqueue_script('ucp-admin', UCP_PLUGIN_URL . 'js/ucp-admin.js', array('jquery'), self::$version, true);
            wp_localize_script('ucp-admin', 'ucp', $js_localize);

            // fix for agressive plugins
            wp_dequeue_style('uiStyleSheet');
            wp_dequeue_style('wpcufpnAdmin');
            wp_dequeue_style('unifStyleSheet');
            wp_dequeue_style('wpcufpn_codemirror');
            wp_dequeue_style('wpcufpn_codemirrorTheme');
            wp_dequeue_style('collapse-admin-css');
            wp_dequeue_style('jquery-ui-css');
            wp_dequeue_style('tribe-common-admin');
            wp_dequeue_style('file-manager__jquery-ui-css');
            wp_dequeue_style('file-manager__jquery-ui-css-theme');
            wp_dequeue_style('wpmegmaps-jqueryui');
            wp_dequeue_style('wp-botwatch-css');
        }

        if ($pointers) {
            $pointers['_nonce_dismiss_pointer'] = wp_create_nonce('ucp_dismiss_pointer');
            wp_enqueue_script('wp-pointer');
            wp_enqueue_script('ucp-pointers', plugins_url('js/ucp-admin-pointers.js', __FILE__), array('jquery'), self::$version, true);
            wp_enqueue_style('wp-pointer');
            wp_localize_script('wp-pointer', 'ucp_pointers', $pointers);
            wp_localize_script('wp-pointer', 'ucp', $js_localize);
        }
    } // admin_enqueue_scripts


    // permanently dismiss a pointer
    static function dismiss_pointer_ajax()
    {
        check_ajax_referer('ucp_dismiss_pointer');

        $pointers = get_option(UCP_POINTERS_KEY);
        $pointer = trim(sanitize_text_field($_POST['pointer']));

        if (empty($pointers) || empty($pointers[$pointer])) {
            wp_send_json_error();
        }

        unset($pointers[$pointer]);
        update_option(UCP_POINTERS_KEY, $pointers);

        wp_send_json_success();
    } // dismiss_pointer_ajax


    // permanently dismiss a survey
    static function dismiss_survey_ajax()
    {
        check_ajax_referer('ucp_dismiss_survey');

        $surveys = get_option(UCP_SURVEYS_KEY, array());
        $survey = trim(sanitize_text_field($_POST['survey']));

        $surveys[$survey] = -1;
        update_option(UCP_SURVEYS_KEY, $surveys);

        wp_send_json_success();
    } // dismiss_survey_ajax

    // submit survey
    static function submit_survey_ajax()
    {
        check_ajax_referer('ucp_submit_survey');

        $options = self::get_options();
        $meta = self::get_meta();
        $surveys = get_option(UCP_SURVEYS_KEY);

        $vars = wp_parse_args($_POST, array('survey' => '', 'answers' => '', 'custom_answer' => $options['theme'], 'emailme' => ''));
        $vars['answers'] = trim($vars['answers'], ',');
        $vars['custom_answer'] = trim(strip_tags($vars['custom_answer']));

        $vars['custom_answer'] .= '; ' . date('Y-m-d H:i:s', $meta['first_install']);
        $vars['custom_answer'] = trim($vars['custom_answer'], ' ;');

        if (empty($vars['survey']) || empty($vars['answers'])) {
            wp_send_json_error();
        }

        $request_params = array('sslverify' => false, 'timeout' => 15, 'redirection' => 2);
        $request_args = array(
            'action' => 'submit_survey',
            'survey' => $vars['survey'],
            'email' => $vars['emailme'],
            'answers' => $vars['answers'],
            'custom_answer' => $vars['custom_answer'],
            'first_version' => $meta['first_version'],
            'version' => UCP::$version,
            'codebase' => 'free',
            'site' => get_home_url()
        );

        $url = add_query_arg($request_args, self::$licensing_servers[0]);
        $response = wp_remote_get(esc_url_raw($url), $request_params);

        if (is_wp_error($response) || !wp_remote_retrieve_body($response)) {
            $url = add_query_arg($request_args, self::$licensing_servers[1]);
            $response = wp_remote_get(esc_url_raw($url), $request_params);
        }

        $surveys[$vars['survey']] = time();
        update_option(UCP_SURVEYS_KEY, $surveys);

        wp_send_json_success();
    } // submit_survey_ajax


    // encode email for frontend use
    static function encode_email($email)
    {
        $len = strlen($email);
        $out = '';

        for ($i = 0; $i < $len; $i++) {
            $out .= '&#' . ord($email[$i]) . ';';
        }

        return $out;
    } // encode_email


    // parse shortcode alike variables
    static function parse_vars($string)
    {
        $org_string = $string;

        $vars = array(
            'site-title' => get_bloginfo('name'),
            'site-tagline' => get_bloginfo('description'),
            'site-description' => get_bloginfo('description'),
            'site-url' => trailingslashit(get_home_url()),
            'wp-url' => trailingslashit(get_site_url()),
            'site-login-url' => get_site_url() . '/wp-login.php'
        );

        foreach ($vars as $var_name => $var_value) {
            $var_name = '[' . $var_name . ']';
            $string = str_ireplace($var_name, $var_value, $string);
        }

        $string = apply_filters('ucp_parse_vars', $string, $org_string, $vars);

        return $string;
    } // parse_vars


    // generate HTML from social icons
    static function generate_social_icons($options, $template_id)
    {
        $out = '';

        if (!empty($options['social_facebook'])) {
            $out .= '<a title="Facebook" href="' . esc_attr($options['social_facebook']) . '" target="_blank"><i class="fa fa-facebook-square fa-3x"></i></a>';
        }
        if (!empty($options['social_twitter'])) {
            $out .= '<a title="Twitter" href="' . esc_attr($options['social_twitter']) . '" target="_blank"><i class="fa fa-twitter-square fa-3x"></i></a>';
        }
        if (!empty($options['social_linkedin'])) {
            $out .= '<a title="LinkedIn" href="' . esc_attr($options['social_linkedin']) . '" target="_blank"><i class="fa fa-linkedin-square fa-3x"></i></a>';
        }
        if (!empty($options['social_youtube'])) {
            $out .= '<a title="YouTube" href="' . esc_attr($options['social_youtube']) . '" target="_blank"><i class="fa fa-youtube-square fa-3x"></i></a>';
        }
        if (!empty($options['social_vimeo'])) {
            $out .= '<a title="Vimeo" href="' . esc_attr($options['social_vimeo']) . '" target="_blank"><i class="fa fa-vimeo-square fa-3x"></i></a>';
        }
        if (!empty($options['social_pinterest'])) {
            $out .= '<a title="Pinterest" href="' . esc_attr($options['social_pinterest']) . '" target="_blank"><i class="fa fa-pinterest-square fa-3x"></i></a>';
        }
        if (!empty($options['social_dribbble'])) {
            $out .= '<a title="Dribbble" href="' . esc_attr($options['social_dribbble']) . '" target="_blank"><i class="fa fa-dribbble fa-3x"></i></a>';
        }
        if (!empty($options['social_behance'])) {
            $out .= '<a title="Behance" href="' . esc_attr($options['social_behance']) . '" target="_blank"><i class="fa fa-behance-square fa-3x"></i></a>';
        }
        if (!empty($options['social_instagram'])) {
            $out .= '<a title="Instagram" href="' . esc_attr($options['social_instagram']) . '" target="_blank"><i class="fa fa-instagram fa-3x"></i></a>';
        }
        if (!empty($options['social_tumblr'])) {
            $out .= '<a title="Tumblr" href="' . esc_attr($options['social_tumblr']) . '" target="_blank"><i class="fa fa-tumblr-square fa-3x"></i></a>';
        }
        if (!empty($options['social_vk'])) {
            $out .= '<a title="VK" href="' . esc_attr($options['social_vk']) . '" target="_blank"><i class="fa fa-vk fa-3x"></i></a>';
        }
        if (!empty($options['social_skype'])) {
            $out .= '<a title="Skype" href="skype:' . esc_attr($options['social_skype']) . '?chat"><i class="fa fa-skype fa-3x"></i></a>';
        }
        if (!empty($options['social_whatsapp'])) {
            $out .= '<a title="WhatsApp" href="https://api.whatsapp.com/send?phone=' . str_replace('+', '', esc_attr($options['social_whatsapp'])) . '"><i class="fa fa-whatsapp fa-3x"></i></a>';
        }
        if (!empty($options['social_telegram'])) {
            $out .= '<a title="Telegram" href="' . esc_attr($options['social_telegram']) . '"><i class="fa fa-telegram fa-3x"></i></a>';
        }
        if (!empty($options['social_email'])) {
            $out .= '<a title="Email" href="mailto:' . esc_attr(self::encode_email($options['social_email'])) . '"><i class="fa fa-envelope fa-3x"></i></a>';
        }
        if (!empty($options['social_phone'])) {
            $out .= '<a title="Phone" href="tel:' . esc_attr($options['social_phone']) . '"><i class="fa fa-phone-square fa-3x"></i></a>';
        }

        return $out;
    } // generate_social_icons


    // shortcode for inserting things in header
    static function generate_head($options, $template_id)
    {
        $out = '';

        $out .= '<link rel="stylesheet" href="' . trailingslashit(UCP_PLUGIN_URL . 'themes/css') . 'bootstrap.min.css?v=' . self::$version . '" type="text/css">' . "\n";
        $out .= '<link rel="stylesheet" href="' . trailingslashit(UCP_PLUGIN_URL . 'themes/css') . 'common.css?v=' . self::$version . '" type="text/css">' . "\n";
        $out .= '<link rel="stylesheet" href="' . trailingslashit(UCP_PLUGIN_URL . 'themes/' . $template_id) . 'style.css?v=' . self::$version . '" type="text/css">' . "\n";
        $out .= '<link rel="stylesheet" href="' . trailingslashit(UCP_PLUGIN_URL . 'themes/css') . 'font-awesome.min.css?v=' . self::$version . '" type="text/css">' . "\n";

        $out .= '<link rel="icon" sizes="128x128" href="' . trailingslashit(UCP_PLUGIN_URL . 'themes/images') . 'favicon.png" />';

        if (!empty($options['ga_tracking_id'])) {
            $out .= "
      <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
        ga('create', '{$options['ga_tracking_id']}', 'auto');
        ga('send', 'pageview');
      </script>";
        }

        if (!empty($options['custom_css'])) {
            $out .= "\n" . '<style type="text/css">' . $options['custom_css'] . '</style>';
        }

        $out = apply_filters('ucp_head', $out, $options, $template_id);

        return trim($out);
    } // generate_head


    // shortcode for inserting things in footer
    static function generate_footer($options, $template_id)
    {
        $out = '';

        if ($options['linkback'] == '1') {
            $out .= '<p class="text-center">Desenvolvido por <a href="https://inovany.com.br" target="_blank" rel="noopener" title="iNova">
                        <img src="https://assets.comet.com.br/assets/default/logo-inova-dark.png" alt="Inova" height="24">
                    </a>
                    <a href="https://bluelizard.com.br" target="_blank" rel="noopener" title="Blue Lizard">
                        <img src="https://assets.comet.com.br/assets/default/logo-bluelizard-default.png" alt="Blue Lizard" height="24">
                    </a>
                </p>';
        }

        if ($options['login_button'] == '1') {
            if (is_user_logged_in()) {
                $out .= '<div id="login-button" class="loggedin">';
                $out .= '<a title="' . esc_attr__('Open WordPress admin', 'wpdevhelper') . '" href="' . get_site_url() . '/wp-admin/"><i class="fa fa-wordpress fa-2x" aria-hidden="true"></i></a>';
            } else {
                $out .= '<div id="login-button" class="loggedout">';
                $out .= '<a title="' . esc_attr__('Log in to WordPress admin', 'wpdevhelper') . '" href="' . get_site_url() . '/wp-login.php"><i class="fa fa-wordpress fa-2x" aria-hidden="true"></i></a>';
            }
            $out .= '</div>';
        }

        $out = apply_filters('ucp_footer', $out, $options, $template_id);

        return $out;
    } // generate_footer


    // returnes parsed template
    static function get_template($template_id)
    {
        $vars = array();
        $options = self::get_options();

        $vars['version'] = self::$version;
        $vars['site-url'] = trailingslashit(get_home_url());
        $vars['wp-url'] = trailingslashit(get_site_url());
        $vars['theme-url'] = trailingslashit(UCP_PLUGIN_URL . 'themes/' . $template_id);
        $vars['theme-url-common'] = trailingslashit(UCP_PLUGIN_URL . 'themes');
        $vars['title'] = self::parse_vars($options['title']);
        $vars['generator'] = esc_attr__('Free UnderConstructionPage plugin for WordPress', 'wpdevhelper');
        $vars['heading1'] = self::parse_vars($options['heading1']);
        $vars['content'] = nl2br(self::parse_vars($options['content']));
        $vars['description'] = self::parse_vars($options['description']);
        $vars['social-icons'] = self::generate_social_icons($options, $template_id);
        $vars['head'] = self::generate_head($options, $template_id);
        $vars['footer'] = self::generate_footer($options, $template_id);

        $vars = apply_filters('ucp_get_template_vars', $vars, $template_id, $options);

        ob_start();
        require UCP_PLUGIN_DIR . 'themes/' . $template_id . '/index.php';
        $template = ob_get_clean();

        foreach ($vars as $var_name => $var_value) {
            $var_name = '[' . $var_name . ']';
            $template = str_ireplace($var_name, $var_value, $template);
        }

        $template = apply_filters('ucp_get_template', $template, $vars, $options);

        return $template;
    } // get_template


    // checks if construction mode is enabled for the current visitor
    static function is_construction_mode_enabled($settings_only = false)
    {
        $options = self::get_options();
        $current_user = wp_get_current_user();

        $override_status = apply_filters('ucp_is_construction_mode_enabled', null, $options);
        if (is_bool($override_status)) {
            return $override_status;
        }

        // just check if it's generally enabled
        if ($settings_only) {
            if ($options['status']) {
                return true;
            } else {
                return false;
            }
        } else {
            // check if enabled for current user
            if (!$options['status']) {
                return false;
            } elseif (self::user_has_role($options['whitelisted_roles'])) {
                return false;
            } elseif (in_array($current_user->ID, $options['whitelisted_users'])) {
                return false;
            } elseif (strlen($options['end_date']) === 16 && $options['end_date'] !== '0000-00-00 00:00' && $options['end_date'] < current_time('mysql')) {
                return false;
            } else {
                return true;
            }
        }
    } // is_construction_mode_enabled


    // check if user has the specified role
    static function user_has_role($roles)
    {
        $current_user = wp_get_current_user();

        if ($current_user->roles) {
            $user_role = $current_user->roles[0];
        } else {
            $user_role = 'guest';
        }

        return in_array($user_role, $roles);
    } // user_has_role


    // frontend notification when UCP is enabled but current user is whitelisted
    static function whitelisted_notice()
    {
        $notices = get_option(UCP_NOTICES_KEY);
        $dismiss_url = add_query_arg(array('action' => 'ucp_dismiss_notice', 'notice' => 'whitelisted', 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php'));

        if (
            empty($notices['dismiss_whitelisted']) &&
            is_user_logged_in() &&
            self::is_construction_mode_enabled(true) &&
            !self::is_construction_mode_enabled(false)
        )
            // keeping everything inline due to minimal CSS
            echo '<div style="background-color: #333; line-height: 140%; font-size: 14px; position: fixed; display: block; top: 50px; z-index: 99999; color: #fefefe; padding: 20px 35px 20px 20px; width: 500px; border: thin solid #fefefe; left: -1px;"><a style="color: #ea1919; font-weight: 900; text-decoration: none; position: absolute; top: 7px; right: 10px;" href="' . esc_url($dismiss_url) . '" alt="Dismiss notice" onclick="window.location.href = \'' . esc_url($dismiss_url) . '\'; return false;" title="Dismiss notice">X</a><b>' . esc_attr__('<b>Under Construction Mode is enabled</b> but you are whitelisted so you see the normal site.', 'wpdevhelper') . '<br><a href="' . esc_url(get_home_url()) . '/?ucp_preview" style="text-decoration: underline; color: #fefefe;">' . esc_attr__('Preview UnderConstructionPage', 'wpdevhelper') . '</a><br><a href="' . esc_url(admin_url('options-general.php?page=ucp')) . '" style="text-decoration: underline; color: #fefefe;">' . esc_attr__('Configure UnderConstructionPage', 'wpdevhelper') . '</a></div>';
    } // whitelisted_notification


    // displays various notices in admin header
    static function admin_notices()
    {
        $notices = get_option(UCP_NOTICES_KEY);
        $options = self::get_options();
        $meta = self::get_meta();
        $current_user = wp_get_current_user();
        $shown = false;

        $name = '';
        if (!empty($current_user->user_firstname)) {
            $name = ' ' . $current_user->user_firstname;
        }

        // end date in past
        if (self::is_plugin_page() && self::is_construction_mode_enabled(true) && !empty($options['end_date']) && $options['end_date'] != '0000-00-00 00:00' && $options['end_date'] < current_time('mysql')) {
            echo '<div id="ucp_end_date_notice" class="notice-error notice"><p>Under construction mode is enabled but the <a href="#end_date" class="change_tab" data-tab="0">end date</a> is set to a past date so the <b>under construction page will not be shown</b>. Either move the <a href="#end_date" class="change_tab" data-tab="0">end date</a> to a future date or disable it.</p></div>';
            $shown = true;
        }
    } // notices


    // handle dismiss button for notices
    static function dismiss_notice()
    {
        if (empty($_GET['notice'])) {
            wp_safe_redirect(admin_url());
            exit;
        }

        $notices = get_option(UCP_NOTICES_KEY, array());
        $notice = sanitize_text_field($_GET['notice']);

        if ($notice == 'translate') {
            $notices['dismiss_translate'] = true;
        } elseif ($notice == 'whitelisted') {
            $notices['dismiss_whitelisted'] = true;
        } else {
            wp_safe_redirect(admin_url());
            exit;
        }

        update_option(UCP_NOTICES_KEY, $notices);

        if (!empty($_GET['redirect'])) {
            wp_safe_redirect($_GET['redirect']);
        } else {
            wp_safe_redirect(admin_url());
        }

        exit;
    } // dismiss_notice


    // reset all settings to default values
    static function reset_settings()
    {
        check_admin_referer('ucp_reset_settings');

        if (false === current_user_can('administrator')) {
            wp_safe_redirect(admin_url());
            exit;
        }

        $options = self::default_options();
        update_option(UCP_OPTIONS_KEY, $options);

        if (!empty($_GET['redirect'])) {
            wp_safe_redirect($_GET['redirect']);
        } else {
            wp_safe_redirect(admin_url());
        }

        exit;
    } // reset_settings


    // change status via admin bar
    static function change_status()
    {
        check_admin_referer('ucp_change_status');

        if (false === current_user_can('administrator') || empty($_GET['new_status'])) {
            wp_safe_redirect(admin_url());
            exit;
        }

        $options = self::get_options();

        if (sanitize_text_field($_GET['new_status']) == 'enabled') {
            $options['status'] = '1';
        } else {
            $options['status'] = '0';
        }

        update_option(UCP_OPTIONS_KEY, $options);

        if (!empty($_GET['redirect'])) {
            wp_safe_redirect($_GET['redirect']);
        } else {
            wp_safe_redirect(admin_url());
        }

        exit;
    } // change_status


    static function admin_bar_style()
    {
        // admin bar has to be anabled, user an admin and custom filter true
        if (false === is_admin_bar_showing() || false === current_user_can('administrator') || false === apply_filters('ucp_show_admin_bar', true)) {
            return;
        }

        // no sense in loading a new CSS file for 2 lines of CSS
        echo '<style type="text/css">';
        $custom_css = '#wpadminbar ul li#wp-admin-bar-ucp-info { padding: 5px 0; } #wpadminbar ul li#wp-admin-bar-ucp-settings, #wpadminbar ul li#wp-admin-bar-ucp-status { padding-bottom: 2px; } #wpadminbar i.ucp-status-dot { font-size: 17px; margin-top: -7px; color: #02ca02; height: 17px; display: inline-block; } #wpadminbar i.ucp-status-dot-enabled { color: #87c826; } #wpadminbar i.ucp-status-dot-disabled { color: #ea1919; } #wpadminbar #ucp-status-wrapper { display: inline; border: 1px solid rgba(240,245,250,.7); padding: 0; margin: 0 0 0 5px; background: rgb(35, 40, 45); } #wpadminbar .ucp-status-btn { padding: 0 7px; color: #fff; } #wpadminbar #ucp-status-wrapper.off #ucp-status-off { background: #ea1919;} #wpadminbar #ucp-status-wrapper.on #ucp-status-on { background: #66b317; }#wp-admin-bar-under-construction-page img.logo { height: 17px; margin-bottom: 4px; padding-right: 3px; } body.wp-admin #wp-admin-bar-under-construction-page img.logo { margin-bottom: -4px; }';
        self::wp_kses_wf($custom_css);
        echo '</style>';
    } // admin_bar_style


    // add admin bar menu and status
    static function admin_bar()
    {
        global $wp_admin_bar;

        // only show to admins
        if (false === current_user_can('administrator') || false === apply_filters('ucp_show_admin_bar', true)) {
            return;
        }

        if (self::is_construction_mode_enabled(true)) {
            $main_label = '<img style="height: 17px; margin-bottom: -4px; padding-right: 3px;" src="' . UCP_PLUGIN_URL . 'images/ucp_icon.png" alt="' . esc_attr__('Under construction mode is enabled', 'wpdevhelper') . '" title="' . esc_attr__('Under construction mode is enabled', 'wpdevhelper') . '"> <span class="ab-label">' . esc_attr__('UnderConstruction', 'wpdevhelper') . ' <i class="ucp-status-dot ucp-status-dot-enabled">&#9679;</i></span>';
            $class = 'ucp-enabled';
            $action_url = add_query_arg(array('action' => 'ucp_change_status', 'new_status' => 'disabled', 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php'));
            $action_url = wp_nonce_url($action_url, 'ucp_change_status');
            $action = esc_attr__('Under Construction Mode', 'wpdevhelper');
            $action .= '<a href="' . $action_url . '" id="ucp-status-wrapper" class="on"><span id="ucp-status-off" class="ucp-status-btn">OFF</span><span id="ucp-status-on" class="ucp-status-btn">ON</span></a>';
        } else {
            $main_label = '<img style="height: 17px; margin-bottom: -4px; padding-right: 3px;" src="' . UCP_PLUGIN_URL . 'images/ucp_icon.png" alt="' . esc_attr__('Under construction mode is disabled', 'wpdevhelper') . '" title="' . esc_attr__('Under construction mode is disabled', 'wpdevhelper') . '"> <span class="ab-label">' . esc_attr__('UnderConstruction', 'wpdevhelper') . ' <i class="ucp-status-dot ucp-status-dot-disabled">&#9679;</i></span>';
            $class = 'ucp-disabled';
            $action_url = add_query_arg(array('action' => 'ucp_change_status', 'new_status' => 'enabled', 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php'));
            $action_url = wp_nonce_url($action_url, 'ucp_change_status');
            $action = esc_attr__('Under Construction Mode', 'wpdevhelper');
            $action .= '<a href="' . $action_url . '" id="ucp-status-wrapper" class="off"><span id="ucp-status-off" class="ucp-status-btn">OFF</span><span id="ucp-status-on" class="ucp-status-btn">ON</span></a>';
        }

        $wp_admin_bar->add_menu(array(
            'parent' => '',
            'id'     => 'under-construction-page',
            'title'  => $main_label,
            'href'   => admin_url('options-general.php?page=ucp'),
            'meta'   => array('class' => $class)
        ));
        $wp_admin_bar->add_node(array(
            'id'    => 'ucp-status',
            'title' => $action,
            'href'  => false,
            'parent' => 'under-construction-page'
        ));
        $wp_admin_bar->add_node(array(
            'id'     => 'ucp-preview',
            'title'  => esc_attr__('Preview', 'wpdevhelper'),
            'meta'   => array('target' => 'blank'),
            'href'   => get_home_url() . '/?ucp_preview',
            'parent' => 'under-construction-page'
        ));
        $wp_admin_bar->add_node(array(
            'id'     => 'ucp-settings',
            'title'  => esc_attr__('Settings', 'wpdevhelper'),
            'href'   => admin_url('options-general.php?page=ucp'),
            'parent' => 'under-construction-page'
        ));
    } // admin_bar


    // show under construction notice on WP login form
    static function login_message($message)
    {
        if (self::is_construction_mode_enabled(true)) {
            $message .= '<div class="message">' . esc_attr__('Under Construction Mode is <b>enabled</b>.', 'wpdevhelper') . '</div>';
        }

        return $message;
    } // login_notice

    
    // additional powered by text in admin footer; only on UCP page
    static function admin_footer_text($text)
    {
        if (!self::is_plugin_page()) {
            return $text;
        }

        $text = '<i>' . esc_attr__('UnderConstructionPage', 'wpdevhelper') . ' v' . self::$version . '. Esta é uma versão por WP Dev Helper</i>';

        return $text;
    } // admin_footer_text


    // fix for opening the plugin install modal
    static function admin_footer()
    {
        if (empty($_GET['fix-install-button']) || empty($_GET['tab']) || sanitize_text_field($_GET['tab']) != 'plugin-information') {
            return;
        }

        echo '<script>';
        echo "jQuery('#plugin_install_from_iframe').on('click', function() { window.location.href = jQuery(this).attr('href'); return false;});";
        echo '</script>';
    } // admin_footer


    // test if we're on plugin's page
    static function is_plugin_page()
    {
        $current_screen = get_current_screen();

        if ($current_screen->id == 'settings_page_ucp') {
            return true;
        } else {
            return false;
        }
    } // is_plugin_page


    // create the admin menu item
    static function admin_menu()
    {
        add_options_page(esc_attr__('UnderConstruction', 'wpdevhelper'), esc_attr__('UnderConstruction', 'wpdevhelper'), 'manage_options', 'ucp', array(__CLASS__, 'main_page'));
    } // admin_menu


    // all settings are saved in one option
    static function register_settings()
    {
        register_setting(UCP_OPTIONS_KEY, UCP_OPTIONS_KEY, array(__CLASS__, 'sanitize_settings'));
    } // register_settings


    // set default settings
    static function default_options()
    {
        $defaults = array(
            'status' => '0',
            'end_date' => '',
            'ga_tracking_id' => '',
            'theme' => 'mad_designer',
            'custom_css' => '',
            'title' => '[site-title] is under construction',
            'description' => '[site-tagline]',
            'heading1' => esc_attr__('Sorry, we\'re doing some work on the site', 'wpdevhelper'),
            'content' => esc_attr__('Thank you for being patient. We are doing some work on the site and will be back shortly.', 'wpdevhelper'),
            'social_facebook' => '',
            'social_twitter' => '',
            'social_linkedin' => '',
            'social_youtube' => '',
            'social_vimeo' => '',
            'social_pinterest' => '',
            'social_dribbble' => '',
            'social_behance' => '',
            'social_instagram' => '',
            'social_tumblr' => '',
            'social_vk' => '',
            'social_email' => '',
            'social_phone' => '',
            'social_skype' => '',
            'social_telegram' => '',
            'social_whatsapp' => '',
            'login_button' => '1',
            'linkback' => '0',
            'whitelisted_roles' => array('administrator'),
            'whitelisted_users' => array()
        );

        return $defaults;
    } // default_options


    // sanitize settings on save
    static function sanitize_settings($options)
    {
        $old_options = self::get_options();

        foreach ($options as $key => $value) {
            switch ($key) {
                case 'title':
                case 'description':
                    $options[$key] = trim(strip_tags($value));
                    break;
                case 'heading1':
                case 'content':
                    $options[$key] = trim(wp_kses($value, wp_kses_allowed_html('post')));
                    break;
                case 'custom_css':
                case 'social_facebook':
                case 'social_twitter':
                case 'social_linkedin':
                case 'social_youtube':
                case 'social_vimeo':
                case 'social_pinterest':
                case 'social_dribbble':
                case 'social_behance':
                case 'social_instagram':
                case 'social_tumblr':
                case 'social_vk':
                case 'social_email':
                case 'social_phone':
                case 'social_telegram':
                case 'social_whatsapp':
                case 'ga_tracking_id':
                    $options[$key] = substr(strtoupper(trim($value)), 0, 15);
                    break;
                case 'end_date':
                    $options[$key] = substr(trim($value), 0, 16);
                    break;
            } // switch
        } // foreach

        $options['title'] = strip_tags($options['title']);
        $options['description'] = strip_tags($options['description']);
        $options['heading1'] = strip_tags($options['heading1'], '<br><a><b><strong><i><em><p><del><img>');
        $options['content'] = strip_tags($options['content'], '<br><a><b><strong><i><em><p><del><img><ul><ol><li><blockquote><ins><code><hr><h2><h3><h4><span><div><iframe>');

        $options['whitelisted_roles'] = empty($options['whitelisted_roles']) ? array() : $options['whitelisted_roles'];
        $options['whitelisted_users'] = empty($options['whitelisted_users']) ? array() : $options['whitelisted_users'];
        $options = self::check_var_isset($options, array('status' => 0, 'linkback' => 0, 'login_button' => 0));

        if (empty($options['end_date_toggle'])) {
            $options['end_date'] = '';
        }
        if ($options['end_date'] == '0000-00-00 00:00') {
            $options['end_date'] = '';
        }
        unset($options['end_date_toggle']);

        if (empty($options['ga_tracking_toggle'])) {
            $options['ga_tracking_id'] = '';
        }
        if (!empty($options['ga_tracking_id']) && preg_match('/^UA-\d{3,}-\d{1,3}$/', $options['ga_tracking_id']) === 0) {
            add_settings_error('ucp', 'ga_tracking_id', esc_attr__('Please enter a valid Google Analytics Tracking ID or disable tracking.', 'wpdevhelper'));
        }
        unset($options['ga_tracking_toggle']);

        // empty cache in 3rd party plugins
        if ($options != $old_options) {
            $notices = get_option(UCP_NOTICES_KEY);
            unset($notices['dismiss_whitelisted']);
            update_option(UCP_NOTICES_KEY, $notices);
            self::empty_cache();
        }

        return array_merge($old_options, $options);
    } // sanitize_settings


    static function empty_cache()
    {
        wp_cache_flush();
        if (function_exists('w3tc_flush_all')) {
            w3tc_flush_all();
        }
        if (function_exists('wp_cache_clear_cache')) {
            wp_cache_clear_cache();
        }
        if (method_exists('LiteSpeed_Cache_API', 'purge_all')) {
            LiteSpeed_Cache_API::purge_all();
        }
        if (class_exists('Endurance_Page_Cache')) {
            $epc = new Endurance_Page_Cache;
            $epc->purge_all();
        }
        if (class_exists('SG_CachePress_Supercacher') && method_exists('SG_CachePress_Supercacher', 'purge_cache')) {
            SG_CachePress_Supercacher::purge_cache(true);
        }
        if (class_exists('SiteGround_Optimizer\Supercacher\Supercacher')) {
            SiteGround_Optimizer\Supercacher\Supercacher::purge_cache();
        }
        if (isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')) {
            $GLOBALS['wp_fastest_cache']->deleteCache(true);
        }
        if (is_callable(array('Swift_Performance_Cache', 'clear_all_cache'))) {
            Swift_Performance_Cache::clear_all_cache();
        }
        if (is_callable(array('Hummingbird\WP_Hummingbird', 'flush_cache'))) {
            Hummingbird\WP_Hummingbird::flush_cache(true, false);
        }
        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
        }
        do_action('cache_enabler_clear_complete_cache');
    } // empty_cache


    // checkbox helper function
    static function checked($value, $current, $echo = false)
    {
        $out = '';

        if (!is_array($current)) {
            $current = (array) $current;
        }

        if (in_array($value, $current)) {
            $out = ' checked="checked" ';
        }

        if ($echo) {
            self::wp_kses_wf($out);
        } else {
            return $out;
        }
    } // checked


    // helper function for saving options, mostly checkboxes
    static function check_var_isset($values, $variables)
    {
        foreach ($variables as $key => $value) {
            if (!isset($values[$key])) {
                $values[$key] = $value;
            }
        }

        return $values;
    } // check_var_isset


    // helper function for creating dropdowns
    static function create_select_options($options, $selected = null, $output = true)
    {
        $out = "\n";

        if (!is_array($selected)) {
            $selected = array($selected);
        }

        foreach ($options as $tmp) {
            $data = '';
            if (isset($tmp['disabled'])) {
                $data .= ' disabled="disabled" ';
            }
            if (in_array($tmp['val'], $selected)) {
                $out .= "<option selected=\"selected\" value=\"{$tmp['val']}\"{$data}>{$tmp['label']}&nbsp;</option>\n";
            } else {
                $out .= "<option value=\"{$tmp['val']}\"{$data}>{$tmp['label']}&nbsp;</option>\n";
            }
        } // foreach

        if ($output) {
            UCP::wp_kses_wf($out);
        } else {
            return $out;
        }
    } // create_select_options


    // helper function to generate tagged buy links
    static function generate_web_link($placement = '', $page = '/', $params = array(), $anchor = '')
    {
        $base_url = 'https://underconstructionpage.com';

        if ('/' != $page) {
            $page = '/' . trim($page, '/') . '/';
        }
        if ($page == '//') {
            $page = '/';
        }

        if ($placement) {
            $placement = trim($placement, '-');
            $placement = '-' . $placement;
        }

        $parts = array_merge(array('ref' => 'ucp-free' . $placement), $params);

        if (!empty($anchor)) {
            $anchor = '#' . trim($anchor, '#');
        }

        $out = $base_url . $page . '?' . http_build_query($parts, '', '&amp;') . $anchor;

        return $out;
    } // generate_web_link


    // first, main tab content
    static function tab_main()
    {
        $options = self::get_options();
        $default_options = self::default_options();

        echo '<div class="ucp-tab-content">';
        echo '<table class="form-table">';

        echo '<tr valign="top">
    <th scope="row"><label for="status">' . esc_attr__('Under Construction Mode', 'wpdevhelper') . '</label></th>
    <td>';

        echo '<div class="toggle-wrapper" id="main-status">
      <input type="checkbox" id="status" ' . esc_attr(self::checked(1, $options['status'])) . ' type="checkbox" value="1" name="' . esc_attr(UCP_OPTIONS_KEY) . '[status]">
      <label for="status" class="toggle"><span class="toggle_handler"></span></label>
    </div>';

        echo '<p class="description">' . __('By enabling construction mode users will not be able to access the site\'s content. They will only see the under construction page. To configure exceptions set <a class="change_tab" data-tab="3" href="#whitelisted-roles">whitelisted user roles</a>.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top">
    <th scope="row"><label for="end_date_toggle">' . esc_attr__('Automatic End Date &amp; Time', 'wpdevhelper') . '</label></th>
    <td>';
        echo '<div class="toggle-wrapper">
      <input type="checkbox" id="end_date_toggle" ' . esc_attr(self::checked(1, (empty($options['end_date']) || $options['end_date'] == '0000-00-00 00:00') ? 0 : 1)) . ' type="checkbox" value="1" name="' . esc_attr(UCP_OPTIONS_KEY) . '[end_date_toggle]">
      <label for="end_date_toggle" class="toggle"><span class="toggle_handler"></span></label>
    </div>';
        echo '<div id="end_date_wrapper"><input id="end_date" type="text" class="datepicker" name="' . esc_attr(UCP_OPTIONS_KEY) . '[end_date]" value="' . esc_attr($options['end_date']) . '" placeholder="yyyy-mm-dd hh:mm"><span title="' . esc_attr__('Open date & time picker', 'wpdevhelper') . '" alt="' . esc_attr__('Open date & time picker', 'wpdevhelper') . '" class="show-datepicker dashicons dashicons-calendar-alt"></span>';
        echo '<p class="description">' . esc_attr__('If enabled, construction mode will automatically stop showing on the selected date.
    This option will not "auto-enable" construction mode. Status has to be set to "On".', 'wpdevhelper') . '</p></div>';
        echo '</td></tr>';

        echo '<tr valign="top">
    <th scope="row"><label for="ga_tracking_id_toggle">' . esc_attr__('Google Analytics Tracking', 'wpdevhelper') . '</label></th>
    <td>';
        echo '<div class="toggle-wrapper">
      <input type="checkbox" id="ga_tracking_id_toggle" ' . esc_attr(self::checked(1, empty($options['ga_tracking_id']) ? 0 : 1)) . ' type="checkbox" value="1" name="' . esc_attr(UCP_OPTIONS_KEY) . '[ga_tracking_toggle]">
      <label for="ga_tracking_id_toggle" class="toggle"><span class="toggle_handler"></span></label>
    </div>';
        echo '<div id="ga_tracking_id_wrapper"><input id="ga_tracking_id" type="text" class="code" name="' . esc_attr(UCP_OPTIONS_KEY) . '[ga_tracking_id]" value="' . esc_attr($options['ga_tracking_id']) . '" placeholder="UA-xxxxxx-xx">';
        echo '<p class="description">' . esc_attr__('Enter the unique tracking ID found in your GA tracking profile settings to track visits to pages.', 'wpdevhelper') . '</p></div>';
        echo '</td></tr>';

        $reset_url = add_query_arg(array('action' => 'ucp_reset_settings', 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php'));
        $reset_url = wp_nonce_url($reset_url, 'ucp_reset_settings');
        echo '<tr valign="top">
    <th scope="row"><label for="">' . esc_attr__('Reset Settings', 'wpdevhelper') . '</label></th>
    <td>';
        echo '<a href="' . esc_url($reset_url) . '" class="button button-secondary reset-settings">' . esc_attr__('Reset all settings to default values', 'wpdevhelper') . '</a>';
        echo '<p class="description">' . esc_attr__('By resetting all settings to their default values any customizations you have done will be lost. There is no undo.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '</table>';
        echo '</div>';

        self::footer_buttons();
    } // tab_main


    static function tab_content()
    {
        global $wpdb;
        $options = self::get_options();
        $default_options = self::default_options();

        echo '<div class="ucp-tab-content">';

        echo '<table class="form-table">';

        echo '<tr valign="top">
    <th scope="row"><label for="title">' . esc_attr__('Title', 'wpdevhelper') . '</label></th>
    <td><input type="text" id="title" class="regular-text" name="' . esc_attr(UCP_OPTIONS_KEY) . '[title]" value="' . esc_attr($options['title']) . '" />';
        echo '<p class="description">Page title. Default: ' . esc_attr($default_options['title']) . '</p>';
        echo '<p><b>Available shortcodes:</b> (only active in UC themes, not on the rest of the site)</p>
    <ul class="ucp-list">
    <li><code>[site-title]</code> - blog title, as set in <a href="options-general.php">Options - General</a></li>
    <li><code>[site-tagline]</code> - blog tagline, as set in <a href="options-general.php">Options - General</a></li>
    <li><code>[site-url]</code> - site address (URL), as set in <a href="options-general.php">Options - General</a></li>
    <li><code>[wp-url]</code> - WordPress address (URL), as set in <a href="options-general.php">Options - General</a></li>
    <li><code>[site-login-url]</code> - URL of the default site login page</li>
    </ul>';
        echo '</td></tr>';

        echo '<tr valign="top">
    <th scope="row"><label for="description">' . esc_attr__('Description', 'wpdevhelper') . '</label></th>
    <td><input id="description" type="text" class="large-text" name="' . esc_attr(UCP_OPTIONS_KEY) . '[description]" value="' . esc_attr($options['description']) . '" />';
        echo '<p class="description">Description meta tag (see above for available <a href="#title">shortcodes</a>). Default: ' . esc_attr($default_options['description']) . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top">
    <th scope="row"><label for="heading1">' . esc_attr__('Headline', 'wpdevhelper') . '</label></th>
    <td><input id="heading1" type="text" class="large-text" name="' . esc_attr(UCP_OPTIONS_KEY) . '[heading1]" value="' . esc_attr($options['heading1']) . '" />';
        echo '<p class="description">Main heading/title (see above for available <a href="#title">shortcodes</a>). Default: ' . esc_attr($default_options['heading1']) . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top" id="content_wrap">
    <th scope="row"><label for="content">' . esc_attr__('Content', 'wpdevhelper') . '</label></th>
    <td>';
        wp_editor($options['content'], 'content', array('tabfocus_elements' => 'insert-media-button,save-post', 'editor_height' => 250, 'resize' => 1, 'textarea_name' => esc_attr(UCP_OPTIONS_KEY) . '[content]', 'drag_drop_upload' => 1));
        echo '<p class="description">All HTML elements are allowed. Shortcodes are not parsed except <a href="#title">UC theme ones</a>. Default: ' . esc_attr($default_options['content']) . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top">
    <th scope="row"><label for="linkback">' . esc_attr__('Show Copyright', 'wpdevhelper') . '</label></th>
    <td>';
        echo '<div class="toggle-wrapper">
      <input type="checkbox" id="linkback" ' . esc_attr(self::checked(1, $options['linkback'])) . ' type="checkbox" value="1" name="' . esc_attr(UCP_OPTIONS_KEY) . '[linkback]">
      <label for="linkback" class="toggle"><span class="toggle_handler"></span></label>
    </div>';
        echo '<p class="description">Show copyright in page footer.</p>';
        echo '</td></tr>';


        echo '<tr valign="top" id="login_button_wrap">
    <th scope="row"><label for="login_button">' . esc_attr__('Login Button', 'wpdevhelper') . '</label></th>
    <td>';
        echo '<div class="toggle-wrapper">
      <input type="checkbox" id="login_button" ' . esc_attr(self::checked(1, $options['login_button'])) . ' type="checkbox" value="1" name="' . esc_attr(UCP_OPTIONS_KEY) . '[login_button]">
      <label for="login_button" class="toggle"><span class="toggle_handler"></span></label>
    </div>';
        echo '<p class="description">Show a discrete link to the login form, or WP admin if you\'re logged in, in the lower right corner of the page.</p>';
        echo '</td></tr>';
        echo '</table>';

        self::footer_buttons();

        echo '<h2 class="title">' . esc_attr__('Social &amp; Contact Icons', 'wpdevhelper') . '</h2>';

        echo '<table class="form-table" id="ucp-social-icons">';
        echo '<tr valign="top">
    <th scope="row"><label for="social_facebook">' . esc_attr__('Facebook Page', 'wpdevhelper') . '</label></th>
    <td><input id="social_facebook" type="url" class="regular-text code" name="' . esc_attr(UCP_OPTIONS_KEY) . '[social_facebook]" value="' . esc_attr($options['social_facebook']) . '" placeholder="' . esc_attr__('Facebook business or personal page URL', 'wpdevhelper') . '">';
        echo '<p class="description">' . esc_attr__('Complete URL, with https prefix, to Facebook page.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top">
    <th scope="row"><label for="social_twitter">' . esc_attr__('Twitter Profile', 'wpdevhelper') . '</label></th>
    <td><input id="social_twitter" type="url" class="regular-text code" name="' . esc_attr(UCP_OPTIONS_KEY) . '[social_twitter]" value="' . esc_attr($options['social_twitter']) . '" placeholder="' . esc_attr__('Twitter profile URL', 'wpdevhelper') . '">';
        echo '<p class="description">' . esc_attr__('Complete URL, with https prefix, to Twitter profile page.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top">
    <th scope="row"><label for="social_linkedin">' . esc_attr__('LinkedIn Profile', 'wpdevhelper') . '</label></th>
    <td><input id="social_linkedin" type="url" class="regular-text code" name="' . esc_attr(UCP_OPTIONS_KEY) . '[social_linkedin]" value="' . esc_attr($options['social_linkedin']) . '" placeholder="' . esc_attr__('LinkedIn profile page URL', 'wpdevhelper') . '">';
        echo '<p class="description">' . esc_attr__('Complete URL, with https prefix, to LinkedIn profile page.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top">
    <th scope="row"><label for="social_youtube">' . esc_attr__('YouTube Profile Page or Video', 'wpdevhelper') . '</label></th>
    <td><input id="social_youtube" type="url" class="regular-text code" name="' . esc_attr(UCP_OPTIONS_KEY) . '[social_youtube]" value="' . esc_attr($options['social_youtube']) . '" placeholder="' . esc_attr__('YouTube page or video URL', 'wpdevhelper') . '">';
        echo '<p class="description">' . esc_attr__('Complete URL, with https prefix, to YouTube page or video.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top" class="hidden">
    <th scope="row"><label for="social_vimeo">' . esc_attr__('Vimeo Profile Page or Video', 'wpdevhelper') . '</label></th>
    <td><input id="social_vimeo" type="url" class="regular-text code" name="' . esc_attr(UCP_OPTIONS_KEY) . '[social_vimeo]" value="' . esc_attr($options['social_vimeo']) . '" placeholder="' . esc_attr__('Vimeo page or video URL', 'wpdevhelper') . '">';
        echo '<p class="description">' . esc_attr__('Complete URL, with https prefix, to Vimeo page or video.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top" class="hidden">
    <th scope="row"><label for="social_pinterest">' . esc_attr__('Pinterest Profile', 'wpdevhelper') . '</label></th>
    <td><input id="social_pinterest" type="url" class="regular-text code" name="' . esc_attr(UCP_OPTIONS_KEY) . '[social_pinterest]" value="' . esc_attr($options['social_pinterest']) . '" placeholder="' . esc_attr__('Pinterest profile URL', 'wpdevhelper') . '">';
        echo '<p class="description">' . esc_attr__('Complete URL, with https prefix, to Pinterest profile.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top" class="hidden">
    <th scope="row"><label for="social_dribbble">' . esc_attr__('Dribbble Profile', 'wpdevhelper') . '</label></th>
    <td><input id="social_dribbble" type="url" class="regular-text code" name="' . esc_attr(UCP_OPTIONS_KEY) . '[social_dribbble]" value="' . esc_attr($options['social_dribbble']) . '" placeholder="' . esc_attr__('Dribbble profile URL', 'wpdevhelper') . '">';
        echo '<p class="description">' . esc_attr__('Complete URL, with https prefix, to Dribbble profile.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top" class="hidden">
    <th scope="row"><label for="social_behance">' . esc_attr__('Behance Profile', 'wpdevhelper') . '</label></th>
    <td><input id="social_behance" type="url" class="regular-text code" name="' . esc_attr(UCP_OPTIONS_KEY) . '[social_behance]" value="' . esc_attr($options['social_behance']) . '" placeholder="' . esc_attr__('Behance profile URL', 'wpdevhelper') . '">';
        echo '<p class="description">' . esc_attr__('Complete URL, with https prefix, to Behance profile.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top" class="hidden">
    <th scope="row"><label for="social_instagram">' . esc_attr__('Instagram Profile', 'wpdevhelper') . '</label></th>
    <td><input id="social_instagram" type="url" class="regular-text code" name="' . esc_attr(UCP_OPTIONS_KEY) . '[social_instagram]" value="' . esc_attr($options['social_instagram']) . '" placeholder="' . esc_attr__('Instagram profile URL', 'wpdevhelper') . '">';
        echo '<p class="description">' . esc_attr__('Complete URL, with https prefix, to Instagram profile.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top" class="hidden">
    <th scope="row"><label for="social_vk">' . esc_attr__('VK Profile', 'wpdevhelper') . '</label></th>
    <td><input id="social_vk" type="url" class="regular-text code" name="' . esc_attr(UCP_OPTIONS_KEY) . '[social_vk]" value="' . esc_attr($options['social_vk']) . '" placeholder="' . esc_attr__('VK profile URL', 'wpdevhelper') . '">';
        echo '<p class="description">' . esc_attr__('Complete URL, with https prefix, to VK profile.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top" class="hidden">
    <th scope="row"><label for="social_telegram">' . esc_attr__('Telegram Group, Channel or Account', 'wpdevhelper') . '</label></th>
    <td><input id="social_telegram" type="text" class="regular-text code" name="' . esc_attr(UCP_OPTIONS_KEY) . '[social_telegram]" value="' . esc_attr($options['social_telegram']) . '" placeholder="' . esc_attr__('Telegram group, channel or account URL', 'wpdevhelper') . '">';
        echo '<p class="description">' . esc_attr__('Complete URL, with https prefix to Telegram group, channel or account.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top" class="hidden">
    <th scope="row"><label for="social_skype">' . esc_attr__('Skype Username', 'wpdevhelper') . '</label></th>
    <td><input id="social_skype" type="text" class="regular-text code" name="' . esc_attr(UCP_OPTIONS_KEY) . '[social_skype]" value="' . esc_attr($options['social_skype']) . '" placeholder="' . esc_attr__('Skype username or account name', 'wpdevhelper') . '">';
        echo '<p class="description">' . esc_attr__('Skype username or account name.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top" class="hidden">
    <th scope="row"><label for="social_whatsapp">' . esc_attr__('WhatsApp Phone Number', 'wpdevhelper') . '</label></th>
    <td><input id="social_whatsapp" type="text" class="regular-text code" name="' . esc_attr(UCP_OPTIONS_KEY) . '[social_whatsapp]" value="' . esc_attr($options['social_whatsapp']) . '" placeholder="' . esc_attr__('123-456-789', 'wpdevhelper') . '">';
        echo '<p class="description">' . esc_attr__('WhatsApp phone number in international format without + or 55 prefix.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top" class="hidden">
    <th scope="row"><label for="social_email">' . esc_attr__('Email Address', 'wpdevhelper') . '</label></th>
    <td><input id="social_email" type="email" class="regular-text code" name="' . esc_attr(UCP_OPTIONS_KEY) . '[social_email]" value="' . esc_attr($options['social_email']) . '" placeholder="' . esc_attr__('name@domain.com', 'wpdevhelper') . '">';
        echo '<p class="description">' . esc_attr__('Email will be encoded on the page to protect it from email address harvesters.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top" class="hidden">
    <th scope="row"><label for="social_phone">' . esc_attr__('Phone Number', 'wpdevhelper') . '</label></th>
    <td><input id="social_phone" type="tel" class="regular-text" name="' . esc_attr(UCP_OPTIONS_KEY) . '[social_phone]" value="' . esc_attr($options['social_phone']) . '" placeholder="' . esc_attr__('+1-123-456-789', 'wpdevhelper') . '">';
        echo '<p class="description">' . esc_attr__('Phone number in full international format.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '<tr><th colspan="2"><a id="show-social-icons" href="#" class="js-action">' . esc_attr__('Show more Social &amp; Contact Icons', 'wpdevhelper') . '</a></th></tr>';

        echo '</table>';

        echo '</div>';

        self::footer_buttons();
    } // tab_content


    static function get_themes()
    {
        $themes = array(
            'mad_designer' => esc_attr__('Mad Designer', 'wpdevhelper'),
            'plain_text' => esc_attr__('Plain Text', 'wpdevhelper'),
            'under_construction' => esc_attr__('Under Construction', 'wpdevhelper'),
            'dark' => esc_attr__('Things Went Dark', 'wpdevhelper'),
            'forklift' => esc_attr__('Forklift at Work', 'wpdevhelper'),
            'under_construction_text' => esc_attr__('Under Construction Text', 'wpdevhelper'),
            'cyber_chick' => esc_attr__('Cyber Chick', 'wpdevhelper'),
            'rocket' => esc_attr__('Rocket Launch', 'wpdevhelper'),
            'loader' => esc_attr__('Loader at Work', 'wpdevhelper'),
            'cyber_chick_dark' => esc_attr__('Cyber Chick Dark', 'wpdevhelper'),
            'safe' => esc_attr__('Safe', 'wpdevhelper'),
            'people' => esc_attr__('People at Work', 'wpdevhelper'),
            'windmill' => esc_attr__('Windmill', 'wpdevhelper'),
            'sad_site' => esc_attr__('Sad Site', 'wpdevhelper'),
            '_pro-soothing-nature' => esc_attr__('Soothing Nature', 'wpdevhelper'),
            'lighthouse' => esc_attr__('Lighthouse', 'wpdevhelper'),
            'hot_air_baloon' => esc_attr__('Hot Air Balloon', 'wpdevhelper'),
            'people_2' => esc_attr__('People at Work #2', 'wpdevhelper'),
            'rocket_2' => esc_attr__('Rocket Launch #2', 'wpdevhelper'),
            'light_bulb' => esc_attr__('Light Bulb', 'wpdevhelper'),
            'ambulance' => esc_attr__('Ambulance', 'wpdevhelper'),
            'laptop' => esc_attr__('Laptop', 'wpdevhelper'),
            'puzzles' => esc_attr__('Puzzles', 'wpdevhelper'),
            'iot' => esc_attr__('Internet of Things', 'wpdevhelper'),
            'setup' => esc_attr__('Setup', 'wpdevhelper'),
            'stop' => esc_attr__('Stop', 'wpdevhelper'),
            'clock' => esc_attr__('Clock', 'wpdevhelper'),
            'bulldozer' => esc_attr__('Bulldozer at Work', 'wpdevhelper'),
            'christmas' => esc_attr__('Christmas Greetings', 'wpdevhelper'),
            'hard_worker' => esc_attr__('Hard Worker', 'wpdevhelper'),
            'closed' => esc_attr__('Temporarily Closed', 'wpdevhelper'),
            'dumper_truck' => esc_attr__('Dumper Truck', 'wpdevhelper'),
            '000webhost' => esc_attr__('000webhost', 'wpdevhelper'),
            'work_desk' => esc_attr__('Work Desk', 'wpdevhelper'),
            'research' => esc_attr__('Research', 'wpdevhelper'),
        );

        $themes = apply_filters('ucp_themes', $themes);

        return $themes;
    } // get_themes


    static function tab_design()
    {
        $options = self::get_options();

        $img_path = UCP_PLUGIN_URL . 'images/thumbnails/';
        $themes = self::get_themes();

        echo '<table class="form-table">';
        echo '<tr valign="top">
    <td colspan="2"><b style="margin-bottom: 10px; display: inline-block;">' . esc_attr__('Themes', 'wpdevhelper') . '</b><br>';
        echo '<input type="hidden" id="theme_id" name="' . esc_attr(UCP_OPTIONS_KEY) . '[theme]" value="' . esc_attr($options['theme']) . '">';

        foreach ($themes as $theme_id => $theme_name) {
            if ($theme_id === $options['theme']) {
                $class = ' active';
            } else {
                $class = '';
            }
            echo '<div class="ucp-thumb' . esc_attr($class) . '" data-theme-id="' . esc_attr($theme_id) . '"><img src="' . esc_attr($img_path . $theme_id) . '.png" alt="' . esc_attr($theme_name) . '" title="' . esc_attr($theme_name) . '"><span>' . esc_attr($theme_name) . '</span>';
            echo '<div class="buttons">';
            if ($theme_id !== $options['theme']) {
                echo '<a href="#" class="button button-primary activate-theme">Activate</a> ';
            }
            echo '<a href="' . esc_url(get_home_url()) . '/?ucp_preview&theme=' . esc_attr($theme_id) . '" class="button-secondary" target="_blank">Preview</a></div>';
            echo '</div>';        
        } // foreach

        echo '</td></tr>';

        echo '<tr valign="top">
    <th scope="row"><label for="custom_css">' . esc_attr__('Custom CSS', 'wpdevhelper') . '</label></th>
    <td>';
        echo '<textarea data-autoresize="1" rows="3" id="custom_css" class="code large-text" name="' . esc_attr(UCP_OPTIONS_KEY) . '[custom_css]" placeholder=".selector { property-name: property-value; }">' . esc_textarea($options['custom_css']) . '</textarea>';
        echo '<p class="description">&lt;style&gt; tags will be added automatically. Do not include them in your code.<br>
    For RTL languages support add: <code>body { direction: rtl; }</code><br>If you need help with writing custom CSS code, post your question on the <a href="https://wordpress.org/support/plugin/under-construction-page/" target="_blank">official forum</a>.</p>';
        echo '</td></tr>';

        echo '</table>';

        self::footer_buttons();
    } // tab_design


    // markup & logic for access tab
    static function tab_access()
    {
        $options = self::get_options();
        $default_options = self::default_options();
        $roles = $users = array();

        $tmp_roles = get_editable_roles();
        foreach ($tmp_roles as $tmp_role => $details) {
            $name = translate_user_role($details['name']);
            $roles[] = array('val' => $tmp_role,  'label' => $name);
        }

        $tmp_users = get_users(array('fields' => array('id', 'display_name')));
        foreach ($tmp_users as $user) {
            $users[] = array('val' => $user->id, 'label' => $user->display_name);
        }

        echo '<div class="ucp-tab-content">';
        echo '<table class="form-table">';

        echo '<tr valign="top" id="whitelisted-roles">
    <th scope="row">' . esc_attr__('Whitelisted User Roles', 'wpdevhelper') . '</th>
    <td>';
        foreach ($roles as $tmp_role) {
            echo  '<input name="' . esc_attr(UCP_OPTIONS_KEY) . '[whitelisted_roles][]" id="roles-' . esc_attr($tmp_role['val']) . '" ' . esc_attr(self::checked($tmp_role['val'], $options['whitelisted_roles'], false)) . ' value="' . esc_attr($tmp_role['val']) . '" type="checkbox" /> <label for="roles-' . esc_attr($tmp_role['val']) . '">' . esc_attr($tmp_role['label']) . '</label><br />';
        }
        echo '<p class="description">' . __('Selected user roles will <b>not</b> be affected by the under construction mode and will always see the "normal" site. Default: administrator.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '<tr valign="top">
    <th scope="row"><label for="whitelisted_users">' . esc_attr__('Whitelisted Users', 'wpdevhelper') . '</label></th>
    <td><select id="whitelisted_users" class="select2" style="width: 50%; max-width: 300px;" name="' . esc_attr(UCP_OPTIONS_KEY) . '[whitelisted_users][]" multiple>';
        self::create_select_options($users, $options['whitelisted_users'], true);

        echo '</select><p class="description">' . __('Selected users (when logged in) will <b>not</b> be affected by the under construction mode and will always see the "normal" site.', 'wpdevhelper') . '</p>';
        echo '</td></tr>';

        echo '</table>';
        echo '</div>';

        self::footer_buttons();
    } // tab_access

    // output the whole options page
    static function main_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        echo '<div class="wrap">
          <h1 class="ucp-logo"><a href="' . esc_url(admin_url('options-general.php?page=ucp')) . '"><img src="' . esc_url(UCP_PLUGIN_URL) . 'images/ucp_logo.png" class="rotate" alt="UnderConstructionPage" title="UnderConstructionPage"><img src="' . esc_url(UCP_PLUGIN_URL) . 'images/ucp_logo_2.png" class="ucp-logo-text" alt="UnderConstructionPage" title="UnderConstructionPage"></a></h1>';

        echo '<form action="options.php" method="post" id="ucp_form">';
        settings_fields(UCP_OPTIONS_KEY);

        $tabs = array();
        $tabs[] = array('id' => 'ucp_main', 'icon' => 'dashicons-admin-settings', 'class' => '', 'label' => __('Main', 'wpdevhelper'), 'callback' => array(__CLASS__, 'tab_main'));
        $tabs[] = array('id' => 'ucp_design', 'icon' => 'dashicons-admin-customizer', 'class' => '', 'label' => esc_attr__('Design', 'wpdevhelper'), 'callback' => array(__CLASS__, 'tab_design'));
        $tabs[] = array('id' => 'ucp_content', 'icon' => 'dashicons-format-aside', 'class' => '', 'label' => esc_attr__('Content', 'wpdevhelper'), 'callback' => array(__CLASS__, 'tab_content'));
        $tabs[] = array('id' => 'ucp_access', 'icon' => 'dashicons-shield', 'class' => '', 'label' => esc_attr__('Access', 'wpdevhelper'), 'callback' => array(__CLASS__, 'tab_access'));

        $tabs = apply_filters('ucp_tabs', $tabs);

        echo '<div id="ucp_tabs" class="ui-tabs" style="display: none;">';
        echo '<ul class="ucp-main-tab">';
        foreach ($tabs as $tab) {
            if (!empty($tab['label'])) {
                echo '<li><a href="#' . esc_attr($tab['id']) . '" class="' . esc_attr($tab['class']) . '"><span class="icon"><span class="dashicons ' . esc_attr($tab['icon']) . '"></span></span><span class="label">' . esc_attr($tab['label']) . '</span></a></li>';
            }
        }
        echo '</ul>';

        foreach ($tabs as $tab) {
            if (is_callable($tab['callback'])) {
                echo '<div style="display: none;" id="' . esc_attr($tab['id']) . '">';
                call_user_func($tab['callback']);
                echo '</div>';
            }
        } // foreach
        echo '</div>'; // ucp_tabs

        echo '</form>'; // ucp_tabs

        echo '</div>'; // wrap
    } // main_page


    // save and preview buttons
    static function footer_buttons()
    {
        echo '<p class="submit">';
        self::wp_kses_wf(get_submit_button(esc_attr__('Save Changes', 'wpdevhelper'), 'primary large', 'submit', false));
        echo ' &nbsp; &nbsp; <a id="ucp_preview" href="' . esc_url(get_home_url()) . '/?ucp_preview" class="button button-large button-secondary" target="_blank">' . esc_attr__('Preview', 'wpdevhelper') . '</a>';
        echo '</p>';
    } // footer_buttons


    // reset all pointers to default state - visible
    static function reset_pointers()
    {
        $pointers = array();

        $pointers['welcome'] = array('target' => '#menu-settings', 'edge' => 'left', 'align' => 'right', 'content' => 'Thank you for installing the <b style="font-weight: 800; font-variant: small-caps;">UnderConstructionPage</b> plugin! Please open <a href="' . admin_url('options-general.php?page=ucp') . '">Settings - UnderConstruction</a> to create a beautiful under construction page.');
        $pointers['getting_started'] = array('target' => '.ucp-main-tab li:nth-child(2)', 'edge' => 'top', 'align' => 'left', 'content' => 'Watch the short <a href="https://www.youtube.com/watch?v=RN4XABhK7_w" target="_blank">getting started video</a> to get you up to speed with UCP in no time. If that doesn\'t answer your questions watch the longer <a href="https://www.youtube.com/watch?v=K3DF-NP6Fog" target="_blank">in-depth video walktrough</a>.<br>If you need the videos later, links are in the <a href="#" class="change_tab" data-tab="4">FAQ</a>.');

        update_option(UCP_POINTERS_KEY, $pointers);
    } // reset_pointers



    // auto download / install / activate WP Force SSL plugin
    static function install_wpfssl()
    {
        check_ajax_referer('install_wpfssl');

        if (false === current_user_can('administrator')) {
            wp_die('Sorry, you have to be an admin to run this action.');
        }

        $plugin_slug = 'wp-force-ssl/wp-force-ssl.php';
        $plugin_zip = 'https://downloads.wordpress.org/plugin/wp-force-ssl.latest-stable.zip';

        @include_once ABSPATH . 'wp-admin/includes/plugin.php';
        @include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        @include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        @include_once ABSPATH . 'wp-admin/includes/file.php';
        @include_once ABSPATH . 'wp-admin/includes/misc.php';
        echo '<style>
		body{
			font-family: sans-serif;
			font-size: 14px;
			line-height: 1.5;
			color: #444;
		}
		</style>';

        echo '<div style="margin: 20px; color:#444;">';
        echo 'If things are not done in a minute <a target="_parent" href="' . admin_url('plugin-install.php?s=force%20ssl%20webfactory&tab=search&type=term') . '">install the plugin manually via Plugins page</a><br><br>';
        echo 'Starting ...<br><br>';

        wp_cache_flush();
        $upgrader = new Plugin_Upgrader();
        echo 'Check if WP Force SSL is already installed ... <br />';
        if (self::is_plugin_installed($plugin_slug)) {
            echo 'WP Force SSL is already installed! <br /><br />Making sure it\'s the latest version.<br />';
            $upgrader->upgrade($plugin_slug);
            $installed = true;
        } else {
            echo 'Installing WP Force SSL.<br />';
            $installed = $upgrader->install($plugin_zip);
        }
        wp_cache_flush();

        if (!is_wp_error($installed) && $installed) {
            echo 'Activating WP Force SSL.<br />';
            $activate = activate_plugin($plugin_slug);

            if (is_null($activate)) {
                echo 'WP Force SSL Activated.<br />';

                echo '<script>setTimeout(function() { top.location = "options-general.php?page=ucp"; }, 1000);</script>';
                echo '<br>If you are not redirected in a few seconds - <a href="options-general.php?page=ucp" target="_parent">click here</a>.';
            }
        } else {
            echo 'Could not install WP Force SSL. You\'ll have to <a target="_parent" href="' . admin_url('plugin-install.php?s=force%20ssl%20webfactory&tab=search&type=term') . '">download and install manually</a>.';
        }

        echo '</div>';
    } // install_wpfssl

    static function wp_kses_wf($html)
    {
        add_filter('safe_style_css', function ($styles) {
            $styles_wf = array(
                'text-align',
                'margin',
                'color',
                'float',
                'border',
                'background',
                'background-color',
                'border-bottom',
                'border-bottom-color',
                'border-bottom-style',
                'border-bottom-width',
                'border-collapse',
                'border-color',
                'border-left',
                'border-left-color',
                'border-left-style',
                'border-left-width',
                'border-right',
                'border-right-color',
                'border-right-style',
                'border-right-width',
                'border-spacing',
                'border-style',
                'border-top',
                'border-top-color',
                'border-top-style',
                'border-top-width',
                'border-width',
                'caption-side',
                'clear',
                'cursor',
                'direction',
                'font',
                'font-family',
                'font-size',
                'font-style',
                'font-variant',
                'font-weight',
                'height',
                'letter-spacing',
                'line-height',
                'margin-bottom',
                'margin-left',
                'margin-right',
                'margin-top',
                'overflow',
                'padding',
                'padding-bottom',
                'padding-left',
                'padding-right',
                'padding-top',
                'text-decoration',
                'text-indent',
                'vertical-align',
                'width',
                'display',
            );

            foreach ($styles_wf as $style_wf) {
                $styles[] = $style_wf;
            }
            return $styles;
        });

        $allowed_tags = wp_kses_allowed_html('post');
        $allowed_tags['input'] = array(
            'type' => true,
            'style' => true,
            'class' => true,
            'id' => true,
            'checked' => true,
            'disabled' => true,
            'name' => true,
            'size' => true,
            'placeholder' => true,
            'value' => true,
            'data-*' => true,
            'size' => true,
            'disabled' => true
        );

        $allowed_tags['textarea'] = array(
            'type' => true,
            'style' => true,
            'class' => true,
            'id' => true,
            'checked' => true,
            'disabled' => true,
            'name' => true,
            'size' => true,
            'placeholder' => true,
            'value' => true,
            'data-*' => true,
            'cols' => true,
            'rows' => true,
            'disabled' => true,
            'autocomplete' => true
        );

        $allowed_tags['select'] = array(
            'type' => true,
            'style' => true,
            'class' => true,
            'id' => true,
            'checked' => true,
            'disabled' => true,
            'name' => true,
            'size' => true,
            'placeholder' => true,
            'value' => true,
            'data-*' => true,
            'multiple' => true,
            'disabled' => true
        );

        $allowed_tags['option'] = array(
            'type' => true,
            'style' => true,
            'class' => true,
            'id' => true,
            'checked' => true,
            'disabled' => true,
            'name' => true,
            'size' => true,
            'placeholder' => true,
            'value' => true,
            'selected' => true,
            'data-*' => true
        );
        $allowed_tags['optgroup'] = array(
            'type' => true,
            'style' => true,
            'class' => true,
            'id' => true,
            'checked' => true,
            'disabled' => true,
            'name' => true,
            'size' => true,
            'placeholder' => true,
            'value' => true,
            'selected' => true,
            'data-*' => true,
            'label' => true
        );

        $allowed_tags['a'] = array(
            'href' => true,
            'data-*' => true,
            'class' => true,
            'style' => true,
            'id' => true,
            'target' => true,
            'data-*' => true,
            'role' => true,
            'aria-controls' => true,
            'aria-selected' => true,
            'disabled' => true
        );

        $allowed_tags['div'] = array(
            'style' => true,
            'class' => true,
            'id' => true,
            'data-*' => true,
            'role' => true,
            'aria-labelledby' => true,
            'value' => true,
            'aria-modal' => true,
            'tabindex' => true
        );

        $allowed_tags['li'] = array(
            'style' => true,
            'class' => true,
            'id' => true,
            'data-*' => true,
            'role' => true,
            'aria-labelledby' => true,
            'value' => true,
            'aria-modal' => true,
            'tabindex' => true
        );

        $allowed_tags['span'] = array(
            'style' => true,
            'class' => true,
            'id' => true,
            'data-*' => true,
            'aria-hidden' => true
        );

        $allowed_tags['style'] = array(
            'class' => true,
            'id' => true,
            'type' => true
        );

        $allowed_tags['fieldset'] = array(
            'class' => true,
            'id' => true,
            'type' => true
        );

        $allowed_tags['link'] = array(
            'class' => true,
            'id' => true,
            'type' => true,
            'rel' => true,
            'href' => true,
            'media' => true
        );

        $allowed_tags['form'] = array(
            'style' => true,
            'class' => true,
            'id' => true,
            'method' => true,
            'action' => true,
            'data-*' => true
        );

        $allowed_tags['script'] = array(
            'class' => true,
            'id' => true,
            'type' => true,
            'src' => true
        );

        echo wp_kses($html, $allowed_tags);

        add_filter('safe_style_css', function ($styles) {
            $styles_wf = array(
                'text-align',
                'margin',
                'color',
                'float',
                'border',
                'background',
                'background-color',
                'border-bottom',
                'border-bottom-color',
                'border-bottom-style',
                'border-bottom-width',
                'border-collapse',
                'border-color',
                'border-left',
                'border-left-color',
                'border-left-style',
                'border-left-width',
                'border-right',
                'border-right-color',
                'border-right-style',
                'border-right-width',
                'border-spacing',
                'border-style',
                'border-top',
                'border-top-color',
                'border-top-style',
                'border-top-width',
                'border-width',
                'caption-side',
                'clear',
                'cursor',
                'direction',
                'font',
                'font-family',
                'font-size',
                'font-style',
                'font-variant',
                'font-weight',
                'height',
                'letter-spacing',
                'line-height',
                'margin-bottom',
                'margin-left',
                'margin-right',
                'margin-top',
                'overflow',
                'padding',
                'padding-bottom',
                'padding-left',
                'padding-right',
                'padding-top',
                'text-decoration',
                'text-indent',
                'vertical-align',
                'width'
            );

            foreach ($styles_wf as $style_wf) {
                if (($key = array_search($style_wf, $styles)) !== false) {
                    unset($styles[$key]);
                }
            }
            return $styles;
        });
    }


    static function is_plugin_installed($slug)
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = get_plugins();

        if (!empty($all_plugins[$slug])) {
            return true;
        } else {
            return false;
        }
    } // is_plugin_installed

    // reset pointers on activation
    static function activate()
    {
        self::reset_pointers();
        self::empty_cache();
    } // activate


    // clean up on deactivation
    static function deactivate()
    {
        delete_option(UCP_POINTERS_KEY);
        delete_option(UCP_NOTICES_KEY);
        self::empty_cache();
    } // deactivate

} // class UCP


// hook everything up
register_activation_hook(__FILE__, array('UCP', 'activate'));
register_deactivation_hook(__FILE__, array('UCP', 'deactivate'));
add_action('init', array('UCP', 'init'));
add_action('plugins_loaded', array('UCP', 'plugins_loaded'));
