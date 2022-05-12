<?php
/*
 * Plugin Name: WP Dev Helper
 * Plugin URI: https://github.com/alexmeninf/wp-dev-helper
 * Description: An awesome plugin that help WordPress developers to develop their themes faster than ever.
 * Version: 2.0
 * License: GPL
 * Author: Alexandre Menin
 * Author URI: https://github.com/alexmeninf
 * Text Domain: wpdevhelper
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once(ABSPATH . "wp-includes/pluggable.php");


/*============================
=            INFO            =
============================*/
define('WPDEVHELPER_VERSION', '2.0');
define('WPDEVHELPER_REPOSITORY', 'https://github.com/alexmeninf/wp-dev-helper');
define('WPDEVHELPER__MINIMUM_WP_VERSION', '5.8');


/*==============================================
=            THEME/PLUGIN PATH/ROOT            =
==============================================*/
if ( ! defined('THEMEROOT') )
  define('THEMEROOT', get_template_directory_uri());

define('PLUGINROOT', plugins_url('', __FILE__));
define('PLUGINPATH', plugin_dir_path(__FILE__));


/*=================================
=            LANGUAGES            =
=================================*/
load_theme_textdomain('wpdevhelper', PLUGINPATH . 'languages');


/*=================================
=            INCLUDES            =
=================================*/
require ( PLUGINPATH . 'includes/sanitize-characters.php' );

/*=======================================
=            INCLUDE ACF PRO            =
=======================================*/
include_once ABSPATH . 'wp-admin/includes/plugin.php';

if (!is_plugin_active('advanced-custom-fields-pro/acf.php')) {
  
  define('ACF_LITE', false);
  require_once PLUGINPATH . 'advanced-custom-fields-pro/acf.php';
  include PLUGINPATH . 'acf-code-field/acf-code-field.php';
}

include PLUGINPATH . 'acf-options.php';

/*==============================================================
=            HTML, CSS, JS MINIFIER; DUPLICATE POST            =
==============================================================*/
include_once PLUGINPATH . 'includes/register-post_type.php';

/*==============================================================
=                ADICIONAR CÓDIGO NAS PÁGINAS               =
==============================================================*/
include_once PLUGINPATH . 'includes/add-code-in-page.php';

/*==============================================================
=            HTML, CSS, JS MINIFIER; DUPLICATE POST            =
==============================================================*/
include_once PLUGINPATH . 'duplicate-post.php';

/*=================================================
=            INIT/CONFIG WP DEV HELPER            =
=================================================*/
include_once PLUGINPATH . 'class.developers.php';

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
$wpdh->developersAdvancedCustomCSS($wpdhCssCode = get_field('wpdevhelperAdvanced-custom_css', 'option'));

/*----------  Custom style  ----------*/
add_filter('admin_enqueue_scripts', 'admin_header_styles', 10);
function admin_header_styles()
{
  wp_enqueue_style('my_custom_style', PLUGINROOT . '/assets/css/wpdh-style.css');
}

/*----------  Custom script  ----------*/
add_filter('admin_enqueue_scripts', 'admin_footer_scripts', 10);
function admin_footer_scripts()
{
  wp_enqueue_script('my_custom_script', PLUGINROOT . '/assets/js/wpdh-scripts.js');
}