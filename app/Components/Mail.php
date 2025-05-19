<?php

namespace App\Components;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mail
{
    public bool $error = false;
    public string $messageError = '';

    public function __construct(
        private string $name,
        private string $email,
        private string $subject,
        private string $body,
        private string $altBody,
    )
    {
    }

    public function send(): void
    {
        try {
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = MAILER_HOST;
            $mail->SMTPAuth = MAILER_SMTP_AUTH;
            $mail->Port = MAILER_PORT;
            $mail->Username = MAILER_USERNAME;
            $mail->Password = MAILER_PASSWD;

            $mail->setFrom(MAILER_SENDER_EMAIL, MAILER_SENDER_NAME);
            $mail->addAddress($this->email, $this->name);

            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            $mail->isHTML(true);
            $mail->Subject = $this->subject;
            $mail->Body = $this->body;
            $mail->AltBody = $this->altBody;

            $mail->send();
        } catch (Exception $e) {
            $this->error = true;
            $this->messageError = $e->errorMessage();
        }
    }
}
