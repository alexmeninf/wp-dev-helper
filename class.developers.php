<?php

class Developers
{

  /*----------  CREATE PAGE DEVELOPERS  ----------*/
  public function pageDevelopers()
  {
    if (function_exists('acf_add_options_page')) {
      acf_add_options_page(array(
        'page_title' => 'WP Dev Helper',
        'menu_title' => __('Developers', 'wpdevhelper'),
        'menu_slug'  => 'wp-dev-helper',
        'capability' => 'edit_posts',
        'icon_url'   => 'dashicons-coffee',
        'redirect'   => false
      ));
    }
  }

  /**
   * Ceate page Head, Footer and Post Injections
   */
  public function pageHeadFooterPostInjections()
  {
    if (function_exists('acf_add_options_page')) {
      acf_add_options_page(array(
        'page_title' => __('Head, Footer and Post Injections', 'wpdevhelper'),
        'menu_title' => __('Code Injections', 'wpdevhelper'),
        'menu_slug'  => 'wpdh-injections',
        'parent_slug' => 'wp-dev-helper',
        'capability' => 'edit_posts',
        'icon_url'   => 'dashicons-editor-code',
        'redirect'   => false
      ));
    }
  }

  /*----------  DASHBOARD -> REMOVE WIDGETS  ----------*/
  public function developersDashboardRemoveWidgets()
  {
    if (is_array(get_field('wpdevhelperDashboard-remove_widgets', 'option'))) {
      add_action('admin_init', function () {
        foreach (get_field('wpdevhelperDashboard-remove_widgets', 'option') as $widget) {
          remove_meta_box($widget, 'dashboard', 'normal');
        }
      });
    }
  }

  /*----------  DASHBOARD -> ADD WIDGETS  ----------*/
  public function developersDashboardAddBox()
  {
    add_action('wp_dashboard_setup', function ($widget) {
      $dashboardWidgets = get_posts('post_type=dashboard_widget&posts_per_page=-1&orderby=meta_value&meta_key=order&order=ASC');
      if (count($dashboardWidgets) >= 1) {
        foreach ($dashboardWidgets as $widget) {
          $post_content = $widget->post_content;
          wp_add_dashboard_widget('dashwidget_' . $widget->ID, $widget->post_title, function () use ($post_content) {
            echo apply_filters('the_content', $post_content);
          });
        }
      }
    });
  }

  /*----------  TAXONOMIES -> HIERARCHICAL ORDER  ----------*/
  public function developersTaxonomiesHierarchical()
  {
    if (count(explode(',', get_field('wpdevhelperTax-hierarchical_order', 'option'))) >= 1) {
      add_action('wp_terms_checklist_args', function ($args) {
        $getTaxonomies = explode(',', str_replace(' ', '', get_field('wpdevhelperTax-hierarchical_order', 'option')));
        if (in_array($args['taxonomy'], $getTaxonomies)) {
          $args['checked_ontop'] = false;
        }
        return $args;
      }, 12);
    }
  }

  /*----------  LOGIN SCREEN -> ENABLE  ----------*/
  public function developersLoginScreenEnable()
  {
    if (get_field('wpdevhelperLoginScreen-enable_custom_login_screen', 'option') == 'yes') {
      // Logo Image
      if (trim(get_field('wpdevhelperLoginScreen-logo_image', 'option')) != '') {
        $loginImage = get_field('wpdevhelperLoginScreen-logo_image', 'option');
        $loginImage = wp_get_attachment_image_src($loginImage, 'full');
        add_action('login_enqueue_scripts', function () use ($loginImage) {
          echo '
          <style type="text/css">
            #login h1 a, .login h1 a {
              background-image: url(\'' . $loginImage[0] . '\');
              background-repeat: no-repeat;
              background-size: 100px;
              width: 100px;
              height: 100px;
              padding-bottom: 30px;
            }
          </style>
          ';
        });
      }
      // Logo URL
      if (trim(get_field('wpdevhelperLoginScreen-logo_url', 'option')) != '') {
        $loginURL = get_field('wpdevhelperLoginScreen-logo_url', 'option');
        add_filter('login_headerurl', function () use ($loginURL) {
          return $loginURL;
        });
      }
      // Logo Title
      if (trim(get_field('wpdevhelperLoginScreen-logo_title', 'option')) != '') {
        $loginTitle = get_field('wpdevhelperLoginScreen-logo_title', 'option');
        add_filter('login_headertext', function () use ($loginTitle) {
          return $loginTitle;
        });
      }
      // Custom Footer
      if (trim(get_field('wpdevhelperLoginScreen-add_footer_content', 'option')) != '') {
        $loginFooter = get_field('wpdevhelperLoginScreen-add_footer_content', 'option');
        add_action('login_footer', function () use ($loginFooter) {
          echo '<div style="border-top: 1px solid #ddd; width:320px; padding:0; margin: 26px auto;">';
          echo '<div style="padding: 15px 26px 0;">' . $loginFooter . '</div>';
          echo '</div>';
        });
      }
      // Login Text Color Style
      if (trim(get_field('wpdevhelperLoginScreen-login_text_color_style', 'option')) != '' && get_field('wpdevhelperLoginScreen-login_text_color_style', 'option') == 'dark') {
        add_action('login_enqueue_scripts', function () {
          echo '<style>
          body.login { color: #fff !important; }
          .login #backtoblog a, .login #nav a { text-decoration: none; color: #fff !important; }
          </style>';
        });
      }
      // Body Background Color
      if (trim(get_field('wpdevhelperLoginScreen-body_background_color', 'option')) != '') {
        $bodyBackgroundColor = get_field('wpdevhelperLoginScreen-body_background_color', 'option');
        add_action('login_enqueue_scripts', function () use ($bodyBackgroundColor) {
          echo "<style>body.login {background-color: $bodyBackgroundColor}</style>";
        });
      }
    }
  }

  /*----------  WP HEAD -> Meta Description  ----------*/
  public function developersWPHeadMetaDescription()
  {
    if (trim(get_field('wpdevhelperWPHead-meta_description', 'option')) != '') {
      add_action('wp_head', function () {
        # Meta Description
        echo '<meta name="description" content="' . get_field('wpdevhelperWPHead-meta_description', 'option') . '">' . "\n";
      }, 0);
    } else {
	    add_action('wp_head', function () {
		    echo '<meta name="description" content="' . get_bloginfo('description') . '">' . "\n";
	    }, 0);
	  }
  }

  /*----------  WP HEAD -> Theme color  ----------*/
  public function developersWPHeadMetaThemeColor()
  {
    if (trim(get_field('wpdevhelperWPHead-theme_color', 'option')) != '') {
      add_action('wp_head', function () {
        # Theme Color
        echo '<meta name="theme-color" content="' . get_field('wpdevhelperWPHead-theme_color', 'option') . '">' . "\n";
        echo '<meta name="msapplication-TileColor" content="' . get_field('wpdevhelperWPHead-theme_color', 'option') . '">';
        echo '<meta name="msapplication-navbutton-color" content="' . get_field('wpdevhelperWPHead-theme_color', 'option') . '">' . "\n\n";
      }, 0);
    }
  }

  /*----------  WP HEAD -> FAVICON / APPLE TOUCH ICON  ----------*/
  public function developersWPHeadFavicon()
  {
    // Verifica se o tema não possui um favicon definido
    if ( empty( get_site_icon_url() ) ) {
      add_action('wp_head', function () {
        # Favicon
        if (trim(get_field('head-icon-favicon', 'option')) != '') {
          $favicon = wp_get_attachment_image_src(get_field('head-icon-favicon', 'option'), 'thumbnail', false);
          echo "\n\n" . '<link rel="shortcut icon" type="image/png" href="' . $favicon[0] . '">' . "\n";
        }
        # Apple Touch Icon iPhone
        if (trim(get_field('head-icon-apple-touch-icon-57x57', 'option')) != '') {
          echo '<link rel="apple-touch-icon" sizes="57x57" href="' . get_field('head-icon-apple-touch-icon-57x57', 'option') . '">' . "\n";
        }
  
        if (trim(get_field('head-icon-apple-touch-icon-60x60', 'option')) != '') {
          echo '<link rel="apple-touch-icon" sizes="60x60" href="' . get_field('head-icon-apple-touch-icon-60x60', 'option') . '">' . "\n";
        }
  
        if (trim(get_field('head-icon-apple-touch-icon-72x72', 'option')) != '') {
          echo '<link rel="apple-touch-icon" sizes="72x72" href="' . get_field('head-icon-apple-touch-icon-72x72', 'option') . '">' . "\n";
        }
  
        if (trim(get_field('head-icon-apple-touch-icon-76x76', 'option')) != '') {
          echo '<link rel="apple-touch-icon" sizes="76x76" href="' . get_field('head-icon-apple-touch-icon-76x76', 'option') . '">' . "\n";
        }
  
        if (trim(get_field('head-icon-apple-touch-icon-114x114', 'option')) != '') {
          echo '<link rel="apple-touch-icon" sizes="114x114" href="' . get_field('head-icon-apple-touch-icon-114x114', 'option') . '">' . "\n";
        }
  
        if (trim(get_field('head-icon-apple-touch-icon-120x120', 'option')) != '') {
          echo '<link rel="apple-touch-icon" sizes="120x120" href="' . get_field('head-icon-apple-touch-icon-120x120', 'option') . '">' . "\n";
        }
  
        if (trim(get_field('head-icon-apple-touch-icon-144x144', 'option')) != '') {
          echo '<link rel="apple-touch-icon" sizes="144x144" href="' . get_field('head-icon-apple-touch-icon-144x144', 'option') . '">' . "\n";
        }
  
        if (trim(get_field('head-icon-apple-touch-icon-152x152', 'option')) != '') {
          echo '<meta name="msapplication-TileImage" content="' . get_field('head-icon-apple-touch-icon-152x152', 'option') . '">
          <link rel="apple-touch-icon" sizes="152x152" href="' . get_field('head-icon-apple-touch-icon-152x152', 'option') . '">' . "\n";
        }
  
        if (trim(get_field('head-icon-apple-touch-icon-180x180', 'option')) != '') {
          echo '<link rel="apple-touch-icon" sizes="180x180" href="' . get_field('head-icon-apple-touch-icon-180x180', 'option') . '">' . "\n";
        }
  
        if (trim(get_field('head-icon-192x192', 'option')) != '') {
          echo '<link rel="icon" type="image/png" sizes="192x192" href="' . get_field('head-icon-192x192', 'option') . '">' . "\n";
        }
  
        if (trim(get_field('head-icon-96x96', 'option')) != '') {
          echo '<link rel="icon" type="image/png" sizes="96x96" href="' . get_field('head-icon-96x96', 'option') . '">' . "\n";
        }
  
        if (trim(get_field('head-icon-16x16', 'option')) != '') {
          echo '<link rel="icon" type="image/png" sizes="16x16" href="' . get_field('head-icon-16x16', 'option') . '">' . "\n";
        }
      }, 2);
    }
  }

  /*----------  WP HEAD -> Open Graph  ----------*/
  public function developersWPHeadOpenGraph() {
    
    // Verifica se alguns plugins populares de SEO não estão ativos, para exibir o Open Graph padrão.
    $has_seo_plugin = false;
    if (is_plugin_active( 'wordpress-seo/wp-seo.php' )) {
      $has_seo_plugin = true;
    }

    if (is_plugin_active('all-in-one-seo-pack/all_in_one_seo_pack.php')) {
      $has_seo_plugin = true;
    }

    if ( ! $has_seo_plugin ) {
      add_action('wp_head', function () {
        # Og Graph
        $tags = [];
        
        // Single pages
        if (is_single()) {
		      $tags['og:type'] = 'article';
			
		      $desc = str_replace('"', '\'', get_the_excerpt());
          $tags['og:description'] = wp_trim_words( $desc, 25, ' ...' );
			
        } elseif ( ! empty(get_field('wpdevhelperWPHead-meta_description', 'option')) ) {
          $tags['og:description'] = get_field('wpdevhelperWPHead-meta_description', 'option');
        }

        // Página inicial
        if ( is_front_page() ) {
          $tags['og:title'] = get_bloginfo('name');
        }

        // Usuário
        if ( is_author() ) {
          $curauth = get_userdata(get_the_author_meta('ID'));
                
          if ( ! empty($curauth->first_name) ) {
            $tags['profile:first_name'] = $curauth->first_name;
            $tags['profile:last_name'] = $curauth->last_name;
          } else {
            $tags['profile:first_name'] = $curauth->display_name;
          }
			
          $tags['og:type'] = 'profile';
        }
  
        // Taxonomia
        if (is_tax()) {
          $tags['og:url'] = get_term_link(get_queried_object(), get_queried_object()->taxonomy);
        }
        
        // Imagens
        if (has_post_thumbnail()) {
          $tags['og:image'] = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), "full")[0];   
          
        } elseif( is_author() ) {

          $id = get_the_author_meta('ID');
          $tags['og:image'] = get_avatar_url( $id, ['size' => 350] );

        } elseif (function_exists('get_field')) {

          if (trim(get_field('head-opengraph-image', 'option')) != '') {
            $tags['og:image'] = esc_url(wp_get_attachment_image_src(get_field('head-opengraph-image', 'option'), 'full', false)[0]);
            $tags['og:image:width'] = '1200';
            $tags['og:image:height'] = '630';
  
          } if ( ! empty(get_site_icon_url()) ) {
          	$tags['og:image'] = get_site_icon_url();
			  
		      } elseif (trim(get_field('head-icon-192x192', 'option')) != '') {
            $tags['og:image'] = esc_url(get_field('head-icon-192x192', 'option'));
          }
        }

        // Para Woocommerce
        if ( class_exists( 'WooCommerce' ) ) {
          $product = wc_get_product( get_the_ID() );
          
          if (is_product()) {
            $tags['og:type'] = 'product';
            $tags['product:plural_title'] = get_the_title();
            $tags['product:price.amount'] = $product->get_price();
            $tags['product:price.currency'] = get_woocommerce_currency();
          }
          
          if (is_account_page()) {
            $tags['og:type'] = 'profile';
          }
        }
        
        // Valores padrão
        $tags = wp_parse_args(
          $tags, 
          [
            'og:description' => get_bloginfo('description'), 
            'og:image'       => '',
            'og:locale'      => get_bloginfo( 'language' ),
            'og:site_name'   => get_bloginfo( 'name' ),
            'og:title'       => trim(wp_title('', false)), 
            'og:type'        => 'website', 
            'og:url'         => get_permalink(), 
          ]
        );
        $tags = array_filter($tags);
        $tags = apply_filters('opengraph_tags', $tags);
  
        foreach ($tags as $property => $content) {
          printf('<meta property="%s" content="%s">', esc_attr($property), esc_attr($content));
        }
      }, 1);
    }
  }

  /*----------  TEMPLATE SETTINGS -> FORM GENERATOR ----------*/
  public function developersTemplateSettingsFormGenerator()
  {
    $theme_name = wp_get_theme()->get( 'Name' );
    $v          = wp_get_theme()->get( 'Version' );

    if (get_field('wpdevhelperTemplateSettings-form_generator', 'option') == 'yes') {

      if ($theme_name == 'WP Starter Theme' && $v < '3.5.4' || $theme_name == 'WP Starter Theme Child' && $v < '1.2') {

        add_action( 'admin_notices', function () {
          $minimum_version = wp_get_theme()->get( 'Name' ) == 'WP Starter Theme' ? '3.5.4' : '1.2';
          echo '<div class="notice notice-warning is-dismissible">';
          echo '<h3>WP Dev Helper v'.WPDEVHELPER_VERSION.'</h3>';
          echo '<p>';
          printf(__('Your current theme needs to be updated to display the <b>Form Generator</b> module. Minimum requested %s version is %s', 'wpdevhelper'), wp_get_theme()->get( 'Name' ), $minimum_version);
          echo '</p></div>';
        } );

      } else {
        require_once(PLUGINPATH . 'includes/form-generator/form.php');
      }
    }
  }

  /*----------  OTHERS -> DUPLICATE  ----------*/
  public function developersOthersDuplicate()
  {
    if (get_field('wpdevhelperOthers-duplicate_post', 'option') == 'yes') {
      add_filter('post_row_actions', 'rd_duplicate_post_link', 10, 2);
    }
    if (get_field('wpdevhelperOthers-duplicate_page', 'option') == 'yes') {
      add_filter('page_row_actions', 'rd_duplicate_post_link', 10, 2);
    }
  }

  /** Remover comentários do dashboard */
  public function developersAdminPanelComments()
  {
    if (get_field('wpdevhelperOthers-remove-comment', 'option') == 'yes') {
      // Removes from admin menu
      function wdh_remove_admin_menus() {
        remove_menu_page( 'edit-comments.php' );
      }

      // Removes from post and pages
      function wpdh_remove_comment_support() {
        remove_post_type_support( 'post', 'comments' );
        remove_post_type_support( 'page', 'comments' );
        
        $postTypes = get_posts('post_type=new_post_type&posts_per_page=-1');
        if (count($postTypes) >= 1) {
          foreach ($postTypes as $postType) { 
            remove_post_type_support( get_field('wpdevhelper-posttype-post_type_key', $postType->ID), 'comments' );            
          }
        }
      }

      // Removes from admin bar
      function wpdh_admin_bar_render() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('comments');
      }

      add_action( 'admin_menu', 'wdh_remove_admin_menus' );
      add_action('init', 'wpdh_remove_comment_support', 100);
      add_action( 'wp_before_admin_bar_render', 'wpdh_admin_bar_render' );
    }
  }

  /*----------  WP HEAD -> Meta tags  ----------*/
  public function developersAdvancedWPHead()
  {
    if (is_array(get_field('wpdevhelperAdvanced-wp_head', 'option'))) {
      # wlwmanifest
      if (in_array('wlwmanifest', get_field('wpdevhelperAdvanced-wp_head', 'option'))) {
        remove_action('wp_head', 'wlwmanifest_link');
      }
      # generator
      if (in_array('generator', get_field('wpdevhelperAdvanced-wp_head', 'option'))) {
        remove_action('wp_head', 'wp_generator');
      }
      # canonical
      if (in_array('canonical', get_field('wpdevhelperAdvanced-wp_head', 'option'))) {
        remove_action('wp_head', 'rel_canonical');
      }
      # shortlink
      if (in_array('shortlink', get_field('wpdevhelperAdvanced-wp_head', 'option'))) {
        remove_action('wp_head', 'wp_shortlink_wp_head');
      }
      # Emoji Scripts
      if (in_array('emoji_scripts', get_field('wpdevhelperAdvanced-wp_head', 'option'))) {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');
      }
      # Disable WordPress REST API
      if (in_array('rest_api', get_field('wpdevhelperAdvanced-wp_head', 'option'))) {
        add_filter('rest_authentication_errors', function ($access) {
          if (!current_user_can('administrator')) {
            return new WP_Error('rest_cannot_access', 'Only authenticated users can access the REST API.', ['status' => rest_authorization_required_code()]);
          }
          return $access;
        });
      }
      # Disable WordPress REST API
      if (in_array('xmlrpc', get_field('wpdevhelperAdvanced-wp_head', 'option'))) {
        add_filter('xmlrpc_enabled', function (): bool {
          return false;
        }); 
        remove_action('wp_head', 'rsd_link');
      }
      # Gutenberg 
      if (in_array('gutenberg', get_field('wpdevhelperAdvanced-wp_head', 'option'))) {
        add_action('wp_print_styles', function (): void {
          wp_dequeue_style('wp-block-library');
          wp_dequeue_style('wp-block-library-theme');
        });
      }
    }
  }

  /*----------  ADVANCED -> CUSTOM CSS  ----------*/
  public function developersAdvancedCustomCSS($wpdhCssCode)
  {
    if (trim($wpdhCssCode) != '') {
      add_action('wp_head', function () {
        global $wpdhCssCode;
        echo '<style>' . $wpdhCssCode . '</style>';
      });
    }
  }
}
