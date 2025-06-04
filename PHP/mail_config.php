<?php
require __DIR__ . '/../vendor/autoload.php';

$mail = new PHPMailer\PHPMailer\PHPMailer(true);

// Mailtrap configuration
$mail->isSMTP();
$mail->Host = 'sandbox.smtp.mailtrap.io';
$mail->SMTPAuth = true;
$mail->Username = 'c92d422846f55b';
$mail->Password = '0e2a021d569f59';
$mail->Port = 2525;
$mail->SMTPSecure = 'tls';

// Email settings
$mail->setFrom('noreply@pinyatapet.infinityfreeapp.com', 'Pet Hotel');
$mail->isHTML(false);
?>