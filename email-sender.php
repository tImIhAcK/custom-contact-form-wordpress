<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require __DIR__ . '/vendor/autoload.php';

class EmailSender
{
    public static function send($config, $email, $subject, $message)
    {
        // $headers = 'From: Adeniran John adeniranjohn2016@gmail.com' . "\r\n";

        // // Send mail
        // $result = wp_mail($email, $subject, $message, $headers);
        // return $result;

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPDebug = 3;

        $mail->Host = "smtp.gmail.com";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->Username = $config['key']['EMAIL'];
        $mail->Password = $config['key']['SMTP_PASSWORD'];

        $mail->setFrom($config['key']['EMAIL']);
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        try {
            if ($mail->Send()) {
                return true;
            }
        } catch (Exception $th) {
            return 'Error' . $th->getMessage();
        }

        $mail->smtpClose();
    }

    // Add other methods as needed
}