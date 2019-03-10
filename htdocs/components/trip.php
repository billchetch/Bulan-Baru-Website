		<?php 
		$tid = $page->get('sid');
		$trip = getTrip($page, $tid);
		$thisYear =  date('Y');
		$years = array($thisYear, $thisYear + 1, $thisYear + 2);
		$seasons = array();
		$hasSchedule = false;
		foreach($years as $year){
			$seasons[$year] = array('schedule'=>null, 'year'=>$year);
			$seasons[$year]['schedule'] = getSchedule($page, $trip['id'], $year);
			if($seasons[$year]['schedule'] && count($seasons[$year]['schedule']))$hasSchedule = true;
		}	
		?>
		
		<style>
		#trip-map{
			width: 1000px;
			height: 300px;
			position: relative;
		}
		#schedule{
			margin-top: 40px;
		}
		#schedule table{
			border: 1px solid #eeeeee;
			cellspacing: 0;
			width: 100%;
			margin-bottom: 16px;
		}
		#schedule th{
			border-bottom: 1px solid #eeeeee;
			background-color: #cccccc;
			color: #333333;
		}
		#schedule th,td{
			padding: 2px 6px 2px 6px;
		}
		.si-not-available{
			color: #666666;
		}
		.trip-enquiry{
			padding-right: 8px;
			text-align: right;
		}
		#trip-content .column2 img{
			margin-bottom: 14px;
		}
		#trip-content h1{
			margin-top: 0px;
			margin-bottom: 8px;
			padding-top: 0px;
			padding-bottom: 0px;
			color: white;
		}
		#schedule .comments{
			font-size: 12px;
			font-style: italic;
		}
		#prev-next-trip{
			
		}
		
		
		/* wave */
		.location-1{ 
			width: 16px;
			height: 16px;
			position: absolute;
			display: block;
			cursor: pointer;
			border: 0;
		}
		/* scenic-spot */
		.location-2{ 
			width: 16px;
			height: 16px;
			position: absolute;
			display: block;
			cursor: pointer;
			border: 0;
		}
		/* harbour */
		.location-3{ 
			width: 22px;
			height: 28px;
			position: absolute;
			display: block;
			cursor: pointer;
			border: 0;
		}
		/* airport */
		.location-4{ 
			width: 21px;
			height: 21px;
			position: absolute;
			display: block;
			cursor: pointer;
			border: 0;
		}
		.location-image{
			display: block;
			margin-bottom: 6px;
			margin-top: 3px;
			width: 262px;
		}
		</style>
		
		<?php if($trip['map_image']){ ?>
		<!-- include qtip css and js -->
		<link rel="stylesheet" href="/lib/js/jquery/qtip/jquery.qtip.min.css" type="text/css" />
		<script type="text/javascript" src="/lib/js/jquery/qtip/jquery.qtip.min.js"></script>
		
		<div id="trip-map">
			<img src="<?php echo $trip['map_image']['src']; ?>"/>
		</div>
		<?php } ?>
		
		<script type="text/javascript">
		var locations = [];
		<?php 
		if($trip['map_image']){
			if(!empty($trip['locations'])){
				foreach($trip['locations'] as $location){
					echo "locations[locations.length] = ".json_encode($location).";\n";
				}
			}
		?>
		$.each(locations, function(idx, location){
				$elt = $('<img class="location-' + location.location_type + ' image-link"/>');
				$elt.attr('id', 'location-' + location.id);
				$elt.attr('src', '/images/location-icon-' + location.location_type + '.png');
				$elt.css('left', location.map_left + 'px');
				$elt.css('top', location.map_top + 'px');
				var qhtml = location.long_desc;
				if(location.image && location.image.src){
					qhtml = '<img class="location-image" src="' + location.image.src + '"/>' + qhtml;
				}
				var qdata = {
					content: {
						title: location.location_title,
						text: qhtml,
					},
					style: { classes: 'qtip-blue qtip-shadow qtip-rounded' },
					hide: {
				        fixed: true,
				        delay: 100
				    }
				};
				$elt.qtip(qdata);
				$('#trip-map').append($elt);
				$elt.on('mouseover', function(){
					//console.log(location.location_title);
					if(typeof ga != 'undefined' && ga)ga('send', 'event', 'icon', 'mouseover', location.location_title, 2);
				});
			});
		<?php } ?>
		</script>
		
		<div id="content">
			<div id="trip-content">
				<!-- column 1 -->
				<div class="column1" <?php echo getCMSForm('trip', $trip); ?>>
					<h1><?php echo $trip['title']; ?></h1>
					<?php echo $trip['long_desc']; ?>
					
					
					<div id="schedule">
						<?php 
						if(!$hasSchedule){ ?>
						<i>Unfortunately there are no scheduled dates available for this trip but we would be happy to organise a specialised charter.  Please <?php echo getResourceHTML('{CONTACT}'); ?> for futher information.</i>
						<?php } else {
							foreach($seasons as $year=>$season){
								$schedule = $season['schedule'];
								if(!$schedule || !count($schedule))continue;
								echo "<h3>SCHEDULE FOR $year</h3>";
								foreach($schedule as $si){
						?>
						
						<table class="schedule-item <?php echo $si['availablility'] == 'FULL' ? 'si-not-available' : ''; ?>" cellspacing="0">
							<tr>
								<th align="left" width="160px">Departs</th>
								<th align="left" width="160px">Arrives</th>
								<th align="center">Available</th>
								<th align="center">Price</th>
							</tr>
						
							<tr>
								<td width="160px"><?php echo $si['depart']; ?></td>
								<td width="160px"><?php echo $si['arrive']; ?></td>
								<td valign="top" align="center"><?php echo $si['availablility']; ?></td>
								<td valign="top" align="center"><?php echo $si['fprice']; ?></td>
							</tr>
							<tr>
								<td colspan="2">
								<?php if(!empty($si['comments'])){ ?>
								<div class="comments"><?php echo $si['comments']; ?></div>
								<?php } ?>
								</td>
								<td colspan="2" class="trip-enquiry si-not-available"><a href="<?php echo $si['href']; ?>"><span class="<?php echo $si['availablility'] == 'FULL' ? 'si-not-available' : ''; ?>">Enquire about this trip &raquo;</span></a></td>
							</tr>
						</table>
						<?php } 
							} //end year loop
						} //ehd schedule test?>
					</div>
					<br/><br/>
					<div id="prev-next-trip">
						<?php 
						$prevURL = '#';
						$nextURL = '#';
						$trips = getTrips($page);
						for($i = 0; $i < count($trips); $i++){
							$t = $trips[$i];
							if($t['id'] == $trip['id']){
								$prevURL = $trips[$i == 0 ? count($trips) - 1 : $i - 1]['href'];
								$nextURL = $trips[$i == count($trips) - 1 ? 0 : $i + 1]['href'];
								break;
							}
						}
						?>
						<a href="<?php echo $prevURL; ?>">&laquo; previous trip</a>&nbsp;&nbsp;
						<a href="<?php echo $nextURL; ?>">next trip &raquo;</a>
					</div>
					<ul id="content-links">
						<li><a href="<?php echo Website::getPageURL('trips'); ?>">See all trips &raquo;</a></li>
						<li><a href="<?php echo Website::getPageURL('bookings'); ?>">Make an enquiry &raquo;</a></li>
						<li><a href="<?php echo Website::getPageURL('schedule'); ?>">See full schedule &raquo;</a></li>
					</ul>
				</div>
				
				<!-- column 2 -->
				<div id="trip-column" class="column2">
					<?php foreach($trip['small_images'] as $img){ 
						$caption = !empty($img['caption']) ? $img['caption'] : '';
					?>
					<div><a class="fancybox image-link" href="<?php echo $img['lg_src']; ?>" data-fancybox-group="gallery" title="<?php echo $caption; ?>"><img class="trip-side" src="<?php echo $img['sm_src']; ?>" alt="<?php echo $caption; ?>"/></a></div>
					<?php } ?>
					<script type="text/javascript">
					$('#trip-column').find('a').on('click', function() {
						if(typeof ga != 'undefined' && ga)ga('send', 'event', 'image', 'click', 'trip-thumb', 2);
					});
					</script>
				</div>
				
				<div style="clear: both"></div>
			</div>
		</div>