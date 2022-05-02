<?php

if ( ! defined( 'ABSPATH' ) ) 
  exit; // Exit if accessed directly.
  
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


/*====================================================
=            CONFIG NEW CUSTOM POST TYPES            =
====================================================*/
$postTypes = get_posts('post_type=new_post_type&posts_per_page=-1');
if (count($postTypes) >= 1) {
  foreach ($postTypes as $postType) {    
    add_action('init', function () use ($postType) {
      $post_type_settings = array(
        'labels' => [
          'name'                  => __(get_field('wpdevhelper-posttype-name_plural', $postType->ID), 'Post Type General Name', get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'singular_name'         => __(get_field('wpdevhelper-posttype-name_singular', $postType->ID), 'Post Type Singular Name', get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'menu_name'             => __(get_field('wpdevhelper-posttype-menu_name', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'name_admin_bar'        => __(get_field('wpdevhelper-posttype-admin_bar_name', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'archives'              => __(get_field('wpdevhelper-posttype-archives', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'attributes'            => __(get_field('wpdevhelper-posttype-attributes', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'parent_item_colon'     => __(get_field('wpdevhelper-posttype-parent_item', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'all_items'             => __(get_field('wpdevhelper-posttype-all_items', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'add_new_item'          => __(get_field('wpdevhelper-posttype-add_new_item', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'add_new'               => __(get_field('wpdevhelper-posttype-add_new', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'new_item'              => __(get_field('wpdevhelper-posttype-new_item', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'edit_item'             => __(get_field('wpdevhelper-posttype-edit_item', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'update_item'           => __(get_field('wpdevhelper-posttype-update_item', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'view_item'             => __(get_field('wpdevhelper-posttype-view_item', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'view_items'            => __(get_field('wpdevhelper-posttype-view_items', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'search_item'           => __(get_field('wpdevhelper-posttype-search_item', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'not_found'             => __(get_field('wpdevhelper-posttype-not_found', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'not_found_in_trash'    => __(get_field('wpdevhelper-posttype-not_found_in_trash', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'featured_image'        => __(get_field('wpdevhelper-posttype-featured_image', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'set_featured_image'    => __(get_field('wpdevhelper-posttype-set_featured_image', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'remove_featured_image' => __(get_field('wpdevhelper-posttype-remove_featured_image', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'use_featured_image'    => __(get_field('wpdevhelper-posttype-use_as_featured_image', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'insert_into_item'      => __(get_field('wpdevhelper-posttype-insert_into_item', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'uploaded_to_this_item' => __(get_field('wpdevhelper-posttype-uploaded_to_this_item', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'items_list'            => __(get_field('wpdevhelper-posttype-items_list', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'items_list_navigation' => __(get_field('wpdevhelper-posttype-items_list_navigation', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
          'filter_items_list'     => __(get_field('wpdevhelper-posttype-filter_items_list', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
        ],
        'label'               => __(get_field('wpdevhelper-posttype-name_singular', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
        'description'         => __(get_field('wpdevhelper-posttype-description', $postType->ID), get_field('wpdevhelper-posttype-text_domain', $postType->ID)),
        'supports'            => get_field('wpdevhelper-posttype-supports', $postType->ID),
        'hierarchical'        => get_field('wpdevhelper-posttype-hierarchical', $postType->ID) === 'true' ? true : false,
        'public'              => get_field('wpdevhelper-posttype-public', $postType->ID) === 'true' ? true : false,
        'show_ui'             => get_field('wpdevhelper-posttype-show_ui', $postType->ID) === 'true' ? true : false,
        'show_in_menu'        => get_field('wpdevhelper-posttype-show_in_admin_sidebar', $postType->ID) === 'true' ? true : false,
        'menu_position'       => (int)get_field('wpdevhelper-posttype-admin_sidebar_location', $postType->ID),
        'menu_icon'           => get_field('wpdevhelper-posttype-sidebar_icon', $postType->ID) == '' ? 'dashicons-admin-post' : get_field('wpdevhelper-posttype-sidebar_icon', $postType->ID),
        'show_in_admin_bar'   => get_field('wpdevhelper-posttype-show_in_admin_bar', $postType->ID) === 'true' ? true : false,
        'show_in_nav_menus'   => get_field('wpdevhelper-posttype-show_in_navigation_menus', $postType->ID) === 'true' ? true : false,
        'can_export'          => get_field('wpdevhelper-posttype-enable_export', $postType->ID) === 'true' ? true : false,
        'exclude_from_search' => get_field('wpdevhelper-posttype-exclude_from_search', $postType->ID) === 'true' ? true : false,
        'publicly_queryable'  => get_field('wpdevhelper-posttype-publicly_queryable', $postType->ID) === 'true' ? true : false,
        'capability_type'     => get_field('wpdevhelper-posttype-base_capability_type', $postType->ID),
      );

      # Taxonomy
      if (trim(get_field('wpdevhelper-posttype-link_to_taxonomies', $postType->ID)) != '') {
        $post_type_settings['taxonomies'] = explode(',', get_field('wpdevhelper-posttype-link_to_taxonomies', $postType->ID));
      }

      # Custom archives
      if (get_field('wpdevhelper-posttype-enable_archives', $postType->ID) == 'custom') {
        $post_type_settings['has_archive'] = get_field('wpdevhelper-posttype-custom_archive_slug', $postType->ID);
      } else {
        $post_type_settings['has_archive'] = get_field('wpdevhelper-posttype-enable_archives', $postType->ID) === 'true' ? true : false;
      }
      
      # Custom query
      if (get_field('wpdevhelper-posttype-query', $postType->ID) == 'custom') {
        $post_type_settings['query_var'] = get_field('wpdevhelper-posttype-custom_query', $postType->ID);
      } 

      # Permalink Rewrite
      if (get_field('wpdevhelper-posttype-permalink_rewrite', $postType->ID) == 'false') {
        $post_type_settings['rewrite'] = false;
      } elseif (get_field('wpdevhelper-posttype-permalink_rewrite', $postType->ID) == 'true') {
        $post_type_settings['rewrite'] = true;
      } elseif (get_field('wpdevhelper-posttype-permalink_rewrite', $postType->ID) == 'custom') {
        $post_type_settings['rewrite'] = array(
          'slug'       => get_field('wpdevhelper-posttype-url_slug', $postType->ID),
          'with_front' => get_field('wpdevhelper-posttype-use_url_slug', $postType->ID) === 'true' ? true : false,
          'pages'      => get_field('wpdevhelper-posttype-pagination', $postType->ID) === 'true' ? true : false,
          'feeds'      => get_field('wpdevhelper-posttype-feeds', $postType->ID) === 'true' ? true : false,
        );
      }

      # Show in REST API
      if (get_field('wpdevhelper-show_in_rest_api', $postType->ID) == 'true') {
        $post_type_settings['show_in_rest'] = true;
        
        if (trim(get_field('wpdevhelper-posttype-rest_base', $postType->ID)) != '') {
          $post_type_settings['rest_base'] = get_field('wpdevhelper-posttype-rest_base', $postType->ID);
        }

        if (trim(get_field('wpdevhelper-posttype-rest_controller_class', $postType->ID)) != '') {
          $post_type_settings['rest_controller_class'] = get_field('wpdevhelper-posttype-rest_controller_class', $postType->ID);
        }
      }

      # Custom Capabilities
      if (get_field('wpdevhelper-posttype-capabilities', $postType->ID) == 'custom') {
        $post_type_settings['capabilities'] = array(
          'edit_post'             => get_field('wpdevhelper-posttype-edit_post', $postType->ID),
          'read_post'             => get_field('wpdevhelper-posttype-read_post', $postType->ID),
          'delete_post'           => get_field('wpdevhelper-posttype-delete_post', $postType->ID),
          'edit_posts'            => get_field('wpdevhelper-posttype-edit_posts', $postType->ID),
          'edit_others_posts'     => get_field('wpdevhelper-posttype-edit_others_posts', $postType->ID),
          'publish_posts'         => get_field('wpdevhelper-posttype-publish_posts', $postType->ID),
          'read_private_posts'    => get_field('wpdevhelper-posttype-read_private_posts', $postType->ID),
        );
      }
      
      # Register post type
      register_post_type(
        get_field('wpdevhelper-posttype-post_type_key', $postType->ID),
        $post_type_settings
      );
    });
  }
}
