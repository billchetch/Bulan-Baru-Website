<?php 
$emailName = 'emailb4trip';
$template = '../templates/emailb4trip.html';
$data = array();
try{
	$data['id'] = isset($_GET['scheduleid']) ? $_GET['scheduleid'] : null;
	$emailBody = createEmailBodyFromTemplate($_db, $_SERVER['HTTP_HOST'], $emailName, $template, $data);
	echo $emailBody;
} catch (Exception $e){
	echo $e->getMessage();
}
?>