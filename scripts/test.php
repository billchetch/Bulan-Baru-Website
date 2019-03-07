<?php
require('_settings.php');

$log->setEcho(Logger::ECHO_ONLY);
$log->start("Test script");


try{
	$email = 'xxx'; //@x.com';
	Validate::email($email);
	
		
} catch (Exception $e){
	$log->logException($e->getMessage());
}
$log->finish();
?>