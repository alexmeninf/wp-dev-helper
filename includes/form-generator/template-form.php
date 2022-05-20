<?php

// Exit if accessed directly
if ( ! defined('ABSPATH') )
  exit;


function wpdh_get_form_code() {  

  global $post;
  $html = '';

  /**
   * API para envio de e-mail.
   */
  $api = get_field('api_url') != '' ? esc_url(get_field('api_url')) : PLUGINROOT . '/includes/form-generator/mail/send.php';
  
  /**
   * Gerar formulário
   */
  if (have_rows('campos')) :
  
    $html .= '<form id="form-theme-'. get_the_ID() .'" name="form'. get_the_ID() .'" action="javascript:void(0);" method="POST" class="d-flex flex-wrap '. esc_attr(get_field('form_style')) .'">';

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
  
          $html .= input($input_name, $input_id, $input_type, $is_required, $input_value, $custom_class, $attributes, $enable_parameter);
        else: 
          
          $html .= '<div class="title-form-group">'.get_sub_field('group_title').'</div>';
  
        endif;
      endwhile;

      // Texto do botão
      $text_btn = get_field('button_text') ? get_field('button_text') : __('Send', 'wpdevhelper');
     
      $html .= '<button type="submit" class="'. esc_attr(get_field('button_class')) .'">
          <span>'. $text_btn .'</span>
        </button>
      </form>';
  
    // Abertura do script
    $html .= "<script>const form". get_the_ID() ." = '#form-theme-". get_the_ID() ."';";
    $html .= "$(form". get_the_ID() .").find('input, select, textarea').prop('required', false);";
    $html .= "$(form". get_the_ID() .").on('submit', function(e) {";
    $html .= "e.preventDefault();";
  
    // Verificar se existe algum campo de arquivo
    $has_file = false;

    while (have_rows('campos')) :
      the_row();

      $id = get_sub_field('input_id');
      $input_type  = get_sub_field('input_type');
      
      // File type
      if ($input_type == 'file') : 
        $has_file = true;
        $html .= "let files_". $id ." = $('input[name=". $id ."]')[0].files;";
      // Radio type
      elseif ($input_type == 'radio') :
        $html .= "let ". $id ." = !!$('input[name=". $id ."]:checked').val() ? $('input[name=". $id ."]:checked').val() : '';";
      // Select type
      elseif ($input_type == 'select') :
        $html .= "let ". $id ." = $(this).find('select[name=". $id ."] option').filter(':selected').val();";
      // Other types
      else :
        $html .= "let ". $id ." = $(this).find('[name=". $id ."]').val();";
      endif;
    endwhile;

    // Validate values
    $count_requireds = 0;

    while (have_rows('campos')) :
      the_row();

      if (get_sub_field('is_required')) :
        $id   = get_sub_field('input_id');
        $name = get_sub_field('input_name');
        $input_type  = get_sub_field('input_type'); 
        
        if ($input_type == 'file') :
          $html .= "if (files_". $id .".length === 0) {
            Swal.fire({
              type: 'warning',
              title: 'Oops...',
              html: '".sprintf(__("Add the file in the <b>%s</b> field.", "wpdevhelper"), $name) ."'
            });  
          } else ";

        else:

          $html .= "if (". $id .".trim() == '') {
            Swal.fire({
              type: 'warning',
              title: 'Oops...',
              html: '". sprintf(__("The <b>%s</b> field is required.", "wpdevhelper"), $name) ."'
            });
          } else ";

        endif;
        $count_requireds++;
      endif;
    endwhile;
    
    $html .= $count_requireds > 0 ? '{' : '';

    // Send informations
    $html .= "const btnForm = form". get_the_ID() ." + ' button[type=submit]';";

    if ($has_file) :
      $html .= "let formData = new FormData();";

      while (have_rows('campos')) : the_row(); 
        $id = get_sub_field('input_id');
        $input_type  = get_sub_field('input_type'); 
        
        // Verificar o tipo da input
        if ($input_type == 'file') :
          $html .= "
            $.each(files_". $id .", function(i, file) {
              formData.append('files[]', file, file.name);
            });";
        else :
          $html .= "formData.append('". $id ."', ". $id .");";
        endif; 
      endwhile;
    else :

    $html .= "const formData = $(form". get_the_ID() .").serialize();";

    endif;

    // Parametros para arquivo
    $file = '';
    if ($has_file) :
      $file = 'processData: false, contentType: false,';
    endif; 

    $html .= "$.ajax({
      url: '". $api ."',
      method: 'POST',
      ". $file ."
      data: ". trim(esc_attr(get_field('ajax_data'))) .",
      beforeSend: () => {
        $(btnForm).html('". __("Sending...", "wpdevhelper") ."');
      }

    }).done(function(data) {
      const obj = JSON.parse(data);    

      if (obj.success) {
        Swal.fire({
          title: '". __("Sent!", "wpdevhelper") ."',
          text: `\${obj.message}`,
          type: 'success',
          confirmButtonText: '". __("Close", "wpdevhelper") ."'
        });
      } else {
        Swal.fire({
          title: '". __( "Something went wrong!", "wpdevhelper") ."',
          text: `\${obj.message}`,
          type: 'error',
          confirmButtonText: 'Ok'
        });
      }
      
      clear_form_elements(form". get_the_ID() .");
      
    }).fail(function(data) {
      const obj = JSON.parse(data);    

      Swal.fire({
        title: '". __( "Something went wrong!", "wpdevhelper") ."',
        text: `\${obj.message}`,
        type: 'error',
        confirmButtonText: 'Ok'
      });

    }).always(function() {
      $(btnForm).html('". __("Send again", "wpdevhelper") ."');
    });";

    $html .= $count_requireds > 0 ? '}' : '';
    $html .= "});"; // on submit
    $html .= "</script>";

  endif;

  return $html;
}
