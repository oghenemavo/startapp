<?php
/**
 * Project: startup
 * File: Mail.php
 *
 * Initial version by: @oghenemavo
 * Initial version created on: 12/10/2019 8:42 PM
 *
 * Contact: princetunes@gmail.com
 *
 */

namespace App\Helpers;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


class Mail
{

    /**
     * @param array $to
     * @param $subject
     * @param array $body
     * @param array $attachment
     */
    static public function send ($to = [], $subject, $body = [], $attachment = []) {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = '*********';                     // SMTP username
            $mail->Password   = '*******';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $mail->Port       = 587;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom('from@example.com', 'Mailer');
            $mail->addAddress($to['address'], $to['name'] ?? null);     // Add a recipient

//            $mail->addAddress('ellen@example.com');               // Name is optional
//            $mail->addReplyTo('info@example.com', 'Information');
//            $mail->addCC('cc@example.com');
//            $mail->addBCC('bcc@example.com');

            // Attachments
            if (!empty($attachment)) {
                $mail->addAttachment($attachment['path'], $attachment['name'] ?? null);    // Optional name
            }

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body['html'];
            $mail->AltBody = $body['alt'];

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

}
