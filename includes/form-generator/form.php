<?php

if (!defined('ABSPATH'))
  exit;

require PLUGINPATH . '/includes/form-generator/acf-options.php';
require PLUGINPATH . '/includes/form-generator/template-form.php';

/**
 *
 * Register Custom Post Type
 *
 */
function post_type_generator_form()
{
  $labels = array(
    'name'                  => __('Forms', 'wpdevhelper'),
    'singular_name'         => __('Form', 'wpdevhelper'),
    'menu_name'             => __('Forms', 'wpdevhelper'),
    'all_items'             => __('All Forms', 'wpdevhelper'),
    'add_new_item'          => __('Add new form', 'wpdevhelper'),
    'new_item'              => __('New form', 'wpdevhelper'),
    'edit_item'             => __('Edit form', 'wpdevhelper'),
    'update_item'           => __('Update form', 'wpdevhelper'),
    'search_items'          => __('Search form', 'wpdevhelper'),
  );

  $args = array(
    'label'                 => __('Form', 'wpdevhelper'),
    'description'           => __('Add a new submission form.', 'wpdevhelper'),
    'labels'                => $labels,
    'supports'              => array('title', 'custom-fields'),
    'hierarchical'          => false,
    'public'                => true,
    'show_ui'               => true,
    'show_in_menu'          => true,
    'menu_position'         => 80,
    'menu_icon'             => PLUGINROOT . '/assets/img/form.svg',
    'show_in_admin_bar'     => false,
    'show_in_nav_menus'     => false,
    'can_export'            => false,
    'has_archive'           => false,
    'exclude_from_search'   => true,
    'publicly_queryable'    => true,
    'rewrite'               => false,
    'capability_type'       => 'post',
  );

  register_post_type('generator_form', $args);
}

add_action('init', 'post_type_generator_form', 0);


/**
 *
 * Display custom column shortcode
 *
 * */
add_filter('manage_generator_form_posts_columns', 'set_custom_edit_generator_form_columns');
add_action('manage_generator_form_posts_custom_column', 'custom_generator_form_column', 10, 2);

function set_custom_edit_generator_form_columns($columns)
{
  $columns['shortcode'] = __('Shortcode', 'wpdevhelper');

  return $columns;
}

function custom_generator_form_column($column, $post_id)
{
  switch ($column) {
    case 'shortcode':
      echo '<input type="text" onfocus="this.select();" readonly="readonly" value="[form_template id=' . esc_attr($post_id) . ']" class="code-input-post">';
      echo '<input type="text" onfocus="this.select();" readonly="readonly" value="&#60;?php echo do_shortcode(\'[form_template id=' . esc_attr($post_id) . ']\'); ?&#62;" class="code-input-post">';
      break;
  }
}


/**
 *
 * Register shortcode
 *
 */
function theme_form_shortcode($atts, $content = null, $tag)
{
  $html = '';

  extract(shortcode_atts(array(
    'id' => null
  ), $atts));

  $args = array(
    'post_type'      => 'generator_form',
    'p'              => $id,
    'posts_per_page' => 1,
  );

  $query = new  WP_Query($args);

  if ($query) :
    while ($query->have_posts()) :
      $query->the_post();

      $html .= wpdh_get_form_code();

    endwhile;

    $html .= wpdh_js_clear_input();

  endif;

  wp_reset_query();

  return $html;
}

add_shortcode('form_template', 'theme_form_shortcode');


/**
 *
 * Verifica a quantidade de shortcodes na página
 *
 */
function mf_count_shortcode_in_page()
{
  global $page;
  static $i = 1;

  $spp = 100; // Limite de shortcodes na página.
  $ii = $i + (($page - 1) * $spp);
  $quantity = $ii;
  $i++;

  return $quantity;
}


/**
 *
 * Scripts necessários para envio e validação.
 *
 */
function wpdh_js_clear_input()
{

  if (mf_count_shortcode_in_page() == 1) :

    return "<script>
      function clear_form_elements(parentClass) {
        $(parentClass).find(':input').each(function() {
          switch (this.type) {
            case 'password':
            case 'text':
            case 'url':
            case 'textarea':
            case 'file':
            case 'select-one':
            case 'select-multiple':
            case 'date':
            case 'number':
            case 'tel':
            case 'email':
              $(this).val('');
              break;
            case 'checkbox':
            case 'radio':
              this.checked = false;
              break;
          }
        });
      }
    </script>";

  endif;

  return false;
}


/**
 * input
 *
 * @param  string  $name             - Título da input
 * @param  string  $id               - Identificador
 * @param  string  $type             - Tipo do campo
 * @param  boolean $is_required      - Se é obrigatório ou não
 * @param  string  $value            - Valor padrão
 * @param  string  $custom_class     - Classe customizada
 * @param  string  $attributes       - Atributos para a tag do campo
 * @param  boolean $enable_parameter - Permitir receber valores pela URL
 * @return void
 */
function input($name, $id, $type, $is_required = false, $value = '', $custom_class = '', $attributes = '', $enable_parameter = false)
{
  $html = '';

  // Veirificar valores
  $required = $is_required ? 'required' : '';

  $html_required = $is_required ? '<sup class="text-danger">*</sup>' : '';

  // Verificar parâmetros passado na url ou pelo banco de dados
  // Todo paramentro na url do formulário deve ser no padrão: f[nome_do_campo], para evitar confitos.
  if ($enable_parameter) {
    $received_parameter = isset($_GET['f' . $id]) && !empty($_GET['f' . $id]) ? esc_attr($_GET['f' . $id]) : esc_attr($value);
  } else {
    $received_parameter = esc_attr($value);
  }

  if ($type == 'hidden') :

    $html .= '<input type="hidden" id="' . $id . '" name="' . $id . '" value="' . $received_parameter . '" ' . $attributes . '>';

  elseif ($type == 'textarea') :

    $html .= '<label class="form-group ' . $custom_class . '">
      <textarea id="' . $id . '" name="' . $id . '" placeholder="&nbsp;" ' . $required . ' ' . $attributes . '>' . $received_parameter . '</textarea>
      <span class="txt">
        ' . $name . '
        ' . $html_required . '
      </span>
      <span class="bar"></span>
    </label>';

  elseif ($type == 'select') :

    $options = split_options($value);

    $html .= '<select id="' . $id . '" name="' . $id . '" class="' . $custom_class . '">';

    // Verificar se recebe seleção pela url
    $has_param = false;
    $has_any_selected = false;
    foreach ($options as $values) {
      $args = preg_split('/\:/', $values);
      $attr = array_key_exists(2, $args) ? $args[2] : '';

      if ($enable_parameter) {
        if (isset($_GET['f' . $id]) && !empty($_GET['f' . $id]) && $_GET['f' . $id] == trim($args[0])) {
          $has_param = true;
        }
      }

      if (strpos($attr, 'selected') === true) {
        $has_any_selected = true;
      }
    }

    // Exibir opções
    foreach ($options as $i => $values) {
      // Separar cada campo da opção
      $args = preg_split('/\:/', $values);
      // Atributos
      $attr = array_key_exists(2, $args) ? $args[2] : '';

      if ($has_param) {
        if ($_GET['f' . $id] == trim($args[0])) {
          // Adicionar seleção
          if (strpos($attr, 'selected') === false) {
            $attr = $attr . ' selected';
          }
        } else {
          // remover seleção de outros
          $attr = str_replace('selected="selected"', '', $attr);
          $attr = str_replace('selected', '', $attr);
        }
      }

      if ($i === 0) {
        $op_default = !$has_param && !$has_any_selected ? 'selected' : '';
        $html .= '<option value="" disabled ' . $op_default . '>' . $name . '</option>';
      }

      // Exibir opção
      $html .= '<option value="' . trim($args[0]) . '" ' . trim($attr) . '>' . trim($args[1]) . '</option>';
    }
    $html .= '</select>';

  elseif ($type == 'radio' || $type == 'checkbox') :

    $options = split_options($value);

    // Verificar se botão foi checado pela url
    $has_param = false;
    if ($enable_parameter) {
      foreach ($options as $values) {
        $args = preg_split('/\:/', $values);
        $attr = array_key_exists(2, $args) ? $args[2] : '';

        if (isset($_GET['f' . $id]) && !empty($_GET['f' . $id]) && $_GET['f' . $id] == trim($args[0])) {
          $has_param = true;
        }
      }
    }

    $html .= '<div class="title-input-' . $type . '">' . $name . ' ' . $html_required . '</div>';

    foreach ($options as $i => $values) {
      // Separar cada campo da opção
      $args = preg_split('/\:/', $values);
      // Atributos
      $attr = array_key_exists(2, $args) ? $args[2] : '';

      if ($has_param) {
        if ($_GET['f' . $id] == trim($args[0])) {
          // Verificar
          if (strpos($attr, 'checked') === false) {
            $attr = $attr . ' checked';
          }
        } else {
          // remover verificação de outros
          $attr = str_replace('checked="checked"', '', $attr);
          $attr = str_replace('checked', '', $attr);
        }
      }

      // Salva no value da input um array com o valor e o nome formatado
      $valueInput = htmlspecialchars(
        json_encode(
          array(
            'value' => trim($args[0]),
            'name' => trim($args[1]),
          )
        ),
        ENT_QUOTES,
        'UTF-8'
      );

      // Exibir opção
      $html .= '<div class="form-check ' . $type . ' ' . $custom_class . '">';
      $html .= '<input type="' . $type . '" id="' . $id . $i . '" value="' . $valueInput . '" name="' . $id . '" class="form-check-input" ' . $attr . '>';
      $html .= '<label class="form-check-label" for="' . $id . $i . '">' . trim($args[1]) . '</label>';
      $html .= '</div>';
    }

  else :

    $html .= '<label class="form-group ' . $custom_class . '">
      <input type="' . $type . '" id="' . $id . '" name="' . $id . '" value="' . $received_parameter . '" placeholder="&nbsp;" ' . $required . ' ' . $attributes . '>
      <span class="txt">
        ' . $name . '
        ' . $html_required . '
      </span>
      <span class="bar"></span>
    </label>';

  endif;

  return $html;
}


/**
 * split_options
 *
 * @param  mixed $values
 * @return array
 */
function split_options($values)
{
  // remover mais de dois espaços vazios
  $options = preg_replace('/(\s)+/s', '\\1', $values);
  // Obter cada linha
  $options = preg_split('/\r\n|\r|\n|\s{2,}/', $options);
  // Remove espaços em branco
  $options = preg_replace('/\n/', '', $options);
  // Fiiltrar linhas vazias
  $options = array_filter($options, function ($a) {
    return $a !== "";
  });

  return $options;
}
