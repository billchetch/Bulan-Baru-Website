		<style>
		#trips td{
			vertical-align: top;
			padding-top: 12px;
			padding-bottom: 12px;
			padding-right: 12px;
		}
		#trips{
			
		}
		#trips table{
			width: 100%;
		}
		#trips .trip-desc p{
			margin-bottom: 0px;
			padding-bottom: 0px;
		}
		</style>
		
		<div id="content">
			<div id="trips" class="no-columns">
				<table>
					<?php 
					$trips = getTrips($page);
					foreach($trips as $trip){
						$src = $trip['thumb']['src'];
					?>
					<tr <?php echo getCMSForm('trip', $trip); ?>>
						<td>
							<a href="<?php echo $trip['href']; ?>" class="image-link"><img src="<?php echo $src; ?>" class="trip-thumb"/></a>
						</td>
						<td class="trip-desc">
							<h4><a href="<?php echo $trip['href']; ?>"><?php echo $trip['title']; ?></a></h4>
							<p>
							<?php echo $trip['short_desc']; ?>
							</p>
							<div style="text-align: right">
								<a href="<?php echo $trip['href']; ?>">Find out more about this trip &raquo;</a>
							</div>
						</td>
					</tr>
					<?php } ?>
				</table>
			</div>
		</div>