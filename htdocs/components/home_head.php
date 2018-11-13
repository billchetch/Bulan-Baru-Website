		
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
		        height: 400,
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
	      
		slideshows.trippromoslides =	{
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
			margin-top: -16px; 
			margin-left: 30px;
		}
		</style>