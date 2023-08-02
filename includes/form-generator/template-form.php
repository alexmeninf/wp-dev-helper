<?php

/**
 * @version 4.1.0 - This file version
 */

// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

add_action('wp_head', function () {

  echo '<script>const ajaxURL = \'' . esc_js(admin_url('admin-ajax.php')) . '\';</script>';
}, 60);

function wpdh_get_form_code()
{
  global $location_page;
  global $post;

  $html = '';
  $field_list = array('fields' => array());
  $has_file = false;

  /**
   * API para envio de e-mail.
   * Para enviar apenas email: THEMEROOT . '/inc/forms/PHPMailer/send.php'
   */
  $api = get_field('api_url') != '' ? '\'' . esc_url(get_field('api_url')) . '\'' : 'ajaxURL';

  // parametros para enviar para a função no tema.
  $actionName = '\'save_new_form_data\'';
  $parameters_data = trim(get_field('api_url')) != '' ? '' : 'action: ' . $actionName . ',';

  // Classes do formulário
  $form_class = get_field('custom_form_class') ? get_field('custom_form_class') : '';

  // Texto do botão
  $text_btn = get_field('button_text') ? get_field('button_text') : __('Send', 'wpdevhelper');

  // Assunto do e-mail
  $subject = get_field('subject_email') ? "'" . get_field('subject_email') . "'" : "'Novo e-mail recebido'";

  // Ajax data com os dados do formulário
  // Não é permitida quando possui input tipo file no formulário, pois precisa usar o "append".
  $ajax_data = get_field('ajax_data') ? trim(esc_attr(get_field('ajax_data'))) : '';

  // Localização do formulário
  $location = $location_page ?: get_field('form_location');

  // permitir receber valores pela url
  $enable_parameter = get_field('enable_received_parameter');

  // Exibe o tooltip na input
  $enable_tooltip = get_field('enable_tooltip');

  /**
   * Gerar formulário
   */
  if (have_rows('campos')) :

    while (have_rows('campos')) :
      the_row();
      if (get_sub_field('input_type') == 'file') $has_file = true;
    endwhile;

    if (is_user_logged_in() && current_user_can('edit_posts')) :
      $html .= '<a href="'. get_edit_post_link() .'" class="btn-theme btn-small mb-3" target="_blank">
        <i class="fa-light fa-pen-to-square me-2"></i>
         '. __("Edit Form", "wpdevhelper") .'
      </a>';
    endif;

    $html .= '<form ';
    $html .= 'class="' . esc_attr($form_class) . ' ' . esc_attr(get_field('form_style')) . '" ';
    $html .= 'id="form-theme-' . get_the_ID() . '" ';
    $html .= 'name="form' . get_the_ID() . '" ';
    $html .= 'action="javascript:void(0);" ';
    $html .= $has_file ? 'enctype="multipart/form-data" ' : '';
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
        $upload_multiple_files = get_sub_field('upload_multiple_files');
        $switchInput = get_sub_field('enable_ui_switch');
        $inline_options_ui = get_sub_field('inline_options_ui');

        array_push(
          $field_list['fields'],
          array(
            'value' => $input_id,
            'name' => $input_name
          )
        );

        $html .= input($input_name, $input_id, $input_type, $is_required, $input_value, $custom_class, $attributes, $enable_parameter, $upload_multiple_files, $switchInput, $inline_options_ui, $enable_tooltip);
      else :

        $html .= '<div class="title-form-group">' . get_sub_field('group_title') . '</div>';

        if (get_sub_field('enable_description_group')) :
          $html .= '<div class="description-form-group">' . get_sub_field('group_description') . '</div>';
        endif;

      endif;
    endwhile;

    // Lista com o título/nome de todos os campos.
    $html .= input(null, 'field_list', 'hidden', true, htmlspecialchars(json_encode($field_list, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'));

    $html .= '<button type="submit" class="' . esc_attr(get_field('button_class')) . '">
          <span>' . $text_btn . '</span>
        </button>
        <button type="reset" class="reset-button" style="display: none">
          <span>' . __('Clear Form', 'wpdevhelper') . '</span>
        </button>
      </form>';

    // Abertura do script
    $html .= "<script>";
    $html .= "const form" . get_the_ID() . " = '#form-theme-" . get_the_ID() . "';";
    $html .= "const resetButton = $(form" . get_the_ID() . " + ' .reset-button');";
    $html .= "
    $(form" . get_the_ID() . " + ' :input').on('keyup click change', function() {
      toggleResetButton(form" . get_the_ID() . ", resetButton);
    });
    resetButton.on('click', function() {
      resetButton.css('display', 'none');
    });
    ";

    $html .= "$(form" . get_the_ID() . ").find('input, select, textarea').prop('required', false);";
    $html .= "$(form" . get_the_ID() . ").on('submit', function(e) {";
    $html .= "e.preventDefault();";

    while (have_rows('campos')) :
      the_row();

      if (get_sub_field('display') == 'title') continue;

      $id          = get_sub_field('input_id');
      $input_type  = get_sub_field('input_type');

      $input_name = wpdh_get_field_name($input_type, $id);

      // File type
      if ($input_type == 'file') :
        $html .= "const files_" . $id . " = $('" . $input_name . "')[0].files;";
      // Radio type
      elseif ($input_type == 'radio') :
        $html .= "const " . $id . " = !!$('" . $input_name . ":checked').val() ? $('" . $input_name . ":checked').val() : '';";
      // Checkbox
      elseif ($input_type == 'checkbox') :
        $html .= "const " . $id . " = !!$('" . $input_name . ":checked').val() ? $('" . $input_name . ":checked').val() : '';";
      // Select type
      elseif ($input_type == 'select') :
        $html .= "const " . $id . " = $(this).find('" . $input_name . " option').filter(':selected').val();";
      // Other types
      else :
        $html .= "const " . $id . " = $(this).find('" . $input_name . "').val();";
      endif;
    endwhile;

    // Validate values
    $count_requireds = 0;

    while (have_rows('campos')) :
      the_row();

      if (get_sub_field('display') == 'title') continue;

      $id   = get_sub_field('input_id');
      $name = get_sub_field('input_name');
      $input_type  = get_sub_field('input_type');

      $input_name = wpdh_get_field_name($input_type, $id);

      if (get_sub_field('is_required')) :
        if ($input_type == 'file') :
          $html .= "if (files_" . $id . ".length === 0) {
            scrollToElement('" . $input_name . "')
            Swal.fire({
              type: 'warning',
              title: 'Oops...',
              html: '" . sprintf(__("Add the file in the <b>%s</b> field.", "wpdevhelper"), $name) . "'
            });
          } else ";

        else :

          $html .= "if (" . $id . ".trim() == '') {
            scrollToElement('" . $input_name . "', '" . $id . "', '" . $input_type . "')
            Swal.fire({
              type: 'warning',
              title: 'Oops...',
              html: '" . sprintf(__("The <b>%s</b> field is required.", "wpdevhelper"), $name) . "'
            });
          } else ";

        endif;
        $count_requireds++;
      endif;

      if ($input_type == 'file') :
        $upload_multiple_files = get_sub_field('upload_multiple_files');
        $upload_max_files = get_sub_field('upload_max_files');

        if ($upload_multiple_files) :
          $html .= "if (files_" . $id . ".length > " . $upload_max_files . ") {
            Swal.fire({
              type: 'warning',
              title: 'Oops...',
              html: '" . sprintf(__("You can only upload a maximum of <b>%s</b> files", "wpdevhelper"), $upload_max_files) . "'
            });
          } else ";
        endif;
      endif;
    endwhile;

    $html .= $count_requireds > 0 ? '{' : '';

    // Send informations
    $html .= "const btnForm = form" . get_the_ID() . " + ' button[type=submit]';";
    $html .= "const serializeData = $(form" . get_the_ID() . ").serializeArray();";

    // Parametros ajax
    $paramsFile = '';
    $dataAjax = '';
    $enableSendEmail = '';

    if (get_field('enable_send_email')) {
      if ($has_file) {
        $enableSendEmail  = "formData.append('enable_email', true);";
        $enableSendEmail .= "formData.append('subject', " . $subject . ");";
      } else {
        $enableSendEmail = "enable_email: true, subject: " . $subject . ",";
      }
    }

    if ($has_file) :
      $html .= "const formData = new FormData();";
      $html .= "
        let fields;
        $.each(serializeData, function(i, field) {
          const value = getAllCheckedValue('[name=\"'+field.name+'\"]');
          fields = {...fields, [field.name]: value};
        });
        formData.append('formData', JSON.stringify(fields));
      ";

      while (have_rows('campos')) :
        the_row();

        if (get_sub_field('display') == 'title') continue;

        $id = get_sub_field('input_id');
        $input_type  = get_sub_field('input_type');

        // Verificar o tipo da input
        if ($input_type == 'file') :
          $upload_multiple_files = get_sub_field('upload_multiple_files');
          $upload_max_files = $upload_multiple_files ? get_sub_field('upload_max_files') : 1;

          // TODO: Precisa de melhoria para multiplias inputs com esse dado diferente
          $html .= "formData.append('upload_max_files', " . $upload_max_files . ");";

          $html .= "
            $.each(files_" . $id . ", function(i, file) {
              formData.append('files[]', file, file.name);
            });";
        endif;
      endwhile;

      $paramsFile = 'processData: false, contentType: false, ';
      $html .= 'formData.append(\'action\', ' . $actionName . ');';
      $html .= 'formData.append(\'form_id\', ' . get_the_ID() . ');';
      $html .= 'formData.append(\'location\', \'' . $location . '\');';
      $html .= $enableSendEmail;
      $dataAjax = 'formData';

    else :

      $html .= "
        let fields;
        $.each(serializeData, function(i, field) {
          const value = getAllCheckedValue('[name=\"'+field.name+'\"]');
          fields = {...fields, [field.name]: value};
        });
      ";
      $dataAjax = "{
        " . $parameters_data . "
        formData: {...fields},
        form_id: " . get_the_ID() . ",
        location: '" . $location . "',
        " . $enableSendEmail . "
        " . $ajax_data . "
      }";
    endif;

    $html .= "$.ajax({
      url: " . $api . ",
      method: 'POST',
      " . $paramsFile . "
      data: " . $dataAjax . ",
      beforeSend: () => {
        $(btnForm).html('<i class=\"fa-duotone fa-loader fa-spin icon-before\"></i> " . __("Sending...", "wpdevhelper") . "');
        $(btnForm).attr('disabled', true);
      }
    }).done(function(data) {
      console.log(data)
      const response = JSON.parse(data);

      if (response.success) {
        const audioSent = new Audio(rootpath() + '/assets/audio/whoosh.mp3');
        audioSent.play();

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
      console.log(data)
      const response = JSON.parse(data);

      const audioError = new Audio(rootpath() + '/assets/audio/error.mp3');
      audioError.play();

      Swal.fire({
        title: '" . __("Something went wrong!", "wpdevhelper") . "',
        html: `\${response.message}`,
        type: 'error',
        confirmButtonText: 'Ok'
      });

      alerts.alert({
        type: 'danger',
        title: i18n('fail_send_email'),
      });

    }).always(function() {
      $(btnForm).html('" . $text_btn . "');
      $(btnForm).attr('disabled', false);
      resetButton.css('display', 'none');
    });";

    $html .= $count_requireds > 0 ? '}' : '';
    $html .= "});"; // on submit
    $html .= "</script>";

  endif;

  return $html;
}

function wpdh_get_field_name($input_type, $id)
{
  $input_name = 'input';

  if (in_array($input_type, ['select', 'textarea'])) {
    $input_name = $input_type;
  }

  return $input_name . "[name=" . $id . "]";
}
