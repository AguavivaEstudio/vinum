<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';

$mail = new PHPMailer(true);

// Variables de mail - requeridos
$c_3            = '#d33014';
$c_blanco       = '#FFFFFF';
$font_f         = 'Arial, sans-serif';
$logo           = '';
$nombre_empresa = 'Nombre Empresa';
$tel_empresa    = '+54 9 xxxx-xxxx';
$wpp_empresa    = '+54 9 xxxx-xxxx';
$tel_alt        = '+54 9 xxxx-xxxx';
$web_url        = 'https://test.aguaviva.com.ar';

// Variables de proyecto - requeridos
$recipient      = "larenasdam@gmail.com"; // Casilla
$bcc            = "martinafsa8@gmail.com"; // Mail CC
$bcc_name       = 'Tester';
$smtp_mail      = "pruebas@aguaviva.com.ar";
$smtp_pass      = '@6bL%3gw#S=2';
$smtp_host      = 'mail.aguaviva.com.ar';

// Variables del usuario
$subject        = "Contacto desde la página web";
$nombre         = $_POST['nombre'] ?? null;
$apellido       = $_POST['apellido'] ?? null;
$email          = $_POST['email'];
$consulta       = $_POST['mensaje'];
$telefono       = $_POST['telefono'] ?? null;
$empresa        = $_POST['empresa'] ?? null;
$website        = $_POST['website'] ?? null;

$response = array();

//Crear cuerpo del mail
$email_body = "
<body style='width: 100%; margin: 0; padding:2rem; mso-line-height-rule: exactly; color: #3C3C3C; font-family: Arial, sans-serif'>
    <center style='max-width: 650px; margin: 0 auto; width: 100%; background: #fff; text-align: left;'>
        <table role='presentation' cellspacing='0' cellpadding='0' border='0' align='center' style='width: 100%;'>
            <tr>
                <td>
                    <table style='width: 100%; padding: 1rem 0; border-spacing: 0;'>
                        <tr>
                            <td class='header' valign='top' style='padding-left: 2rem; padding-right: 2rem;'>
                                <img src='$logo' width='200px' height='auto' alt='Logo $nombre_empresa' border='0'>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style='padding: 1rem 2rem;'>
                    <p style='margin: 0 0 0 auto; width: 100%; font-size: 20px; color: #000;'>CONTACTO DESDE <br><strong >LA WEB</strong></p>
                </td>
            </tr>
        </table>

        <table role='presentation' cellspacing='0' cellpadding='0' border='0' align='left' style='width: 100%; margin-left: 2rem;'>
            <tr>
              <td style='height: 2px; background-color: #C3C3C3; width: 100%'></td>
            </tr>
        </table>

        <table role='presentation' cellspacing='0' cellpadding='0' border='0' align='center' style='width: 100%; margin: 0;' class='email-container'>
            <tr>
                <td style='padding: 2rem 1.5rem; font-family: $font_f; font-size: 12px; text-align: left;'>
                    <p style='padding: 1.25rem .9rem; font-size: 18px; line-height: 30px;'>"  .
    (isset($nombre)   ? "<strong style='color: $c_3;'>Nombre:</strong> $nombre<br>"     : "") .
    (isset($apellido) ? "<strong style='color: $c_3;'>Apellido:</strong> $apellido<br>" : "") .
                        "<strong style='color: $c_3;'>E-mail:</strong> $email<br>"            .
    (isset($telefono) ? "<strong style='color: $c_3;'>Teléfono:</strong> $telefono<br>" : "") .
    (isset($empresa)  ? "<strong style='color: $c_3;'>Empresa:</strong> $empresa<br>"   : "") .
    (isset($website)  ? "<strong style='color: $c_3;'>Sitio:</strong> $website<br>"     : "") .
                        "<strong style='color: $c_3;'>Mensaje:</strong> $consulta<br>
                    </p>
                </td>
            </tr>
        </table>
    </center>
</body>
";


try {
    $mail->isHTML(true);
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_mail;
    $mail->Password = $smtp_pass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->SMTPDebug = 3;

    $mail->addCC($bcc, $bcc_name);
    $mail->addAddress($recipient);

    $mail->setFrom($smtp_mail, $nombre_empresa);
    $mail->addReplyTo($email, $nombre);

    $mail->Subject = $subject;
    $mail->Body = $email_body;
    $mail->AltBody = 'Cuerpo alternativo del mensaje';

    $mail->send();
    echo "Mail enviado correctamente";
} catch (Exception $e) {
    echo "El mail no se pudo enviar. Error: {$mail->ErrorInfo}";
}