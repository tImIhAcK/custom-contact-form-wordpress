<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require __DIR__ . '/vendor/autoload.php';

class EmailSender
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function send(string $to, string $subject, string $message)
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->SMTPAuth = true;

        $mail->Host = $this->config['HOST'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $this->config['PORT'];

        $mail->Username = $this->config['USERNAME'];
        $mail->Password = $this->config['PASSWORD'];

        // $mail->SMTPDebug = 3;

        $mail->setFrom($this->config['USERNAME']);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        try {
            if ($mail->Send()) {
                return true;
            }
        } catch (Exception $th) {
            return 'Error sending email: ' . $th->getMessage();
        }
    }
}
