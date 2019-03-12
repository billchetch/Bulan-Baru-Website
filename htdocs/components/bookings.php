		<style>
		
		</style>
		
<?php 
$fm = null;
$fm = isset($_GET['new']) ? null : HTMLForm::restore('bookings');
if(!$fm){
	$fm = new HTMLForm('bookings', Website::getPageURL($sid, 'action=booking'), 'POST');
	$fm->addField('name', 'Your name', true);
	$fm->addField('email', 'Your email address', true, 'email');
	$fm->addField('schedule', 'Trip', false, 'select');
	$fm->addField('comments', 'Comments', false, 'textarea', true, isset($_GET['comments']) ? $_GET['comments'] : null);
	$fm->save();
}
?>		
		<div id="content">
			<div id="bookings" class="no-columns2">
				<?php 
				if(isset($_GET['action']) && $_GET['action'] == 'booking' && !$fm->hasErrors()){
					echo '<div class="action-completed"><h4>Thank you for your enquiry. An email has been sent to '._EMAIL_.'. We will be in touch soon</h4></div>';
				} else { ?>
				<h2>BOOKINGS</h2>
				<?php require('components/maincopy.php'); ?>
				<?php 
				$ff = $fm->getFields();
				if($fm->hasErrors()){
					$err = $fm->error ? $fm->error : 'We are missing some information. Please fill in the marked fields below.';
				?>
				<div class="error">
					<h4><?php echo $err; ?></h4>
				</div>
				<?php } ?>
				<form action="<?php echo $fm->action; ?>" method="<?php echo $fm->method; ?>">
				<table>
				<?php foreach($ff as $f ){?>
					<tr>
						<td class="<?php echo $f['status'] != 0 ? 'field-error' : ''; ?>">
							<?php echo $f['label']; ?>
						</td>
						<td>
						<?php
						$html = '';
						switch($f['type']){
							case 'textarea':
								$html = '<textarea name="'.$f['name'].'">'.$f['value'].'</textarea>';
								break;
							case 'select':
								$data = $f['name'] == 'schedule' ? getSchedule($page) : array();
								$scid = empty($_GET['schedule']) ? ($f['value'] ? $f['value'] : '') : $_GET['schedule'];
								$html = '<select id="'.$f['name'].'" name="'.$f['name'].'">';
								$html.= '<option value="">-- Select a trip --</option>';
								$season = $data[0]['season'];
								foreach($data as $d){
									if($d['season'] != $season){
										$html.= '<option value="" disabled/>-----------</option>';
										$season = $d['season'];
									}
									$selected = $d['id'] == $scid ? ' selected' : '';
									$html.= '<option value="'.$d['id'].'" '.$selected.'/>'.$d['trip_and_dates'].'</option>';
								}
								$html.= '</select>';
								break;
							default:
								$html = '<input type="text" name="'.$f['name'].'" value="'.$f['value'].'"/>';
								break;
						}
						echo $html;
						?>
						</td>
					</tr>
				<?php } ?>
					<tr>
						<td>
						</td>
						<td>
							<?php echo getResourceHTML('submit-button', 'Send enquiry'); ?>
						</td>
				</table>
				</form>
				<?php }  ?>
				<ul id="content-links">
					<li><a href="<?php echo Website::getPageURL('private-charter'); ?>">Group bookings &raquo;</a></li>
					<li><a href="<?php echo Website::getPageURL('trips'); ?>">See more trips &raquo;</a></li>
					<li><a href="<?php echo Website::getPageURL('terms-and-conditions'); ?>">Terms and conditions &raquo;</a></li>
					<li><a href="<?php echo Website::getPageURL('checklist'); ?>">Checklist &raquo;</a></li>
				</ul>
				
			</div>
		</div>