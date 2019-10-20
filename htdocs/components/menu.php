<style>
.vid-thumb{
	width: 258px;
	/*height: 120px;*/
	border: 1px solid #eeeeee;
	margin-left: 16px;
	margin-bottom: 12px;
}
.side-image-ctn{
	float: left;
	width: 120px;
	height: 80px;
	border: 1px solid #eeeeee;
	margin-left: 16px;
	margin-bottom: 16px;
	height: 80px;
}
.side-image-ctn img{
	width: 120px;
	height: 80px;
}
#no-vid-promo-cms-ui{
	text-align: right;
	margin-bottom: 12px;
}
</style>

<script type="text/javascript">

</script>

		<!--  banner (conditional) -->
		<?php 
		$elt = $page->getImage('banner');
		$form = getCMSForm('banner', $elt);
		if($elt['src']){
		?>
		<div id="banner" <?php echo $form; ?>>
			<img src="<?php echo $elt['src']; ?>" width="1000" height="250"/>
		</div>
		<?php } ?>
		<div id="content">
			<?php if(!$elt['src'] && _STAGING_){ ?>	
			<div id="no-banner-cms-ui" <?php echo $form; ?> class="cms-ui">Double click to add banner...</div>
			<?php } ?>
			<!-- column 1 -->
			<div class="column1">
				<h2>WHAT'S COOKING...</h2>
				<?php require('components/maincopy.php'); ?>
				
				<!-- some links -->
				<ul id="content-links">
					<li><a href="<?php echo Website::getPageURL('trips'); ?>">Check out our trips &raquo;</a></li>
					<li><a href="<?php echo Website::getPageURL('bookings'); ?>">Make an enquiry &raquo;</a></li>
					<li><a href="<?php echo Website::getPageURL('schedule'); ?>">Full schedule &raquo;</a></li>
				</ul>
				
			</div>
			
			<!-- column 2 -->
			<div class="column2">
				<!--  vid promo (conditional) -->
				<?php 
				$elt = $page->getElement('vid-promo');
				$form = getCMSForm('vid-promo', $elt);
				$img = $page->getImageFromId($elt['row_id']);
				$elt['src'] = $img && $img['src'] ? $img['src'] : null;
				$elt['alt_text'] = $img['alt_text'];
				if(!empty($elt['src'])){
				?>
				<div id="vid-promo" <?php echo $form; ?>>
					<a class="fancybox image-link" href="#vid-embed" data-fancybox-group="gallery"><img src="<?php echo $elt['src']; ?>" class="vid-thumb" alt="Bulan Baru Boat video"/></a>
				</div>
				<div id="vid-embed" style="display: none">
				<?php echo $elt['image_caption']; ?>
				</div>
				<?php
				} 
				?>
				<?php if(empty($elt['src']) && _STAGING_){ ?>	
				<div id="no-vid-promo-cms-ui" <?php echo $form; ?> class="cms-ui">Double click to add vid...</div>
				<?php } ?>
			
				<div id="side-images">
				<?php 
				$images = getGalleryImages($page, 'menu');
				foreach($images as $img){
					$caption = $img['caption'] ? $img['caption'] : '';
				?>
					<div class="side-image-ctn"><a class="fancybox image-link" href="<?php echo $img['md_src']; ?>" data-fancybox-group="gallery" title="<?php echo $caption; ?>"><img src="<?php echo $img['xs_src']; ?>" alt="<?php echo $caption; ?>"/></a></div>
				<?php } ?>
					<div style="clear: both"></div>
				</div>
				<script type="text/javascript">
				$('#side-images').find('a').on('click', function() {
					if(ga)ga('send', 'event', 'image', 'click', 'menu', 1);
				});
				</script>
				<?php if(_STAGING_){ ?>
					<div class="cms-ui" <?php echo getCMSForm('menu-gallery'); ?> style="text-align: right">Double click to change gallery...</div>
				<?php } ?>
			</div>
			
			<div style="clear: both"></div>
		</div>