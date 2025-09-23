<?php
// Start of script, no output before this point
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include required files
require 'inc/Exception.php';
require 'inc/PHPMailer.php';
require 'inc/SMTP.php';
require 'forms_settings.php';

// Initialize variables
$result = "";
$rfile = "";
$status = "";

// List of blocked email domains
$blockedDomains = [
    'gmail.com', 'yahoo.com', 'hotmail.com', 'aol.com', 'hotmail.co.uk',
    'hotmail.fr', 'msn.com', 'yahoo.fr', 'wanadoo.fr', 'orange.fr',
    'comcast.net', 'yahoo.co.uk', 'yahoo.com.br', 'yahoo.co.in', 'live.com',
    'rediffmail.com', 'free.fr', 'gmx.de', 'web.de', 'yandex.ru',
    'ymail.com', 'libero.it', 'outlook.com', 'uol.com.br', 'bol.com.br',
    'mail.ru', 'cox.net', 'hotmail.it', 'sbcglobal.net', 'sfr.fr',
    'live.fr', 'verizon.net', 'live.co.uk', 'googlemail.com', 'yahoo.es',
    'ig.com.br', 'live.nl', 'bigpond.com', 'terra.com.br', 'yahoo.it',
    'neuf.fr', 'yahoo.de', 'alice.it', 'rocketmail.com', 'att.net',
    'laposte.net', 'facebook.com', 'bellsouth.net', 'yahoo.in', 'hotmail.es',
    'charter.net', 'yahoo.ca', 'yahoo.com.au', 'rambler.ru', 'hotmail.de',
    'tiscali.it', 'shaw.ca', 'yahoo.co.jp', 'sky.com', 'earthlink.net',
    'optonline.net', 'freenet.de', 't-online.de', 'aliceadsl.fr', 'virgilio.it',
    'home.nl', 'qq.com', 'telenet.be', 'me.com', 'yahoo.com.ar',
    'tiscali.co.uk', 'yahoo.com.mx', 'voila.fr', 'gmx.net', 'mail.com',
    'planet.nl', 'tin.it', 'live.it', 'ntlworld.com', 'arcor.de',
    'yahoo.co.id', 'frontiernet.net', 'hetnet.nl', 'live.com.au', 'yahoo.com.sg',
    'zonnet.nl', 'club-internet.fr', 'juno.com', 'optusnet.com.au', 'blueyonder.co.uk',
    'bluewin.ch', 'skynet.be', 'sympatico.ca', 'windstream.net', 'mac.com',
    'centurytel.net', 'chello.nl', 'live.ca', 'aim.com', 'bigpond.net.au'
];

// Function to extract domain from email
function getDomainFromEmail($email) {
    $parts = explode('@', $email);
    return count($parts) === 2 ? strtolower($parts[1]) : null;
}

// Check if POST data exists
if (!empty($_POST)) {
    $name = $_POST['name'];
    $tel = $_POST['tel'];
    $email = $_POST['email'];
    $budget = $_POST['budget'];
    $message = $_POST['message'];

    // Validate the sender's email domain
    $senderDomain = getDomainFromEmail($email);
    if (in_array($senderDomain, $blockedDomains)) {
        // Redirect to error page if the email domain is blocked
        header('Location: /error_email_EN.html'); // Adjust to match your error handling
        exit;
    }

    // Capture selected language from a hidden input field
    $selectedLanguage = isset($_POST['selected_language']) ? $_POST['selected_language'] : 'EN';

    // Map language codes to success and error pages
    $successPages = [
        'EN' => '/success_email_EN.html',
        'FR' => '/success_email_FR.html',
        'ES' => '/success_email_ES.html',
        'PT-BR' => '/success_email_PT_BR.html',
    ];
    $errorPages = [
        'EN' => '/error_email_EN.html',
        'FR' => '/error_email_FR.html',
        'ES' => '/error_email_ES.html',
        'PT-BR' => '/error_email_PT_BR.html',
    ];

    // Default to English if language not found
    $successPage = $successPages[$selectedLanguage] ?? '/success_email_EN.html';
    $errorPage = $errorPages[$selectedLanguage] ?? '/error_email_EN.html';

    // Build the email message
    $messages  = "<h3>New message from the site $fromName</h3> \r\n";
    $messages .= "<ul>
                    <li><strong>Name:</strong> $name</li>
                    <li><strong>Email:</strong> $email</li>
                    <li><strong>Phone:</strong> $tel</li>
                    <li><strong>Budget:</strong> $budget</li>
                    <li><strong>Message:</strong> $message</li>
                  </ul>";

    $mail = new PHPMailer(true);

    try {
        // Configure SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'nortongaussit@gmail.com';
        $mail->Password = 'bcxe pzyy ommp klya';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Set sender and recipient details
        $mail->setFrom($from, $fromName);
        $mail->addAddress($to, 'Admin');
        $mail->addCC('thenrikson@nortongauss.com');

        // Handle file attachment if provided
        if (!empty($_FILES['userfile']['name'])) {
            $ext = PHPMailer::mb_pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
            $uploadfile = tempnam(sys_get_temp_dir(), hash('sha256', $_FILES['userfile']['name'])) . '.' . $ext;
            if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
                $mail->addAttachment($uploadfile, $_FILES['userfile']['name']);
            }
        }

        // Set email content
        $mail->isHTML(true);
        $mail->Subject = 'New Contact Form Submission';
        $mail->Body = $messages;

        // Send the email
        $mail->send();

        // Redirect to the language-specific success page
        header("Location: $successPage");
        exit;
    } catch (Exception $e) {
        // Redirect to the language-specific error page
        header("Location: $errorPage");
        exit;
    }
} else {
    $result = "error";
    $status = "No data received.";

    // Redirect to the default language error page
    header('Location: /error_email_EN.html');
    exit;
}
?>
