<?php

// Exit if accessed directly
if ( ! defined('ABSPATH') )
  exit;


global $post;

/**
 * API para envio de e-mail.
 */
$api = get_field('api_url') != '' ? esc_url(get_field('api_url')) : PLUGINROOT . '/form-generator/mail/send.php';

/**
 * Gerar formulário
 */
if (have_rows('campos')) :

?>

  <form id="form-theme-<?php the_ID(); ?>" action="javascript:void(0);" method="POST" class="d-flex flex-wrap <?php echo esc_attr(get_field('form_style'))?>">
    <?php
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

        input($input_name, $input_id, $input_type, $is_required, $input_value, $custom_class, $attributes);
      else: 
        
        echo '<div class="title-form-group">'.get_sub_field('group_title').'</div>';

      endif;
    endwhile;
    ?>
    <button type="submit" class="<?php echo esc_attr(get_field('button_class')) ?>">
      <span><?php echo get_field('button_text') ? get_field('button_text') : __('Enviar', 'wpdevhelper'); ?></span>
    </button>
  </form>

  <script>
    const form<?php the_ID(); ?> = '#form-theme-<?php the_ID(); ?>';
    $(form<?php the_ID(); ?>).find('input, select, textarea').prop('required', false);

    $(form<?php the_ID(); ?>).on('submit', function(e) {
      e.preventDefault();

      // Get values
      <?php
      // Verificar se existe algum campo de arquivo
      $has_file = false;

      while (have_rows('campos')) :
        the_row();

        $id = get_sub_field('input_id');
        $input_type  = get_sub_field('input_type');
        
        // File type
        if ($input_type == 'file') : 
          $has_file = true; ?>

          let files_<?= $id ?> = $('input[name=<?= $id ?>]')[0].files;     

        <?php
        // Radio type
        elseif ($input_type == 'radio') : ?>
          let <?= $id ?> = !!$('input[name=<?= $id ?>]:checked').val() ? $('input[name=<?= $id ?>]:checked').val() : "";        
        <?php

        // Select type
        elseif ($input_type == 'select') : ?>
          let <?= $id ?> = $(this).find('select[name=<?= $id ?>] option').filter(':selected').val();

        <?php // Other types
        else : ?>
          let <?= $id ?> = $(this).find('[name=<?= $id ?>]').val();

      <?php endif;
      endwhile;
      ?>

      // Validate values
      <?php
      $count_requireds = 0;
      $check_radio_name = '';

      while (have_rows('campos')) :
        the_row();

        if (get_sub_field('is_required')) :
          $id   = get_sub_field('input_id');
          $name = get_sub_field('input_name');
          $input_type  = get_sub_field('input_type'); 
          
          if ($input_type == 'file') : ?>

            if (files_<?= $id ?>.length === 0) {
              Swal.fire({
                type: 'warning',
                title: 'Oops...',
                html: '<?php printf(__("Adicione o arquivo no campo <b>%s</b>.", 'wpdevhelper'), $name) ?>'
              });  
            } else

          <?php else: ?>

            if (<?= $id ?>.trim() == "") {
              Swal.fire({
                type: 'warning',
                title: 'Oops...',
                html: '<?php printf(__("O campo <b>%s</b> é obrigatório.", 'wpdevhelper'), $name) ?>'
              });
            } else

          <?php
          endif;
          $count_requireds++;
        endif;
      endwhile;
      ?>

      <?php echo $count_requireds > 0 ? '{' : '' ?>

      // Send informations
      const btnForm = form<?php the_ID(); ?> + ' button[type=submit]';

      <?php if ($has_file) : ?>
        let formData = new FormData();

        <?php while (have_rows('campos')) : the_row(); 
          $id = get_sub_field('input_id');
          $input_type  = get_sub_field('input_type'); 
          
          if ($input_type == 'file') : ?>

          $.each(files_<?= $id ?>, function(i, file) {
            formData.append('files[]', file, file.name);
          });

          <?php else : ?>

          formData.append('<?= $id ?>', <?= $id ?>);

          <?php endif; 
        endwhile;
      else : ?>

      const formData = $(form<?php the_ID(); ?>).serialize();

      <?php endif; ?>

      $.ajax({
        url: '<?php echo $api ?>',
        method: 'POST',
        <?php if ($has_file) : ?>
        processData: false,
        contentType: false,
        <?php endif; ?>
        data: <?php echo trim(esc_attr(get_field('ajax_data'))) ?>,
        beforeSend: () => {
          $(btnForm).html('<?= __("Enviando...", 'wpdevhelper') ?>');
        }

      }).done(function(data) {
        const obj = JSON.parse(JSON.stringify(data));    
        console.log(obj)
        if (obj.success) {
          Swal.fire({
            title: '<?= __("Enviado!", 'wpdevhelper') ?>',
            text: `${obj.message}`,
            type: 'success',
            confirmButtonText: '<?= __("Fechar", 'wpdevhelper') ?>'
          });
        } else {
          Swal.fire({
            title: '<?= __( "Algo deu errado!", 'wpdevhelper') ?>',
            text: `${obj.message}`,
            type: 'error',
            confirmButtonText: 'Ok'
          });
        }
        
        clear_form_elements(form<?php the_ID(); ?>);
        
      }).fail(function(data) {
        const obj = JSON.parse(JSON.stringify(data));

        Swal.fire({
          title: '<?= __( "Algo deu errado!", 'wpdevhelper') ?>',
          text: `${obj.message}`,
          type: 'error',
          confirmButtonText: 'Ok'
        });

      }).always(function() {
        $(btnForm).html('<?= __("Enviar novamente", 'wpdevhelper') ?>');
      });

      <?php echo $count_requireds > 0 ? '}' : '' ?>
    }); // on submit
  </script>

<?php endif; ?>