<?php

namespace App\Web\Controllers;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class TesteController
{
    public function mail()
    {
        try {
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = MAILER_HOST;
            $mail->SMTPAuth = MAILER_SMTP_AUTH;
            $mail->Port = MAILER_PORT;
            $mail->Username = MAILER_USERNAME;
            $mail->Password = MAILER_PASSWD;

            //Recipients
            $mail->setFrom('from@example.com', 'Mailer');
            $mail->addAddress('mr.robot.felipe@gmail.com', 'Luiz Felipe');

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Here is the subject';
            $mail->Body = 'This is the HTML message body <b>in bold!</b>';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            pr('Message has been sent');
        } catch (Exception $e) {
            pr("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}
