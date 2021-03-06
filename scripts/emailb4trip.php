<?php
require('_settings.php');
$mailer = 'PHPMailer_v5.0.2';
require("$phplib/$mailer/class.phpmailer.php");
require($root.'/_funcs.php');

$log->setEcho(Logger::ECHO_AND_LOG);
$log->start("Email guests before trip");


$config = array(
	'send'=>1,
	'emailb4trip'=>60,
	'emailb4trip_template'=>realpath($root.'/../templates/emailb4trip.html'),
	'emailfrom'=>'info@bulan-baru.com',
	'emailfromname'=>'Bulan Baru',
	'emailsubject'=>'BULAN BARU - Your upcoming trip'
);


try{
	$mail = new PHPMailer(true);
	$mail->SetLanguage('en', "$phplib/$mailer/language/");
	$mail->IsSMTP();
	$mail->IsHTML(true);
	$mail->CharSet = 'UTF-8';
	if(defined('_SMTP_HOST_'))$mail->Host = _SMTP_HOST_;
	if(defined('_SMTP_SECURE_'))$mail->SMTPSecure = _SMTP_SECURE_;
	if(defined('_SMTP_PORT_'))$mail->Port = _SMTP_PORT_;
	if(defined('_SMTP_USERNAME_')){
		$mail->SMTPAuth = true; 
		$mail->Username = _SMTP_USERNAME_;
		$mail->Password = _SMTP_PASSWORD_;
	}
	
	$schds = Dataset::get($_db, 'schedule', $god);
	
	$rows = array();
	$filter = "depart_date>now() AND trp_schedule.active=1";
	$schds->select($rows, $filter, 'depart_date');
	$log->logInfo("Checking ".count($rows)." trips...");	
	foreach($rows as $r){
		$s = trim($r['email_list']);
		if(empty($s)){
			$log->logWarning("No email addresses attached to this trip schedule (".($r['id']).") so continuing...");
			continue;
		}
		$s = explode(',', $s);
		$emailaddresses = array();
		foreach($s as $ea){
			$ea = Validate::removeWhiteSpace($ea);
			if(!Validate::email2($ea, false)){
				$log->logWarning($ea.' is not a valid email address so skipping');
				continue;
			}
			array_push($emailaddresses, $ea);
		}
		if(count($emailaddresses) == 0){
			$log->logInfo("No email addresses to send email to so moving to next trip schedule");
			continue;
		}
		
		array_push($emailaddresses, 'info@bulan-baru.com');
		
		$days = Utils::dateDiff($r['depart_date'], date('Y-m-d'));
		if($days <= $config['emailb4trip']){ //allow a window
			
			$log->logInfo("Sending emailb4trip for schedule with ID ".$r['id']);
			$template = $config['emailb4trip_template'];
			$emailBody = createEmailBodyFromTemplate($_db, $domain, 'emailb4trip', $template, $r);
			$subject = $config['emailsubject'];
			$mail->Subject = $subject;
			$mail->Body = $emailBody;
			$mail->From = $config['emailfrom'];
			$mail->FromName = $config['emailfromname'];
				
			foreach($emailaddresses as $ea){
				$hisID = 'emailb4trip-'.$r['id'].'-'.$ea;
				$data = $his->restoreLatest($hisID);
				if(!empty($data)){
					$log->logInfo("Already sent email to $hisID so continuing...");
					continue; //already recorded as sent
				}
				
				$log->logInfo("Email not yet sent to $ea so preparing to send...");
				$mail->ClearAddresses();
				$mail->AddAddress($ea);
				if($config['send']){
					if($mail->Send()){
						$data = array('sent'=>date('Y-m-d H:i:s'));
						$data['recipient'] = $ea;
						$data['daysb4trip'] = $days;
						$id = $his->save($hisID, $data);
						$log->logInfo("Recorded emails as sent in system history ID $id");		
					} else {
						$log->logWarning("Failed to send to $ea");
					}
				} else {
					$log->logInfo("Sending to $ea");
				}
			}
		} else { //b4 trips email
			$log->logInfo("Trip with schedule ID ".$r['id']." starts in $days days which is more than the required minimum of ".$config['emailb4trip']." days");
		}
	}
	
	
	
} catch (Exception $e){
	$log->logException($e->getMessage());
}
$log->finish();
?>