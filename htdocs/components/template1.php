		<style>
		</style>
		
		<!--  banner (conditional) -->
		<?php 
		$elt = $page->getImage('banner');
		$form = getCMSForm('banner', $elt);
		if($elt['src']){
		?>
		<div id="banner" <?php echo $form; ?>>
			<img src="<?php echo $elt['src']; ?>"/>
		</div>
		<?php } ?>
		
		<div id="content">
			<?php if(!$elt['src'] && _STAGING_){ ?>	
			<div id="no-banner-cms-ui" <?php echo $form; ?> class="cms-ui">Double click to add banner...</div>
			<?php } ?>
			<div class="no-columns2">
				<?php require('components/maincopy.php'); ?>
				
				<!-- some links -->
				<ul id="content-links">
					<li><a href="<?php echo Website::getPageURL('trips'); ?>">See our trips &raquo;</a></li>
					<li><a href="<?php echo Website::getPageURL('schedule'); ?>">Full schedule &raquo;</a></li>
					<li><a href="<?php echo Website::getPageURL('contact-us'); ?>">Contact us &raquo;</a></li>
				</ul>
			</div>
		</div>