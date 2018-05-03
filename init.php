<?php
ini_set("display_errors", true);
error_reporting(E_ALL);

include_once('config.php');
include_once('SpaceCleaner.php');
include_once('Node.php');

function handleError(Exception $exception)
{
	echo "\nERROR:".$exception->getMessage()."\n";

	if (defined('SEND_ERRRORS_TO_EMAIL') && SEND_ERRRORS_TO_EMAIL) {
		sendEmailsNotifocation("Текст ошибки: ".$exception->getMessage());
	}

	die();
}

function isProdaction()
{
	return defined('ENVIROMENT') && ENVIROMENT == 'PRODACTION';
}

function sendEmailsNotifocation($text)
{
	$adminEmails = explode(',', ADMIN_EMAILS);

	require_once PHPMAILER_PATH;

	$mail = new PHPMailer();
	$mail->Hostname = MAIL_HOST;

	$mail->CharSet = 'UTF-8';
	$mail->setLanguage('ru');

	$mail->isSMTP();
	$mail->Host = MAIL_HOST;
	$mail->Port = MAIL_PORT;
	$mail->SMTPAuth = true;
	$mail->Username = MAIL_FROM;
	$mail->Password = MAIL_PASS;
	$mail->SMTPSecure = MAIL_CERT;

	$mail->SMTPOptions = [
		'ssl' => [
			'verify_peer'  => false,
			'verify_peer_name'  => false,
			'allow_self_signed' => true
		]
	];

	$mail->From = MAIL_FROM;
	$mail->FromName = 'SpaceCleaner';

	foreach ($adminEmails as $email) {
		$mail->addAddress(trim($email));
	}

	$body = emailTemplate($text);
	
	$mail->isHTML(true);

	$mail->Subject = "Ошибка во время плановой очистки места";
	$mail->Body    = $body;
	$mail->AltBody = strip_tags($body);


	$res = $mail->send();

	return ($res == TRUE ? TRUE : FALSE);
}

function emailTemplate($text)
{
	return '
		<div style="border:3px solid #17356b;padding:10px 15px;">
			<div style="color:#1A3C7B;font-size:20px;margin-bottom:15px;">Ошибка во время плановой очистки места</div>

			<div style="margin:15px 0">
				"<b>'.$text.'</b>"
			</div>
		</div>';
}