<?php

// Exit if accessed directly
if ( ! defined('ABSPATH') )
  exit;


global $post;

/**
 * API para envio de e-mail.
 */
$api = get_field('api_url') != '' ? get_field('api_url') : PLUGINROOT . '/inc/form/mail/send.php';

/**
 * Gerar formulário
 */
if (have_rows('campos')) :

  // Form Classes
  $class = get_field('form_style') . ' material-form translucent-form outlined-basic';
?>

  <form id="form-theme-<?php the_ID(); ?>" action="javascript:void(0);" method="POST" class="d-flex flex-wrap <?php echo $class ?>">
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
        $attributes = get_sub_field('attributes');

        input($input_name, $input_id, $input_type, $is_required, $input_value, $custom_class, $attributes);
      else: 
        
        echo '<span class="fs-5 mb-0 mt-2">'.get_sub_field('group_title').'</span>';

      endif;
    endwhile;
    ?>
    <button type="submit" class="btn-theme btn-medium">
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
      while (have_rows('campos')) :
        the_row();

        $id = get_sub_field('input_id');
        $input_type  = get_sub_field('input_type');
        
        // Radio type
        if ($input_type == 'radio') : ?>
          let <?= $id ?> = !!$('input[name="<?= $id ?>"]:checked').val() ? $('input[name="<?= $id ?>"]:checked').val() : "";        
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
          $input_type  = get_sub_field('input_type'); ?>

            if (<?= $id ?>.trim() == "") {
              Swal.fire({
                type: 'warning',
                title: 'Oops...',
                html: '<?php printf(__("O campo <b>%s</b> é obrigatório.", 'wpdevhelper'), $name) ?>'
              });
            } else

          <?php
          $count_requireds++;
        endif;
      endwhile;
      ?>

      <?php echo $count_requireds > 0 ? '{' : '' ?>

      // Send informations
      const btnForm = form<?php the_ID(); ?> + ' button[type=submit]';
      const formData = $(form<?php the_ID(); ?>).serialize();

      $.ajax({
        url: '<?php echo $api ?>',
        method: 'POST',
        data: {
          <?php the_field('ajax_data') ?>
        },
        beforeSend: () => {
          $(btnForm).html('<?= __("Enviando...", 'wpdevhelper') ?>');
        }

      }).done(function(data) {
        const obj = JSON.parse(data);        
        
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
        const obj = JSON.parse(data);

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