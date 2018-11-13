		<?php 
		$thisYear =  date('Y');
		$years = array($thisYear, $thisYear + 1, $thisYear + 2);
		$seasons = array();
		foreach($years as $year){
			$seasons[$year] = array('schedule'=>null, 'year'=>$year);
			$seasons[$year]['schedule'] = getSchedule($page, null, $year);
		}		
		?>
		<style>
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
			padding: 4px 6px 4px 6px;
		}
		#schedule tr:nth-child(odd){
			background-color: #000000;
		}
		#schedule tr:nth-child(even){
			background-color: #333333;
		}
		.trip-enquiry{
			padding-right: 8px;
			text-align: right;
		}
		#schedule{
			
		}
		#schedule .comments{
			font-size: 12px;
			font-style: italic;
		}
		h3{
			margin-bottom: 4px;
		}
		</style>
		
		<div id="content">
			<div id="schedule" class="no-columns">
				<h2>SCHEDULE</h2>
				<?php require('components/maincopy.php'); ?>
				
				<?php
				foreach($seasons as $year=>$season){
					$schedule = $season['schedule'];
					if(!$schedule || !count($schedule))continue;
					echo "<h3>Trips for $year</h3>";
				?>
				<table class="schedule-item" cellspacing="0">
					<tr>
						<th align="left" width="320px">Trip</th>
						<th align="left" width="120px">Departs</th>
						<th align="left" width="120px">Arrives</th>
						<th align="center">Available</th>
						<th align="center">Price</th>
						<th align="right"></th>
					</tr>
				<?php foreach($schedule as $si){ 
						$trip = getTrip($page, $si['trip_id']);
						?>
				
					<tr>
						<td valign="top" width="320px">
							<a href="<?php echo $trip['href']; ?>"><?php echo $si['trip']; ?></a>
							<?php if(!empty($si['comments'])){ ?>
							<div class="comments"><?php echo $si['comments']; ?></div>
							<?php } ?>
						</td>
						<td><?php echo $si['depart']; ?></td>
						<td><?php echo $si['arrive']; ?></td>
						<td valign="top" align="center"><?php echo $si['availablility']; ?></td>
						<td valign="top" align="center"><?php echo $si['fprice']; ?></td>
						<td valign="middle"><a href="<?php echo $si['href']; ?>">Enquire &raquo;</a></td>
					</tr>
				<?php } ?>
				</table>
				<?php } // end seasons loop?>
				<ul id="content-links">
					<li><a href="<?php echo Website::getPageURL('trips'); ?>">See more trips &raquo;</a></li>
					<li><a href="<?php echo Website::getPageURL('bookings'); ?>">Make an enquiry &raquo;</a></li>
					<li><a href="<?php echo Website::getPageURL('terms-and-conditions'); ?>">Terms and conditions &raquo;</a></li>
					<li><a href="<?php echo Website::getPageURL('checklist'); ?>">Checklist &raquo;</a></li>
				</ul>
			</div>
		</div>