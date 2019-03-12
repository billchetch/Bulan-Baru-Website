<?php
require('_settings.php');

$log->setEcho(Logger::ECHO_ONLY);
$log->start("Test script");


try{
	$mailer = 'PHPMailer_v5.0.2';
	require("$phplib/$mailer/class.phpmailer.php");
	require($root.'/_funcs.php');

	$mail = new PHPMailer(true);
	$mail->SetLanguage('en', "$phplib/$mailer/language/");
	$mail->IsHTML(true);
	$mail->CharSet = 'UTF-8';
	
	$mail->Subject = "test";
	$mail->Body = "test";
	$mail->From = 'info@bulan-baru.com';
	$mail->FromName = 'BB';
	
	echo $mail->Send();
		
} catch (Exception $e){
	$log->logException($e->getMessage());
}
$log->finish();
?>