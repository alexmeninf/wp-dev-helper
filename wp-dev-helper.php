<?php
/*
 * Plugin Name: WP Dev Helper
 * Plugin URI: https://github.com/alexmeninf/wp-dev-helper
 * Description: An awesome plugin that help WordPress developers to develop their themes faster than ever.
 * Version: 1.7.3
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
define('WPDEVHELPER_VERSION', '1.7.3');
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


/*====================================================
=            REGISTER POST TYPE GENERATOR            =
====================================================*/
function new_post_type_generator()
{
  $labels = array(
    'name'                  => _x('Post Types', 'Post Type General Name', 'wpdevhelper'),
    'singular_name'         => _x('Post Type', 'Post Type Singular Name', 'wpdevhelper'),
    'menu_name'             => __('Post Types', 'wpdevhelper'),
    'name_admin_bar'        => __('Post Type', 'wpdevhelper'),
    'archives'              => __('Item Archives', 'wpdevhelper'),
    'attributes'            => __('Item Attributes', 'wpdevhelper'),
    'parent_item_colon'     => __('Parent Item:', 'wpdevhelper'),
    'all_items'             => __('All Items', 'wpdevhelper'),
    'add_new_item'          => __('Add New Item', 'wpdevhelper'),
    'add_new'               => __('Add New', 'wpdevhelper'),
    'new_item'              => __('New Item', 'wpdevhelper'),
    'edit_item'             => __('Edit Item', 'wpdevhelper'),
    'update_item'           => __('Update Item', 'wpdevhelper'),
    'view_item'             => __('View Item', 'wpdevhelper'),
    'view_items'            => __('View Items', 'wpdevhelper'),
    'search_items'          => __('Search Item', 'wpdevhelper'),
    'not_found'             => __('Not found', 'wpdevhelper'),
    'not_found_in_trash'    => __('Not found in Trash', 'wpdevhelper'),
    'featured_image'        => __('Featured Image', 'wpdevhelper'),
    'set_featured_image'    => __('Set featured image', 'wpdevhelper'),
    'remove_featured_image' => __('Remove featured image', 'wpdevhelper'),
    'use_featured_image'    => __('Use as featured image', 'wpdevhelper'),
    'insert_into_item'      => __('Insert into item', 'wpdevhelper'),
    'uploaded_to_this_item' => __('Uploaded to this item', 'wpdevhelper'),
    'items_list'            => __('Items list', 'wpdevhelper'),
    'items_list_navigation' => __('Items list navigation', 'wpdevhelper'),
    'filter_items_list'     => __('Filter items list', 'wpdevhelper'),
  );
  $args = array(
    'label'                 => __('Post Types', 'wpdevhelper'),
    'description'           => __('Create New Post Type', 'wpdevhelper'),
    'labels'                => $labels,
    'supports'              => array(),
    'taxonomies'            => array(),
    'hierarchical'          => false,
    'public'                => false,
    'show_ui'               => true,
    'show_in_menu'          => false,
    'menu_position'         => 5,
    'show_in_admin_bar'     => false,
    'show_in_nav_menus'     => false,
    'can_export'            => true,
    'has_archive'           => false,
    'exclude_from_search'   => false,
    'publicly_queryable'    => false,
    'capability_type'       => 'post',
  );
  register_post_type('new_post_type', $args);
}
add_action('init', 'new_post_type_generator', 0);


/*============================================================
=            REGISTER POST TYPE DASHBOARD WIDGETS            =
============================================================*/
function dashboard_widget_post_type()
{
  $labels = array(
    'name'                  => _x('Dashboard Widgets', 'Post Type General Name', 'wpdevhelper'),
    'singular_name'         => _x('Dashboard Widget', 'Post Type Singular Name', 'wpdevhelper'),
    'menu_name'             => __('Dashboard Widgets', 'wpdevhelper'),
    'name_admin_bar'        => __('Dashboard Widgets', 'wpdevhelper'),
    'archives'              => __('Item Archives', 'wpdevhelper'),
    'attributes'            => __('Item Attributes', 'wpdevhelper'),
    'parent_item_colon'     => __('Parent Item:', 'wpdevhelper'),
    'all_items'             => __('All Items', 'wpdevhelper'),
    'add_new_item'          => __('Add New Item', 'wpdevhelper'),
    'add_new'               => __('Add New', 'wpdevhelper'),
    'new_item'              => __('New Item', 'wpdevhelper'),
    'edit_item'             => __('Edit Item', 'wpdevhelper'),
    'update_item'           => __('Update Item', 'wpdevhelper'),
    'view_item'             => __('View Item', 'wpdevhelper'),
    'view_items'            => __('View Items', 'wpdevhelper'),
    'search_items'          => __('Search Item', 'wpdevhelper'),
    'not_found'             => __('Not found', 'wpdevhelper'),
    'not_found_in_trash'    => __('Not found in Trash', 'wpdevhelper'),
    'featured_image'        => __('Featured Image', 'wpdevhelper'),
    'set_featured_image'    => __('Set featured image', 'wpdevhelper'),
    'remove_featured_image' => __('Remove featured image', 'wpdevhelper'),
    'use_featured_image'    => __('Use as featured image', 'wpdevhelper'),
    'insert_into_item'      => __('Insert into item', 'wpdevhelper'),
    'uploaded_to_this_item' => __('Uploaded to this item', 'wpdevhelper'),
    'items_list'            => __('Items list', 'wpdevhelper'),
    'items_list_navigation' => __('Items list navigation', 'wpdevhelper'),
    'filter_items_list'     => __('Filter items list', 'wpdevhelper'),
  );
  $args = array(
    'label'                 => __('Dashboard Widgets', 'wpdevhelper'),
    'description'           => __('Create New Dashboard Widget', 'wpdevhelper'),
    'labels'                => $labels,
    'supports'              => array(),
    'taxonomies'            => array(),
    'hierarchical'          => false,
    'public'                => false,
    'show_ui'               => true,
    'show_in_menu'          => false,
    'menu_position'         => 5,
    'show_in_admin_bar'     => false,
    'show_in_nav_menus'     => false,
    'can_export'            => true,
    'has_archive'           => false,
    'exclude_from_search'   => false,
    'publicly_queryable'    => false,
    'capability_type'       => 'post',
  );
  register_post_type('dashboard_widget', $args);
}
add_action('init', 'dashboard_widget_post_type', 0);


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
$wpdh->developersAdvancedWPHead();
$wpdh->developersAdvancedCustomCSS($wpdhCssCode = get_field('wpdevhelperAdvanced-custom_css', 'option'));
$wpdh->developersAdvancedCustomJSHead($wpdhJsHeadCode = get_field('wpdevhelperAdvanced-custom_js_head', 'option'));
$wpdh->developersAdvancedCustomJSFooter($wpdhJsFooterCode = get_field('wpdevhelperAdvanced-custom_js_footer', 'option'));


/*====================================================
=            CONFIG NEW CUSTOM POST TYPES            =
====================================================*/
$postTypes = get_posts('post_type=new_post_type&posts_per_page=-1');
if (count($postTypes) >= 1) {
  foreach ($postTypes as $postType) {
    add_action('init', function () use ($postType) {
      $postTypeArgs = array();
      # Custom archives
      if (get_field('wpdevhelper-posttype-enable_archives', $postType->ID) == 'custom') {
        $postTypeArgs['has_archive'] = get_field('wpdevhelper-posttype-custom_archive_slug', $postType->ID);
      } else {
        $postTypeArgs['has_archive'] = get_field('wpdevhelper-posttype-enable_archives', $postType->ID) === 'true' ? true : false;
      }
      # Custom query
      if (get_field('wpdevhelper-posttype-query', $postType->ID) == 'custom') {
        $postTypeArgs['query_var'] = get_field('wpdevhelper-posttype-custom_query', $postType->ID);
      }
      # Base capabilities
      if (get_field('wpdevhelper-posttype-capabilities', $postType->ID) == 'base') {
        $postTypeArgs['capability_type'] = get_field('wpdevhelper-posttype-base_capability_type', $postType->ID);
      }
      # Register post type
      register_post_type(
        get_field('wpdevhelper-posttype-post_type_key', $postType->ID),
        [
          'labels' => [
            'name' => __(get_field('wpdevhelper-posttype-name_plural', $postType->ID), 'Post Type General Name', get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'singular_name' => __(get_field('wpdevhelper-posttype-name_singular', $postType->ID), 'Post Type Singular Name', get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'menu_name' => __(get_field('wpdevhelper-posttype-menu_name', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'name_admin_bar' => __(get_field('wpdevhelper-posttype-admin_bar_name', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'archives' => __(get_field('wpdevhelper-posttype-archives', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'attributes' => __(get_field('wpdevhelper-posttype-attributes', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'parent_item_colon' => __(get_field('wpdevhelper-posttype-parent_item', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'all_items' => __(get_field('wpdevhelper-posttype-all_items', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'add_new_item' => __(get_field('wpdevhelper-posttype-add_new_item', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'add_new' => __(get_field('wpdevhelper-posttype-add_new', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'new_item' => __(get_field('wpdevhelper-posttype-new_item', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'edit_item' => __(get_field('wpdevhelper-posttype-edit_item', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'update_item' => __(get_field('wpdevhelper-posttype-update_item', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'view_item' => __(get_field('wpdevhelper-posttype-view_item', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'view_items' => __(get_field('wpdevhelper-posttype-view_items', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'search_item' => __(get_field('wpdevhelper-posttype-search_item', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'not_found' => __(get_field('wpdevhelper-posttype-not_found', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'not_found_in_trash' => __(get_field('wpdevhelper-posttype-not_found_in_trash', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'featured_image' => __(get_field('wpdevhelper-posttype-featured_image', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'set_featured_image' => __(get_field('wpdevhelper-posttype-set_featured_image', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'remove_featured_image' => __(get_field('wpdevhelper-posttype-remove_featured_image', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'use_featured_image' => __(get_field('wpdevhelper-posttype-use_as_featured_image', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'insert_into_item' => __(get_field('wpdevhelper-posttype-insert_into_item', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'uploaded_to_this_item' => __(get_field('wpdevhelper-posttype-uploaded_to_this_item', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'items_list' => __(get_field('wpdevhelper-posttype-items_list', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'items_list_navigation' => __(get_field('wpdevhelper-posttype-items_list_navigation', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
            'filter_items_list' => __(get_field('wpdevhelper-posttype-filter_items_list', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          ],
          'label' => __(get_field('wpdevhelper-posttype-name_singular', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'description' => __(get_field('wpdevhelper-posttype-description', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'supports' => get_field('wpdevhelper-posttype-supports', $postType->ID),
          'taxonomies' => explode(',', get_field('wpdevhelper-posttype-link_to_taxonomies', $postType->ID)),
          'hierarchical' => get_field('wpdevhelper-posttype-hierarchical', $postType->ID) === 'true' ? true : false,
          'public' => get_field('wpdevhelper-posttype-public', $postType->ID) === 'true' ? true : false,
          'show_ui' => get_field('wpdevhelper-posttype-show_ui', $postType->ID) === 'true' ? true : false,
          'show_in_menu' => get_field('wpdevhelper-posttype-show_in_admin_sidebar', $postType->ID) === 'true' ? true : false,
          'menu_position' => get_field('wpdevhelper-posttype-admin_sidebar_location', $postType->ID),
          'menu_icon' => get_field('wpdevhelper-posttype-sidebar_icon', $postType->ID) == '' ? 'dashicons-admin-post' : get_field('wpdevhelper-posttype-sidebar_icon', $postType->ID),
          'show_in_admin_bar' => get_field('wpdevhelper-posttype-show_in_admin_bar', $postType->ID) === 'true' ? true : false,
          'show_in_nav_menus' => get_field('wpdevhelper-posttype-show_in_navigation_menus', $postType->ID) === 'true' ? true : false,
          'can_export' => get_field('wpdevhelper-posttype-enable_export', $postType->ID) === 'true' ? true : false,
          'exclude_from_search' => get_field('wpdevhelper-posttype-exclude_from_search', $postType->ID) === 'true' ? true : false,
          'publicly_queryable' => get_field('wpdevhelper-posttype-publicly_queryable', $postType->ID) === 'true' ? true : false,
          'show_in_rest' => true,
          $postTypeArgs
        ]
      );
    });
  }
}

/*----------  Custom style  ----------*/
add_filter('admin_enqueue_scripts', 'admin_header_styles', 10);
function admin_header_styles()
{
  wp_enqueue_style('my_custom_style', PLUGINROOT . '/assets/css/style.css');
}

/*----------  Custom script  ----------*/
add_filter('admin_enqueue_scripts', 'admin_footer_scripts', 10);
function admin_footer_scripts()
{
  wp_enqueue_script('my_custom_script', PLUGINROOT . '/assets/js/scripts.js');
}