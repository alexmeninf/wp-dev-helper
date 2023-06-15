<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

add_action('wp_head', function () {

  echo '<script>const ajaxURL = \'' . esc_js(admin_url('admin-ajax.php')) . '\';</script>';
}, 60);

function wpdh_get_form_code()
{

  global $post;

  $html = '';
  $field_list = array('values' => array());

  /**
   * API para envio de e-mail.
   * Para enviar apenas email: THEMEROOT . '/inc/forms/PHPMailer/send.php'
   */
  $api = get_field('api_url') != '' ? '\'' . esc_url(get_field('api_url')) . '\'' : 'ajaxURL';

  // parametros para enviar para a função no tema.
  $parameters_data = trim(get_field('api_url')) != '' ? '' : 'action: \'save_new_form_data\',';

  // Classes do formulário
  $form_class = get_field('custom_form_class') ? get_field('custom_form_class') : '';

  // Texto do botão
  $text_btn = get_field('button_text') ? get_field('button_text') : __('Send', 'wpdevhelper');

  // Assunto do e-mail
  $subject = get_field('subject_email') ? "'" . get_field('subject_email') . "'" : "'Novo e-mail recebido'";

  // Ajax data com os dados do formulário
  $ajax_data = get_field('ajax_data') ? trim(esc_attr(get_field('ajax_data'))) : '';

  // Localização do formulário
  $location = get_field('form_location');

  /**
   * Gerar formulário
   */
  if (have_rows('campos')) :

    $html .= '<form ';
    $html .= 'class="' . esc_attr($form_class) . ' ' . esc_attr(get_field('form_style')) . '" ';
    $html .= 'id="form-theme-' . get_the_ID() . '" ';
    $html .= 'name="form' . get_the_ID() . '" ';
    $html .= 'action="javascript:void(0);" ';
    $html .= 'method="POST">';

    while (have_rows('campos')) :
      the_row();

      if (get_sub_field('display') == 'input') :

        $input_name   = get_sub_field('input_name');
        $input_id     = get_sub_field('input_id');
        $input_type   = get_sub_field('input_type');
        $is_required  = get_sub_field('is_required');
        $input_value  = get_sub_field('input_value');
        $custom_class = get_sub_field('class_name');
        $attributes   = get_sub_field('attributes');
        $enable_parameter = get_field('enable_received_parameter');

        array_push($field_list['values'], $input_name);

        $html .= input($input_name, $input_id, $input_type, $is_required, $input_value, $custom_class, $attributes, $enable_parameter);
      else :

        $html .= '<div class="title-form-group">' . get_sub_field('group_title') . '</div>';

        if (get_sub_field('enable_description_group')) :
          $html .= '<div class="description-form-group">' . get_sub_field('group_description') . '</div>';
        endif;

      endif;
    endwhile;

    // Lista com o título/nome de todos os campos.
    $html .= input(null, 'field_list', 'hidden', true, htmlspecialchars(json_encode($field_list), ENT_QUOTES, 'UTF-8'));

    $html .= '<button type="submit" class="' . esc_attr(get_field('button_class')) . '">
          <span>' . $text_btn . '</span>
        </button>
      </form>';

    // Abertura do script
    $html .= "<script>const form" . get_the_ID() . " = '#form-theme-" . get_the_ID() . "';";
    $html .= "$(form" . get_the_ID() . ").find('input, select, textarea').prop('required', false);";
    $html .= "$(form" . get_the_ID() . ").on('submit', function(e) {";
    $html .= "e.preventDefault();";
    $html .= "
      getAllCheckedValue = (input) => {
        if ($(input).length <= 1) return $(input).val();

        const values = [];
        $(input).each(function () {
          const self = $(this);
          if (self.is(':checked')) {
            values.push(JSON.parse(self.val()));
          }
        });
        return values;
      }
    ";

    // Verificar se existe algum campo de arquivo
    $has_file = false;

    while (have_rows('campos')) :
      the_row();

      if (get_sub_field('display') == 'title') continue;

      $id = get_sub_field('input_id');
      $input_type  = get_sub_field('input_type');

      // File type
      if ($input_type == 'file') :
        $has_file = true;
        $html .= "const files_" . $id . " = $('input[name=" . $id . "]')[0].files;";
      // Radio type
      elseif ($input_type == 'radio') :
        $html .= "const " . $id . " = !!$('input[name=" . $id . "]:checked').val() ? $('input[name=" . $id . "]:checked').val() : '';";
      // Checkbox
      elseif ($input_type == 'checkbox') :
        $html .= "const " . $id . " = !!$('input[name=" . $id . "]:checked').val() ? $('input[name=" . $id . "]:checked').val() : '';";
      // Select type
      elseif ($input_type == 'select') :
        $html .= "const " . $id . " = $(this).find('select[name=" . $id . "] option').filter(':selected').val();";
      // Other types
      else :
        $html .= "const " . $id . " = $(this).find('[name=" . $id . "]').val();";
      endif;
    endwhile;

    // Validate values
    $count_requireds = 0;

    while (have_rows('campos')) :
      the_row();

      if (get_sub_field('display') == 'title') continue;

      if (get_sub_field('is_required')) :
        $id   = get_sub_field('input_id');
        $name = get_sub_field('input_name');
        $input_type  = get_sub_field('input_type');

        if ($input_type == 'file') :
          $html .= "if (files_" . $id . ".length === 0) {
            Swal.fire({
              type: 'warning',
              title: 'Oops...',
              html: '" . sprintf(__("Add the file in the <b>%s</b> field.", "wpdevhelper"), $name) . "'
            });
          } else ";

        else :

          $html .= "if (" . $id . ".trim() == '') {
            Swal.fire({
              type: 'warning',
              title: 'Oops...',
              html: '" . sprintf(__("The <b>%s</b> field is required.", "wpdevhelper"), $name) . "'
            });
          } else ";

        endif;
        $count_requireds++;
      endif;
    endwhile;

    $html .= $count_requireds > 0 ? '{' : '';

    // Send informations
    $html .= "const btnForm = form" . get_the_ID() . " + ' button[type=submit]';";

    if ($has_file) :
      $html .= "const formData = new FormData();";

      while (have_rows('campos')) :
        the_row();

        if (get_sub_field('display') == 'title') continue;

        $id = get_sub_field('input_id');
        $input_type  = get_sub_field('input_type');

        // Verificar o tipo da input
        if ($input_type == 'file') :
          $html .= "
            $.each(files_" . $id . ", function(i, file) {
              formData.append('files[]', file, file.name);
            });";
        else :
          $html .= "formData.append('" . $id . "', " . $id . ");";
        endif;
      endwhile;
    else :
      $html .= "const serializeData = $(form" . get_the_ID() . ").serializeArray();";
      $html .= "
        let formData;
        $.each(serializeData, function(i, field) {
          const value = getAllCheckedValue('[name=\"'+field.name+'\"]');
          formData = {...formData, [field.name]: value};
        });
        ";
    endif;

    // Parametros para arquivo
    $file = '';
    if ($has_file) :
      $file = 'processData: false, contentType: false,';
    endif;

    $enableSendEmail = '';
    if (get_field('enable_send_email')) {
      $enableSendEmail = "enable_email: true, subject: " . $subject . ",";
    }

    $html .= "$.ajax({
      url: " . $api . ",
      method: 'POST',
      " . $file . "
      data: {
        " . $parameters_data . "
        data: {...formData},
        form_id: " . get_the_ID() . ",
        location: '" . $location . "',
        " . $enableSendEmail . "
        " . $ajax_data . "
      },
      beforeSend: () => {
        $(btnForm).html('" . __("Sending...", "wpdevhelper") . "');
      }
    }).done(function(data) {
      const response = JSON.parse(data);

      if (response.success) {
        Swal.fire({
          title: '" . __("Sent!", "wpdevhelper") . "',
          html: `\${response.message}`,
          type: 'success',
          confirmButtonText: '" . __("Close", "wpdevhelper") . "'
        });
      } else {
        Swal.fire({
          title: '" . __("Something went wrong!", "wpdevhelper") . "',
          html: `\${response.message}`,
          type: 'error',
          confirmButtonText: 'Ok'
        });
      }

      clear_form_elements(form" . get_the_ID() . ");

    }).fail(function(data) {
      const response = JSON.parse(data);

      Swal.fire({
        title: '" . __("Something went wrong!", "wpdevhelper") . "',
        html: `\${response.message}`,
        type: 'error',
        confirmButtonText: 'Ok'
      });

    }).always(function() {
      $(btnForm).html('" . $text_btn . "');
    });";

    $html .= $count_requireds > 0 ? '}' : '';
    $html .= "});"; // on submit
    $html .= "</script>";

  endif;

  return $html;
}
