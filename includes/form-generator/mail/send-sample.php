<?php  
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\TBodyHTML;

// Load Composer's autoloader
// require 'vendor/autoload.php';
require("src/PHPMailer.php");
require("src/SMTP.php");
require("src/Exception.php");
require("TBodyHTML.php"); // Crie este arquivo através do TBodyHTML-sample.php
require('../../../includes/class.upload-file-to-media.php');

/**
 * Tratar os valores recebidos por serialize() do javascript
 */
// $data = array();

// if (isset($_POST['formData'])) 
//   parse_str($_POST['formData'], $data);

/**
 * Verificar campos
 */
if (empty($_POST['email'])) {
  echo 'O campo e-mail é obrigatório.';

} else {
  //Create an instance; passing `true` enables exceptions
  $mail = new PHPMailer(true);

  // Instância para o template do corpo do e-mail.
  $template = new TBodyHTML;

  // Arquivos recebidos
  $files = null;

  if (isset($_FILES['files'])) 
    $files = $_FILES['files'];

  // Criar o upload para o wordpress e retornar a URL
  if ($files !== null) {
    $up = new UploadFileToMedia();
    $url_file = $up->upload($_FILES['files']);
  }

  try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                         //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.sendgrid.net';                    //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'apikey';                               //SMTP username
    $mail->Password   = '';                                     //SMTP password
    $mail->SMTPSecure = 'tls';                                  //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    $mail->Mailer     = "smtp";
    $mail->CharSet    = 'UTF-8';
    $mail->setLanguage('pt_br');                                // Set language for translations PHPMailer error messages

    // Remetente
    $mail->setFrom('from@example.com', 'Mailer');         // E-mail do envio
    $mail->addReplyTo('info@example.com', 'Information');
    
    // Recipients
    $mail->addAddress('joe@example.net', 'Joe User');     //Add a recipient
    $mail->addAddress('ellen@example.com');               //Name is optional

    // Attachments
    // $mail->AddAttachment('/file.png');

    // Verifica dados recebidos se existem
    $name    = isset($_POST['name']) ? $_POST['name'] :  '';
    $email   = isset($_POST['email']) ? $_POST['email'] :  '';
    $phone   = isset($_POST['phone']) ? $_POST['phone'] :  '';
    $message = isset($_POST['message']) ? $_POST['message'] :  '';
    $links   = !empty($url_file) && $url_file != null ? $url_file :  '';

    // Passar os dados para um array com seus títulos
    $data_content = array(
      'Nome:' => $name,
      'E-mail:' => $email,
      'Telefone:' => $phone,
      'Mensagem:' => $message,
      'Arquivos'  => $links
    );

    // Para o alt body sem estrutura html
    $altBody = '';
    foreach ($data_content as $key => $value) {
      if (!empty($value)) {
        if (is_array($value)) {
          $altBody .= $key . ' ';
          foreach ($value as $url) {
            $altBody .= '(' . $url .') ';
          }
          $altBody .= ', ';
        } else {
          $altBody .= $key . ' ' . $value . ', ';
        }
      }
    }

    // Set email format to HTML
    $mail->isHTML(true);

    // Email title subject
    $mail->Subject = '';

    // Normal body with html
    $mail->Body = $template->getContent($data_content);

    // 'This is the body in plain text for non-HTML mail clients'
    $mail->AltBody = $altBody;

    // Send informations
    $mail->send();

    $info = json_encode(
      array(
        'success' => 1,
          'success' => 1, 
        'success' => 1,
        'message' => "Mensagem enviada com sucesso!",
      )
    );

    print_r($info);

  } catch (Exception $e) {
    $info = json_encode(
      array(
        'success' => 0,
        'message' => "Não foi possível enviar a mensagem. Mailer Error: {$mail->ErrorInfo}",
      )
    );
    print_r($info);
  }
}
