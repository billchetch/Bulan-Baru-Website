		<style>
		#team-members{
			margin-top: 30px;
		}
		
		.team-member{
			margin-bottom: 26px;
		}
		.team-member-img{
			width: 250px;
			height: 250px;
			border: 1px solid #ffffff;
		}
		
		.team-member-blurb{
			padding: 6px;
			padding-left: 12px;
		}
		.team-member-blurb h3{
			padding: 0px;
			margin: 0px;
		}
		.fb, .ig{
			margin-right: 8px;
		}
		.content-copy{
			margin-bottom: 12px;
		}
		</style>
		
		<div id="content">
			<div id="team" class="no-columns">
				<h2>TEAM BULAN BARU</h2>
				<?php require('components/maincopy.php'); ?>
				
				<div id="team-members">
					<?php 
					$team = array();
					$team[] = array('id'=>'tai', 'name'=>"Tai 'Buddha' Graham", 'src'=>'tai.png', 'links'=>array('ig'=>'https://instagram.com/taibuddha/', 'fb'=>'https://www.facebook.com/tai.graham?fref=ts'));
					$team[] = array('id'=>'mikala', 'name'=>"Mikala Jones", 'src'=>'mikala.png', 'links'=>array('ig'=>'https://instagram.com/mikalajones/'));
					$team[] = array('id'=>'marlon', 'name'=>"Marlon Gerber", 'src'=>'marlon.png', 'links'=>array('ig'=>'https://instagram.com/marlongerber/'));
					//$team[] = array('id'=>'awan', 'name'=>"Awan 'Desert Point' Hadi", 'src'=>'awan.png', 'links'=>array('ig'=>'https://instagram.com/awan_desertpoint/'));
					$team[] = array('id'=>'josh', 'name'=>"Joshua Ellard Garner", 'src'=>'josh.jpg', 'links'=>array('ig'=>'https://instagram.com/joshuaellardgarner/', 'fb'=>'https://www.facebook.com/profile.php?id=100008945797247'));
					$team[] = array('id'=>'massey', 'name'=>"Simon Massey", 'src'=>'massey.jpeg', 'links'=>array('ig'=>'https://www.instagram.com/masseysurfboards/'));
					$team[] = array('id'=>'rom', 'name'=>"Romulo Arantes Neto", 'src'=>'romulo.png', 'links'=>array('ig'=>'https://www.instagram.com/romuloarantesneto/'));
					
					$i = 0;
					foreach($team as $member){
					?>
					<div class="team-member">
						<table>
							<tr>
								<td valign="top"><img src="/images/team/<?php echo $member['src']; ?>" class="team-member-img"/></td>
								<td valign="top" class="team-member-blurb">
									<h3><?php echo $member['name'];?></h3>
									<?php 
									$elt = $page->getElement('team-copy-'.$member['id']);
									$copy = getElementCopy($page, $elt);
									?>
									<div <?php echo getCMSForm('copy', $elt); ?> class="content-copy">
										<?php echo $copy ? $copy : 'Copy goes here...'; ?>
									</div>
									<?php if(!empty($member['links'])){ 
										$linkLabels = array();
										$linkLabels['fb'] = 'Facebook';
										$linkLabels['ig'] = 'Instagram';
										foreach($member['links'] as $type=>$url){
											$label = $linkLabels[$type];
											$src = "/images/$type.png";
									?>
									<div>
										<a href="<?php echo $url; ?>"><img src="<?php echo $src; ?>" class="<?php echo $type; ?>"><?php echo $label; ?></a>
									</div>
									<?php } } ?>	
								</td>
							</tr>
						</table>
					</div>
					<?php } ?>
				</div>
				
				<ul id="content-links">
					<li><a href="<?php echo Website::getPageURL('trips'); ?>">See our trips &raquo;</a></li>
					<li><a href="<?php echo Website::getPageURL('schedule'); ?>">Full schedule &raquo;</a></li>
					<li><a href="<?php echo Website::getPageURL('contact-us'); ?>">Contact us &raquo;</a></li>
				</ul>
			</div>
		</div>