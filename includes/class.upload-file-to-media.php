<?php

if ( ! defined( 'ABSPATH' ) ) 
  exit; // Exit if accessed directly.

/**
 * Este arquivo permite o upload de arquivos para o midia no painel administrativo
 * do WordPress, atráves de formulários customizados.
 * 
 * Arquivo introduzido na versão abaixo do plugin 
 * 
 * @version 1.7.2
 * 
 */

require( dirname(__FILE__) . '/../../../../wp-load.php' );

/**
 * UploadFileToMedia
 *
 * @param  mixed $files
 * @return void
 */
class UploadFileToMedia
{
  public function upload($files) {

    $wordpress_upload_dir = wp_upload_dir();
    // $wordpress_upload_dir['path'] is the full server path to wp-content/uploads/2017/05, for multisite works good as well
    // $wordpress_upload_dir['url'] the absolute URL to the same folder, actually we do not need it, just to show the link to file
    $num  = 1;  // number of tries when the file with the same name is already exists
    $urls = []; // URL geradas depois do upload
    $i    = 0;  // Total de arquivos
    
    if (empty($files))
      die('Nenhum arquivo foi selecionado');

    while ($i < count($files['name'])) {

      $file_name = devh_sanitize_filename($files['name'][$i]); // Clear file name

      $new_file_path = $wordpress_upload_dir['path'] . '/' . $file_name;
      $new_file_mime = mime_content_type($files['tmp_name'][$i]);
  
      if ($files['error'][$i])
        die($files['error'][$i]);
  
      if ($files['size'][$i] > wp_max_upload_size())
        die('It is too large than expected.');
  
      if (!in_array($new_file_mime, get_allowed_mime_types()))
        die('WordPress doesn\'t allow this type of uploads.');
  
      while (file_exists($new_file_path)) {
        $num++;
        $new_file_path = $wordpress_upload_dir['path'] . '/' . $num . '_' . $file_name;
      }
  
      // looks like everything is OK
      if (move_uploaded_file($files['tmp_name'][$i], $new_file_path)) {
  
        $upload_id = wp_insert_attachment(array(
          'guid'           => $new_file_path,
          'post_mime_type' => $new_file_mime,
          'post_title'     => preg_replace('/\.[^.]+$/', '', $file_name),
          'post_content'   => '',
          'post_status'    => 'inherit'
        ), $new_file_path);
  
        // wp_generate_attachment_metadata() won't work if you do not include this file
        require_once(ABSPATH . 'wp-admin/includes/image.php');
  
        // Generate and save the attachment metas into the database
        wp_update_attachment_metadata($upload_id, wp_generate_attachment_metadata($upload_id, $new_file_path));

        // Adiciona a url do upload
        array_push($urls, $wordpress_upload_dir['url'] . '/' . basename($new_file_path));
      }

      $i++;
    }

    return $urls;
  }
}
