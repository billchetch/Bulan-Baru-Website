		
		<div id="slides">
		<?php 
		$images = getGalleryImages($page, 'slides');
		foreach($images as $img){
			$alt = $img['caption'] ? $img['caption'] : 'Another unique trip with Bulan Baru!';
		?>
			<img src="<?php echo $img['xl_src']; ?>" alt="<?php echo $alt; ?>" class="slides-image"/>
		<?php } ?>
		</div>
		<script type="text/javascript">
		slideshows.slides = {
				width: 1000,
		        height: 350,
		        navigation: {
		          effect: "fade"
		        },
		        pagination: false,
		        effect: {
		            fade: {
		              speed: 800
		            }
		        },
		        play: {
		            active: true,
		            auto: true,
		            interval: 4000,
		            effect: "fade"
		          }
		      };
	      
		slideshows.trippromoslides = {
				width: 300,
		        height: 230,
		        navigation: false,
		        pagination: false,
		        effect: {
		            fade: {
		              	speed: 800
		            	}
		        	}
				};
		</script>
		<style>
		#slides-cms-ui{
			border: 1px solid blue;
			margin-top: -16px; 
			margin-left: 30px;
		}
		#testimonials{
			font-size: 13px;
			color: #999999;
		}
		#vid-feature, #vid-feature2{
			margin-top: 6px;
		}
		</style>
		<div id="content">
			<?php if(_STAGING_){ ?>
			<div id="no-banner-cms-ui" <?php echo getCMSForm('slides-gallery'); ?> class="cms-ui">Double click to modify slides...</div>
			<?php } ?>
				
			<!-- column 1 -->
			<div class="column1">
				<?php require('components/maincopy.php'); ?>
				<?php 
				$elt = $page->getElement('testimonials');
				$copy = getElementCopy($page, $elt);
				?>
				<div id="testimonials" <?php echo getCMSForm('copy', $elt); ?> class="content-copy">
					<?php echo $copy ? $copy : 'Testimonials go here...'; ?>
				</div>
				<ul id="content-links">
					<li><a href="<?php echo Website::getPageURL('trips'); ?>">Check out our trips &raquo;</a></li>
					<li><a href="<?php echo Website::getPageURL('testimonials'); ?>">Testimonials &raquo;</a></li>
					<li><a href="<?php echo Website::getPageURL('bookings'); ?>">Make an enquiry &raquo;</a></li>
					<li><a href="<?php echo Website::getPageURL('schedule'); ?>">See full schedule &raquo;</a></li>
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
					<h4><a class="fancybox" href="#vid-promo-embed"><?php echo $elt['image_title']; ?></a></h4>
					<a class="fancybox image-link" href="#vid-promo-embed"><img src="<?php echo $elt['src']; ?>" class="vid-thumb" alt="Bulan Baru Surfcharters video promotion"/></a>
				</div>
				<div id="vid-promo-embed" style="display: none">
				<?php echo $elt['image_caption']; ?>
				</div>
				<?php
				} 
				?>
				<?php if(empty($elt['src']) && _STAGING_){ ?>	
				<div id="no-vid-promo-cms-ui" <?php echo $form; ?> class="cms-ui">Double click...</div>
				<?php } ?>
				
				<!--  vid feature (conditional) -->
				<?php 
				$elt = $page->getElement('vid-feature');
				$form = getCMSForm('vid-promo', $elt);
				$img = $page->getImageFromId($elt['row_id']);
				$elt['src'] = $img && $img['src'] ? $img['src'] : null;
				$elt['alt_text'] = $img['alt_text'];
				if(!empty($elt['src'])){
				?>
				<div id="vid-feature" <?php echo $form; ?>>
					<h4><a class="fancybox" href="#vid-feature-embed"><?php echo $elt['image_title']; ?></a></h4>
					<a class="fancybox image-link" href="#vid-feature-embed"><img src="<?php echo $elt['src']; ?>" class="vid-thumb" alt="Bulan Baru Surfcharters video feature"/></a>
				</div>
				<div id="vid-feature-embed" style="display: none">
				<?php echo $elt['image_caption']; ?>
				</div>
				<?php
				} 
				?>
				<?php if(empty($elt['src']) && _STAGING_){ ?>	
				<div id="no-vid-feature-cms-ui" <?php echo $form; ?> class="cms-ui">Double click...</div>
				<?php } ?>
				
				<!--  vid feature 2 (conditional) -->
				<?php 
				$elt = $page->getElement('vid-feature2');
				$form = getCMSForm('vid-promo', $elt);
				$img = $page->getImageFromId($elt['row_id']);
				$elt['src'] = $img && $img['src'] ? $img['src'] : null;
				$elt['alt_text'] = $img['alt_text'];
				if(!empty($elt['src'])){
				?>
				<div id="vid-feature2" <?php echo $form; ?>>
					<h4><a class="fancybox" href="#vid-feature2-embed"><?php echo $elt['image_title']; ?></a></h4>
					<a class="fancybox image-link" href="#vid-feature2-embed"><img src="<?php echo $elt['src']; ?>" class="vid-thumb" alt="Bulan Baru Surfcharters video feature"/></a>
				</div>
				<div id="vid-feature2-embed" style="display: none">
				<?php echo $elt['image_caption']; ?>
				</div>
				<?php
				} 
				?>
				<?php if(empty($elt['src']) && _STAGING_){ ?>	
				<div id="no-vid-feature-cms-ui" <?php echo $form; ?> class="cms-ui">Double click...</div>
				<?php } ?>
				
				
				<hr style="margin-top: 24px; margin-bottom: 24px"/>
				<div id="trippromoslides">
					<?php 
					$trips = getTrips($page, true);
					foreach($trips as $trip){
					?>
					<div class="trip-slide">
						<h4><a href="<?php echo $trip['href']; ?>"><?php echo $trip['title']; ?></a></h4>
						<a href="<?php echo $trip['href']; ?>" class="image-link"><img src="<?php echo $trip['thumb']['src']; ?>" class="trip-thumb"/></a>
						<p>
						<?php echo $trip['promo_desc']; ?>...<a href="<?php echo $trip['href']; ?>">more</a><br/>
						</p>
					</div>
					<?php } ?>
						<a href="#" class="slidesjs-previous slidesjs-navigation">&laquo; prev</a>
      					<a href="#" class="slidesjs-next slidesjs-navigation">next &raquo;</a>
					</div>
					<div style="margin-top: -21px; float: right;">
						<a href="<?php echo Website::getPageURL('trips'); ?>">see all &raquo;</a>
					</div>
					<div style="clear: both"/>
				</div>
				
				<hr style="margin-top: 24px; margin-bottom: 24px"/>
				
				<?php 
				$elt = $page->getElement('side-copy');
				$copy = getElementCopy($page, $elt);
				?>
				<div id="feature" <?php echo getCMSForm('copy', $elt); ?>>
					<?php echo $copy ? $copy : 'Copy goes here...'; ?>
				</div>
			</div>
			
			<div style="clear: both"></div>
		</div> <!--  end content -->
		<script type="text/javascript">
			$('#vid-promo').find('a').on('click', function() {
				if(ga)ga('send', 'event', 'image', 'click', 'video-promo', 1);
			});
			$('#vid-feature').find('a').on('click', function() {
				if(ga)ga('send', 'event', 'image', 'click', 'video-feature', 1);
			});
			$('#trippromoslides').find('.trip-slide').find('a').on('click', function() {
				if(ga)ga('send', 'event', 'image', 'click', 'trip-promo', 1);
			});
		</script>