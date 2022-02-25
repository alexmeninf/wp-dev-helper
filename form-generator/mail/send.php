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
require("src/TBodyHTML.php");

/**
 * Tratar os valores recebidos por serialize() do javascript
 */
$values = array();
parse_str($_POST['formData'], $values);

/**
 * Verificar campos
 */
if ( empty($values['name']) ) {
  echo 'O campo nome é obrigatório.';

} elseif ( empty($values['email']) ) {
  echo 'O campo e-mail é obrigatório.';

} else {
  //Create an instance; passing `true` enables exceptions
  $mail = new PHPMailer(true);
  
  // Instância para o template do corpo do e-mail.
  $template = new TBodyHTML;

  try {
      //Server settings
      $mail->SMTPDebug = SMTP::DEBUG_OFF;                         //Enable verbose debug output
      $mail->isSMTP();                                            //Send using SMTP
      $mail->Host       = 'smtp.sendgrid.net';                    //Set the SMTP server to send through
      $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
      $mail->Username   = 'apikey';                               //SMTP username
      $mail->Password   = 'secret';                               //SMTP password
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
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
      // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
      // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

      // Content
      $mail->isHTML(true);                                  //Set email format to HTML
      $mail->Subject = 'Here is the subject';
      $mail->Body          = $template->getContent(
        array(
          'name' => $values['name'],
          'email' => $values['email'],
          'phone' => $values['phone']
        )
      );
      $mail->AltBody = 'Nome:' . $values['name'] . ' | ' . 'E-mail:' . $values['email']. ' | ' . 'Telefone:' . $values['phone']; // 'This is the body in plain text for non-HTML mail clients'
      $mail->send();

      $info = json_encode(
        array(
          'success' => 1, 
          'message' => "Mensagem enviada com sucesso!"
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