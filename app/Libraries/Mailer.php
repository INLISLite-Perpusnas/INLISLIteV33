<?php namespace App\Libraries;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mailer
{
    public function __construct()
    {
        log_message('info', 'Mail Class Initialized');
    }

    public function send($data = [], $is_debug = false)
    {
        $mail = new PHPMailer();
        if ($is_debug) {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        }

        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = getenv('mail.Host') ?? 'smtp.mailtrap.io';
        $mail->SMTPAuth = getenv('mail.SMTPAuth') ?? true;
        $mail->Port = getenv('mail.Port') ?? 2525;
        $mail->Username = getenv('mail.Username') ?? 'c83905cbd5c27c';
        $mail->Password = getenv('mail.Password') ?? '2a0f54afe71d02';
        $mail->SMTPSecure = getenv('mail.SMTPSecure') ?? '';

        $mail->setFrom(
            getenv('mail.FromEmail') ?? 'info_ppbh@perpusnas.go.id',
            getenv('mail.FromName') ?? 'Perpusnas Republik Indonesia'
        );
        $mail->addAddress($data['email']);

        // Set email format to HTML
        $mail->isHTML(true);
        $mail->Subject = $data['subject'];
        $mail->Body = $data['body'];

        // Send email
        if (!$mail->send()) {
            return false;
        } else {
            return true;
        }
    }

   public function send_via_google(
    $to = '',
    $subject = '',
    $data = [],
    $is_debug = false
) {
    // --- AWAL PERBAIKAN ---
    // Cek jika parameter pertama adalah array (karena pemanggilan fungsi yang keliru)
    if (is_array($to)) {
        $emailData = $to; // Ganti nama variabel agar lebih jelas
        $to = $emailData['email'] ?? ''; // Ekstrak alamat email yang sebenarnya
        $subject = $emailData['subject'] ?? $subject; // Ekstrak subjek email
        $body = $emailData['body'] ?? view('Home\Views\signup_email', $data); // Gunakan body dari array jika ada
    } else {
        // Jika fungsi dipanggil dengan benar, render body dari view
        $body = view('Home\Views\signup_email', $data);
    }
    // --- AKHIR PERBAIKAN ---

    $mail = new PHPMailer(true);
    try {
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = 'smtp.googlemail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'dalidalimar3@gmail.com';
        $mail->Password = 'onzoohklalmmcvno'; // WAJIB GUNAKAN APP PASSWORD
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('Inlislite Admin');
        
        // Sekarang $to sudah pasti string
        $mail->addAddress($to); 

        $mail->addReplyTo(get_parameter('email-default', 'info_ppbh@perpusnas.go.id'), 'Inlislite Admin');
        
        // Konten
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body; // Gunakan body yang sudah ditentukan
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        // Kirim email
        if (!$mail->send()) {
            // Jika perlu debugging, Anda bisa log error di sini
            // error_log('Mailer Error: ' . $mail->ErrorInfo);
            return false;
        } else {
            return true;
        }
    } catch (Exception $e) {
        // error_log('Mailer Exception: ' . $mail->ErrorInfo);
        return false;
    }
}
    public function send_via_corporate($data, $is_debug = false)
    {
        $mail = new PHPMailer(true);
        if ($is_debug) {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        }
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp3.domain.com';
        $mail->SMTPAuth = false;
        $mail->Username = '';
        $mail->Password = '';
        $mail->SMTPSecure = false;
        $mail->Port = 25;
        $mail->SMTPAutoTLS = false;

        $mail->setFrom('info@domain.com', 'Sender');
        $mail->addAddress($data['to_email'], $data['to_name']);

        // Set email format to HTML
        $mail->isHTML(true);
        $mail->Subject = $data['subject'];
        $mail->Body = $data['body'];

        // Send email
        if (!$mail->send()) {
            return false;
        } else {
            return true;
        }
    }
}
