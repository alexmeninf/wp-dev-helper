<?php
/**
 * WP Starter Theme
 * Vinculado com o PHPMailer
 *
 * @author    Alexandre Menin (Criador) <alex.menin11@gmail.com>
 * @since 1.0
 * 
 */

namespace PHPMailer\PHPMailer;

class TBodyHTML 
{
  /**
   * Função para o retorno do layout em html para o 
   * body do e-mail.
   * 
   * Passe no parâmetro $content os dados recebidor 
   * do formulários para tratar-los abaixo no template.
   * É Recomendado ser um array.
   *
   * @param  mixed $content
   */
  public function getContent($data_content)
  {

    $body = '';
    foreach ($data_content as $key => $value) {
      if (!empty($value)) {
        if (is_array($value)) {
          $body .= '<p><b>' . $key . '</b>';
          foreach ($value as $url) {
            $body .= '<br>(' . $url .')';
          }
          $body .= '</p>';
        } else {
          $body .= '<p><b>' . $key . '</b> ' . $value . ' </p>';
        }
      }
    }

    $body_html = '<h2 class="title">Dados recebidos:</h2>';
    $body_html .= $body;

    $html = '<html>
      <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;900&display=swap" rel="stylesheet">
        <style>' . $this->getStyle() . '</style>
      </head>
      <body>
        <div class="wrap">
          <table class="table card">
            <tr>
              <td>
                <div class="infoContent">
                  '. $body_html .'
                </div>
              </td>
            </tr>
          </table>
          <div class="footer">
            <p>Dados recebido através do site ' . $_SERVER['HTTP_HOST'] . '</p>
            <p>&copy; '. date('Y') .'</p>
          </div>
        </div>
      </body>
    </html>';

    return $html;
  }

  
  /**
   * Definição de estilo para o e-mail.
   *
   * @return string
   */
  private function getStyle() 
  {
    $theme_color = '#06c'; // hex color

    $style = 'html,body {font-family: Roboto, sans-serif;}
      .wrap {max-width: 600px;margin: 0 auto;font-family:Roboto,Google Sans,Helvetica,Arial,sans-serif;}
      .card {border:1px solid #dedede;border-radius:20px;overflow:hidden}
      .header {padding:25px 15px;background: #f1f1f1;text-align: center;}
      .imgHeader {height:38px;width:auto;display:block;margin:0 auto;}
      .infoContent {padding: 20px 50px 50px;font-size:16px;}
      .infoContent p {line-height: 1.8;}
      .title {color:#3c3c3b;font-size: 1.4rem;font-weight: 900;margin: 20px 0 40px 0;}
      .subtitle {font-weight: 400;margin-top: 40px;font-size:24px}
      .footer {margin: 30px 0;font-size: 12px;text-align:center;color: #666767}
      .linkColor {color: #3d3d3d;text-decoration:none}
      .btnPrimary {  background-color: ' . $theme_color . ';border-color: ' . $theme_color . ';color: #fff;display: inline-block;font-weight: 700;margin-top: 1rem;padding: 15px 22px;text-align: center;text-decoration: none;text-transform: uppercase;margin-bottom:1rem;border-radius:25px}
      .btnSecundary {  background-color: #fff;border: 1px solid ' . $theme_color . ';color: ' . $theme_color . ';display: inline-block;font-weight: 700;margin-top: 1rem;padding: 15px 22px;text-align: center;text-decoration: none;text-transform: uppercase;margin-bottom:1rem;border-radius:25px}
      .table {width:100%;border-spacing: 0;}
      td {padding:0}
      .social {padding:0 50px;}
      .social a {display: block;width:37px}
      .social img {height: 28px;}
    ';

    return $style;
  }
}