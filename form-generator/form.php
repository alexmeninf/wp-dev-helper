<?php 

if ( ! defined( 'ABSPATH' ) ) 
	exit;

require 'acf-options.php';

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
    'menu_icon'             => PLUGINROOT . '/assets/img/admin-icon-form.png',
    'show_in_admin_bar'     => true,
    'show_in_nav_menus'     => true,
    'can_export'            => true,
    'has_archive'           => true,
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
      echo '<input type="text" onfocus="this.select();" readonly="readonly" value="[form_template id=' . esc_attr($post_id) . ']" class="code">';
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

      require PLUGINPATH . '/form-generator/template-form.php';

    endwhile;

    mf_js_scripts();

  endif;

  wp_reset_query();
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
function mf_js_scripts()
{

  if (mf_count_shortcode_in_page() == 1) :
?>
    <script>
      function clear_form_elements(parentClass) {
        $(parentClass).find(':input').each(function() {
          switch (this.type) {
            case 'password':
            case 'text':
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
    </script>
  <?php
  endif;
}


/**
 * 
 * Generate input
 * 
 */
function input($name, $id, $type, $is_required = false, $value = '', $custom_class = '', $attributes = '')
{

  // Veirificar valores
  $required = $is_required ? 'required' : '';

  // Verificar parametros passado na url ou pelo banco de dados
  // Todo paramentro na url do formulário deve ser no padrão: f[nome_do_campo], para evitar confitos.
  $received_parameter = isset($_GET['f' . $id]) && !empty($_GET['f' . $id]) ? esc_attr($_GET['f' . $id]) : $value;

  if ($type == 'hidden') : ?>

    <input type="hidden" id="<?= $id ?>" name="<?= $id ?>" value="<?= $received_parameter ?>" <?= $attributes ?>>

  <?php elseif ($type == 'textarea') : ?>

    <label class="form-group <?= $custom_class ?>">
      <textarea id="<?= $id ?>" name="<?= $id ?>" placeholder="&nbsp;" <?= $required ?> <?= $attributes ?>><?= $received_parameter ?></textarea>
      <span class="txt">
        <?= $name ?>
        <?= $is_required ? '<sup class="text-danger">*</sup>' : '' ?>
      </span>
      <span class="bar"></span>
    </label>

  <?php elseif ($type == 'select') :

    $options = split_options($value);

    echo '<select id="' . $id . '" name="' . $id . '" class="' . $custom_class . '">';

    // Verificar se recebe seleção pela url
    $has_param = false;
    $has_any_selected = false;
    foreach ($options as $values) {
      $args = preg_split('/\:/', $values);
      $attr = array_key_exists(2, $args) ? $args[2] : '';

      if (isset($_GET['f' . $id]) && !empty($_GET['f' . $id]) && $_GET['f' . $id] == trim($args[0])) {
        $has_param = true;
      }

      if (strpos($attr, 'selected') === true) {
        $has_any_selected = true;
      }
    }

    // Exibir opções
    foreach ($options as $i=>$values) {
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
        echo '<option value="" disabled '.$op_default.'>' . $name . '</option>';
      }
      
      // Exibir opção
      echo '<option value="' . trim($args[0]) . '" ' . trim($attr) . '>' . trim($args[1]) . '</option>';
    }
    echo '</select>';

  elseif ($type == 'radio') :

    $options = split_options($value);

    // Verificar se botão foi checado pela url
    $has_param = false;
    foreach ($options as $values) {
      $args = preg_split('/\:/', $values);
      $attr = array_key_exists(2, $args) ? $args[2] : '';

      if (isset($_GET['f' . $id]) && !empty($_GET['f' . $id]) && $_GET['f' . $id] == trim($args[0])) {
        $has_param = true;
      }
    }

    foreach ($options as $i=>$values) {
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

      // Exibir opção
      echo '<div class="form-check radio ' . $custom_class . '">';
      echo '<input type="radio" id="' . $id . $i . '" value="' . trim($args[0]) . '" name="' . $id . '" class="form-check-input" ' . $attr . '>';
      echo '<label class="form-check-label" for="' . trim($args[0]) . '">' . trim($args[1]) . '</label>';
      echo '</div>';
    }

  else : ?>

    <label class="form-group <?= $custom_class ?>">
      <input type="<?= $type ?>" id="<?= $id ?>" name="<?= $id ?>" value="<?= $received_parameter ?>" placeholder="&nbsp;" <?= $required ?> <?= $attributes ?>>
      <span class="txt">
        <?= $name ?>
        <?= $is_required ? '<sup class="text-danger">*</sup>' : '' ?>
      </span>
      <span class="bar"></span>
    </label>
<?php
  endif;
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
