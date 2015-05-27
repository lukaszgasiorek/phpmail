<?php
require __DIR__.'/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 400 Bad Request");
    exit;
}

// Komunikaty
define('SUCCESS_MESSAGE', 'Wiadomość email została wysłana pomyślnie');
define('ERROR_MESSAGE', 'Wystąpił błąd podczas wysłania wiadomości email');

// Dane serwera SMTP (wymagane dla Swiftmailer)
define('SMTP_HOST', '');
define('SMTP_PORT', '');
define('SMTP_SECURITY', 'ssl'); // ssl lub tls
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
// Nazwa i adres email nadawcy z jakiego będą wysyłane wiadomości - wymagane!!!
define('NAME_FROM', '');
define('EMAIL_FROM', '');
define('EMAIL_SUBJECT', '');

// =============== //
// Wysyłanie maila //
// =============== //

$name = trim(strip_tags($_POST['name']));
$email = trim(strip_tags($_POST['email']));
$text = trim(strip_tags($_POST['text']));

try {
    // Sprawdź poprawność wypełnionych pól formularza
    if (strlen($name) == 0) {
        throw new InvalidArgumentException('Pole "Imię i nazwisko" jest wymagane!');
    }
    if (strlen($email) == 0) {
        throw new InvalidArgumentException('Pole "E-mail" jest wymagane!');
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        throw new InvalidArgumentException('Podany adres e-mail jest nieprawidłowy!');
    }
    if (strlen($text) == 0) {
        throw new InvalidArgumentException('Pole "Tekst" jest wymagane!');
    }

    $transport = Swift_SmtpTransport::newInstance(SMTP_HOST, SMTP_PORT, SMTP_SECURITY)->setUsername(SMTP_USERNAME)->setPassword(SMTP_PASSWORD);
    $mailer = Swift_Mailer::newInstance($transport);

    // Wiadomość email
    $message = Swift_Message::newInstance(EMAIL_SUBJECT, $text);
    $message->setFrom(EMAIL_FROM, NAME_FROM);
    $message->setTo($email);
    $mailer->send($message);
    echo json_encode(array('message' => SUCCESS_MESSAGE));
} catch (\Exception $ex) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(array('message' => ERROR_MESSAGE));
}
exit;
