<?php
// Start of script, no output before this point
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include required files
require 'inc/Exception.php';
require 'inc/PHPMailer.php';
require 'inc/SMTP.php';
require 'join_us_forms_settings.php';

// Initialize variables
$result = "";
$rfile = "";
$status = "";

// Check if POST data exists
if (!empty($_POST)) {
    $name = $_POST['name'];
    $tel = $_POST['tel'];
    $email = $_POST['email'];
    $position = $_POST['position'];
    $message = $_POST['message'];

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

    // Ensure an attachment is provided
    if (empty($_FILES['cv']['name'])) {
        // Redirect to the error page if no attachment is provided
        header("Location: $errorPage");
        exit;
    }

    // Build the email message
    $messages  = "<h3>New CV Submission</h3> \r\n";
    $messages .= "<ul>
                    <li><strong>Name:</strong> $name</li>
                    <li><strong>Email:</strong> $email</li>
                    <li><strong>Phone:</strong> $tel</li>
                    <li><strong>Position:</strong> $position</li>
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

        // Handle file attachment
        $ext = PHPMailer::mb_pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);
        $uploadfile = tempnam(sys_get_temp_dir(), hash('sha256', $_FILES['cv']['name'])) . '.' . $ext;
        if (move_uploaded_file($_FILES['cv']['tmp_name'], $uploadfile)) {
            $mail->addAttachment($uploadfile, $_FILES['cv']['name']);
        }

        // Set email content
        $mail->isHTML(true);
        $mail->Subject = 'New CV Submission';
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
