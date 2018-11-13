<?php
require('_settings.php');

$log->setEcho(Logger::ECHO_ONLY);
$log->start("Test script");


try{
	$schds = Dataset::get($_db, 'schedule', $god);
	
	
		
} catch (Exception $e){
	$log->logException($e->getMessage());
}
$log->finish();
?>