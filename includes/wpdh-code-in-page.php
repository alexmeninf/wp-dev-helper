<?php

if (!defined('ABSPATH'))
  exit; // Exit if accessed directly.


/**
 * Exibir código no head
 */
add_action('wp_head', function () {

  if (have_rows('add_code_inside_head', 'option')) {
    while (have_rows('add_code_inside_head', 'option')) {
      the_row();
      
      // Código
      $code = get_sub_field('code_in_head');

      if (get_sub_field('exibir_em_paginas_especificas') == 'sim') {

        // Lista de páginas para mostrar
        $ids_to_show = get_sub_field('mostrar_somente_em');

        validation_code_to_show($code, $ids_to_show);
      } else {

        echo $code;

      }
    }
  }
}, 100);


/**
 * Exibir código no inicio do body
 */
add_action('wp_body_open', function () {

  if (have_rows('add_code_before_body', 'option')) {
    while (have_rows('add_code_before_body', 'option')) {
      the_row();
      
      // Código
      $code = get_sub_field('code_before_body');

      if (get_sub_field('exibir_em_paginas_especificas') == 'sim') {

        // Lista de páginas para mostrar
        $ids_to_show = get_sub_field('mostrar_somente_em');

        validation_code_to_show($code, $ids_to_show);
      } else {

        echo $code;

      }
    }
  }
}, 0);


/**
 * Exibir código no head
 */
add_action('wp_footer', function () {

  if (have_rows('add_code_after_body', 'option')) {
    while (have_rows('add_code_after_body', 'option')) {
      the_row();
      
      // Código
      $code = get_sub_field('code_after_body');

      if (get_sub_field('exibir_em_paginas_especificas') == 'sim') {

        // Lista de páginas para mostrar
        $ids_to_show = get_sub_field('mostrar_somente_em');

        validation_code_to_show($code, $ids_to_show);
      } else {

        echo $code;

      }
    }
  }
}, 100);


/**
 * Obter id da página por slug
 */
function get_id_by_slug($page_slug) {
  $page = get_page_by_path($page_slug);
  if ($page) {
    return $page->ID;
  } else {
    return null;
  }
}

/**
 * Validar código e mostrar
 */
function validation_code_to_show($code, $ids)
{
  // ID da página atual
  $obj_id = get_queried_object_id();

  if (in_array($obj_id, $ids)) {
    echo $code;

  } elseif ($obj_id == 0) {
    global $wp;

    if (is_front_page()) {
      $obj_id = get_option('page_on_front');
      echo '<script>console.log("Não foi selecionada uma página inicial pelo painel WordPress.")</script>';

    } else {
      $obj_id = get_id_by_slug($wp->request);
    }

    if (in_array($obj_id, $ids)) {
      echo $code;
    }
  }
}


/**
 * Campos personalizados desta página
 */
if( function_exists('acf_add_local_field_group') ):

  acf_add_local_field_group(array(
    'key' => 'group_6272d8726a897',
    'title' => __('Head, Footer and Post Injections', 'wpdevhelper'),
    'fields' => array(
      array(
        'key' => 'field_6272d875d60d9',
        'label' => __('Inside the head tag', 'wpdevhelper'),
        'name' => '',
        'type' => 'tab',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'placement' => 'top',
        'endpoint' => 0,
      ),
      array(
        'key' => 'field_6272da3cef2a9',
        'label' => __('Add code to &lt;head&gt;', 'wpdevhelper'),
        'name' => 'add_code_inside_head',
        'type' => 'repeater',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'collapsed' => '',
        'min' => 1,
        'max' => 0,
        'layout' => 'row',
        'button_label' => '',
        'sub_fields' => array(
          array(
            'key' => 'field_6272d909d60da',
            'label' => __('Code', 'wpdevhelper'),
            'name' => 'code_in_head',
            'type' => 'code',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'mode' => 'htmlmixed',
            'theme' => 'monokai',
          ),
          array(
            'key' => 'field_6272db2297bc0',
            'label' => __('Display on specific pages?', 'wpdevhelper'),
            'name' => 'exibir_em_paginas_especificas',
            'type' => 'select',
            'instructions' => __('Leave unchecked to display on all pages', 'wpdevhelper'),
            'required' => 0,
            'conditional_logic' => array(
              array(
                array(
                  'field' => 'field_6272da3cef2a9',
                  'operator' => '!=empty',
                ),
              ),
            ),
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'choices' => array(
              'sim' => __('Yes', 'wpdevhelper'),
              'nao' => __('No', 'wpdevhelper'),
            ),
            'default_value' => 'nao',
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 0,
            'return_format' => 'value',
            'ajax' => 0,
            'placeholder' => '',
          ),
          array(
            'key' => 'field_6272d973d60dc',
            'label' => __('Add code in', 'wpdevhelper'),
            'name' => 'mostrar_somente_em',
            'type' => 'relationship',
            'instructions' => __('Select the pages to which the code will be added.', 'wpdevhelper'),
            'required' => 1,
            'conditional_logic' => array(
              array(
                array(
                  'field' => 'field_6272db2297bc0',
                  'operator' => '==',
                  'value' => 'sim',
                ),
              ),
            ),
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'post_type' => array(
              0 => 'page',
              1 => 'post',
              2 => 'product',
            ),
            'taxonomy' => '',
            'filters' => array(
              0 => 'search',
            ),
            'elements' => '',
            'min' => 1,
            'max' => '',
            'return_format' => 'id',
          ),
        ),
      ),
      array(
        'key' => 'field_6272dd196b05a',
        'label' => __('Start of body tag', 'wpdevhelper'),
        'name' => '',
        'type' => 'tab',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'placement' => 'top',
        'endpoint' => 0,
      ),
      array(
        'key' => 'field_6272dd2b6b05b',
        'label' => __('Add code after &lt;body&gt;', 'wpdevhelper'),
        'name' => 'add_code_before_body',
        'type' => 'repeater',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'collapsed' => '',
        'min' => 1,
        'max' => 0,
        'layout' => 'row',
        'button_label' => '',
        'sub_fields' => array(
          array(
            'key' => 'field_6272dd2b6b05c',
            'label' => __('Code', 'wpdevhelper'),
            'name' => 'code_before_body',
            'type' => 'code',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'mode' => 'htmlmixed',
            'theme' => 'monokai',
          ),
          array(
            'key' => 'field_6272dd2b6b05d',
            'label' => __('Display on specific pages?', 'wpdevhelper'),
            'name' => 'exibir_em_paginas_especificas',
            'type' => 'select',
            'instructions' => __('Leave unchecked to display on all pages', 'wpdevhelper'),
            'required' => 0,
            'conditional_logic' => array(
              array(
                array(
                  'field' => 'field_6272da3cef2a9',
                  'operator' => '!=empty',
                ),
              ),
            ),
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'choices' => array(
              'sim' => __('Yes', 'wpdevhelper'),
              'nao' => __('No', 'wpdevhelper'),
            ),
            'default_value' => 'nao',
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 0,
            'return_format' => 'value',
            'ajax' => 0,
            'placeholder' => '',
          ),
          array(
            'key' => 'field_6272dd2b6b05e',
            'label' => __('Add code in', 'wpdevhelper'),
            'name' => 'mostrar_somente_em',
            'type' => 'relationship',
            'instructions' => __('Select the pages to which the code will be added.', 'wpdevhelper'),
            'required' => 1,
            'conditional_logic' => array(
              array(
                array(
                  'field' => 'field_6272dd2b6b05d',
                  'operator' => '==',
                  'value' => 'sim',
                ),
              ),
            ),
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'post_type' => array(
              0 => 'post',
              1 => 'page',
              2 => 'product',
            ),
            'taxonomy' => '',
            'filters' => array(
              0 => 'search',
            ),
            'elements' => '',
            'min' => 1,
            'max' => '',
            'return_format' => '',
          ),
        ),
      ),
      array(
        'key' => 'field_6272dd976b05f',
        'label' => __('End of body tag (Footer)', 'wpdevhelper'),
        'name' => '',
        'type' => 'tab',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'placement' => 'top',
        'endpoint' => 0,
      ),
      array(
        'key' => 'field_6272dddf6b060',
        'label' => __('Add code before &lt;/body&gt;', 'wpdevhelper'),
        'name' => 'add_code_after_body',
        'type' => 'repeater',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'collapsed' => '',
        'min' => 1,
        'max' => 0,
        'layout' => 'row',
        'button_label' => '',
        'sub_fields' => array(
          array(
            'key' => 'field_6272dddf6b061',
            'label' => __('Code', 'wpdevhelper'),
            'name' => 'code_after_body',
            'type' => 'code',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'mode' => 'htmlmixed',
            'theme' => 'monokai',
          ),
          array(
            'key' => 'field_6272dddf6b062',
            'label' => __('Display on specific pages?', 'wpdevhelper'),
            'name' => 'exibir_em_paginas_especificas',
            'type' => 'select',
            'instructions' => __('Leave unchecked to display on all pages', 'wpdevhelper'),
            'required' => 0,
            'conditional_logic' => array(
              array(
                array(
                  'field' => 'field_6272da3cef2a9',
                  'operator' => '!=empty',
                ),
              ),
            ),
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'choices' => array(
              'sim' => __('Yes', 'wpdevhelper'),
              'nao' => __('No', 'wpdevhelper'),
            ),
            'default_value' => 'nao',
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 0,
            'return_format' => 'value',
            'ajax' => 0,
            'placeholder' => '',
          ),
          array(
            'key' => 'field_6272dddf6b063',
            'label' => __('Add code in', 'wpdevhelper'),
            'name' => 'mostrar_somente_em',
            'type' => 'relationship',
            'instructions' => __('Select the pages to which the code will be added.', 'wpdevhelper'),
            'required' => 1,
            'conditional_logic' => array(
              array(
                array(
                  'field' => 'field_6272dddf6b062',
                  'operator' => '==',
                  'value' => 'sim',
                ),
              ),
            ),
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'post_type' => array(
              0 => 'post',
              1 => 'page',
              2 => 'product',
            ),
            'taxonomy' => '',
            'filters' => array(
              0 => 'search',
            ),
            'elements' => '',
            'min' => 1,
            'max' => '',
            'return_format' => 'id',
          ),
        ),
      ),
    ),
    'location' => array(
      array(
        array(
          'param' => 'options_page',
          'operator' => '==',
          'value' => 'wpdh-injections',
        ),
      ),
    ),
    'menu_order' => 2,
    'position' => 'normal',
    'style' => 'seamless',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => array(
      0 => 'permalink',
      1 => 'the_content',
      2 => 'excerpt',
      3 => 'discussion',
      4 => 'comments',
      5 => 'revisions',
      6 => 'slug',
      7 => 'author',
      8 => 'format',
      9 => 'page_attributes',
      10 => 'featured_image',
      11 => 'categories',
      12 => 'tags',
      13 => 'send-trackbacks',
    ),
    'active' => true,
    'description' => '',
  ));
  
endif;