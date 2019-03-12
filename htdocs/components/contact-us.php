		<style>
		</style>
		
		<div id="content">
			<div id="contact-us" class="no-columns2">
				<?php
				$fm = null;
				$fm = isset($_GET['new']) ? null : HTMLForm::restore('contact');
				if(!$fm){
					$fm = new HTMLForm('contact', Website::getPageURL($sid, 'action=contact'), 'POST');
					$fm->addField('name', 'Your name', true);
					$fm->addField('email', 'Your email address', true, 'email');
					$fm->addField('comments', 'Comments', true, 'textarea', true, isset($_GET['comments']) ? $_GET['comments'] : null);
					$fm->save();
				} 
				if(isset($_GET['action']) && $_GET['action'] == 'contact' && !$fm->hasErrors()){
					echo '<div class="action-completed"><h4>Thank you for getting in touch, your email has been sent to '._EMAIL_.'.  We will reply shortly. </h4></div>';
				} else { ?>
				<h2>CONTACT US</h2>
				<?php require('components/maincopy.php'); ?>
				<?php 
				$ff = $fm->getFields();
				if($fm->hasErrors()){ ?>
				<div class="error">
					<?php 
					$err = $fm->error ? $fm->error : 'We are missing some information. Please fill in the marked fields below.';
					?>
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
					<li><a href="<?php echo Website::getPageURL('trips'); ?>">See our trips &raquo;</a></li>
					<li><a href="<?php echo Website::getPageURL('schedule'); ?>">See full schedule &raquo;</a></li>
				</ul>
			</div>
		</div>