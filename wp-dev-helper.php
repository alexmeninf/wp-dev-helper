<?php
/*
 * Plugin Name: WP Dev Helper
 * Plugin URI: https://github.com/alexmeninf/wp-dev-helper
 * Description: An awesome plugin that help WordPress developers to develop their themes faster than ever.
 * Version: 2.1.0
 * License: GPL
 * Author: Alexandre Menin
 * Author URI: https://github.com/alexmeninf
 * Text Domain: wpdevhelper
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Informations
 */
define('WPDEVHELPER_VERSION', '2.1.0');
define('WPDEVHELPER_REPOSITORY', 'https://github.com/alexmeninf/wp-dev-helper');
define('WPDEVHELPER__MINIMUM_WP_VERSION', '5.8');

// Directories
if ( ! defined('THEMEROOT') )
  define('THEMEROOT', get_template_directory_uri());

define('PLUGINROOT', plugins_url('', __FILE__));
define('PLUGINPATH', plugin_dir_path(__FILE__));

/**
 * Include functions
 */
require_once ABSPATH . "wp-includes/pluggable.php";
include_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once PLUGINPATH . 'includes/wpdh-sanitize-characters.php';

/**
 * Languages
 */
load_theme_textdomain('wpdevhelper', PLUGINPATH . 'languages');

/**
 * Delete revision posts, pages
 */
require ( PLUGINPATH . 'includes/class.post-revision.php' );

/**
 * ACF PRO
 */
if (!is_plugin_active('advanced-custom-fields-pro/acf.php')) {  
  define('ACF_LITE', false);
  require_once PLUGINPATH . 'includes/advanced-custom-fields-pro/acf.php';
  include_once PLUGINPATH . 'includes/acf-code-field/acf-code-field.php';
}

/**
 * Plugin options fields
 */
include_once PLUGINPATH . 'includes/wpdh-acf-options.php';

/**
 * Create new post types
 */
include_once PLUGINPATH . 'includes/wpdh-register-post_type.php';

/**
 * Code injection into pages
 */
include_once PLUGINPATH . 'includes/wpdh-code-in-page.php';

/**
 * Duplicate post and page
 */
include_once PLUGINPATH . 'includes/wpdh-duplicate-post.php';

/**
 * WP Dev Helper Startup Settings
 */
include_once PLUGINPATH . 'includes/class.developers.php';

$wpdh = new Developers();
$wpdh->pageDevelopers();
$wpdh->pageHeadFooterPostInjections();
$wpdh->developersDashboardRemoveWidgets();
$wpdh->developersDashboardAddBox();
$wpdh->developersTaxonomiesHierarchical();
$wpdh->developersLoginScreenEnable();
$wpdh->developersWPHeadMetaDescription();
$wpdh->developersWPHeadMetaThemeColor();
$wpdh->developersWPHeadFavicon();
$wpdh->developersWPHeadOpenGraph();
$wpdh->developersTemplateSettingsFormGenerator();
$wpdh->developersOthersDuplicate();
$wpdh->developersAdminPanelComments();
$wpdh->developersAdvancedWPHead();
$wpdh->developersAdvancedCustomCSS();

/**
 * Style
 */
add_filter('admin_enqueue_scripts', 'admin_header_styles', 10);
function admin_header_styles()
{
  wp_enqueue_style('wpdh-style', PLUGINROOT . '/assets/css/wpdh-style.css');
}

/**
 * Script
 */
add_filter('admin_enqueue_scripts', 'admin_footer_scripts', 10);
function admin_footer_scripts()
{
  wp_enqueue_script('wpdh-script', PLUGINROOT . '/assets/js/wpdh-scripts.js');
}
