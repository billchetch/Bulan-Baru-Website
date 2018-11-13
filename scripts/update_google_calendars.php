<?php 
function getSharedData($event, $property = null){
	if(!$event || !is_object($event))throw new Exception("Event is not of correct type ".var_export($event, true));
	$xp = $event->getExtendedProperties();
	if(!$xp)return null;
	$shared = $xp->getShared();
	if(!$shared)return null;
	
	if($property){
		return isset($shared[$property]) ? $shared[$property] : null;
	} else {
		return $shared;
	}
}

function createCalendarEvent($eid, $summary, $sdt, $edt, $timezone = 'Asia/Makassar'){
	//build up a picture of the event from the request
	$ce = new Event();
			
	//set properties so we can link this google object to a rogo object 
	$xp = new EventExtendedProperties();
	$d['eid'] = $eid;
	$xp->setShared($d);
	$ce->setExtendedProperties($xp);

	$dt = new EventDateTime();
	$dt->setTimeZone($timezone);
	$dt->setDate($sdt);
	$ce->setStart($dt);
	
	$dt = new EventDateTime();
	$dt->setTimeZone($timezone);
	$dt->setDate(date('Y-m-d', strtotime($edt) + 25*3600));
	$ce->setEnd($dt);
	
	$ce->setSummary($summary);
	
	global $eventCreator;
	$ce->setCreator($eventCreator);
	
	return $ce;
}

function addEvents2Calendar(&$calendars, $calendarId, $events){
	if(!isset($calendars[$calendarId]))$calendars[$calendarId] = array();
	foreach($events as $eid=>$event){
		$calendars[$calendarId][$eid] = $event;
	}
}


require('_settings.php');
set_time_limit(30*60);

//$log->setEcho(Logger::ECHO_AND_LOG);
$log->setEcho(Logger::ECHO_ONLY);
$log->start('update google calendars');
try{
	$apiClient = GoogleAPIClient::create('BULANBARU');
	
	//add services here (before authentication)
	
	//authenticate: 'false' means do not try and obtain new token if one doesn't exist in sys history
	GoogleAPIClient::authenticate($apiClient, $his, false);
	
	$calendarService = new apiCalendarService($apiClient);
	
	//datasets
	$schds = Dataset::get($_db, 'schedule', $god);
	
	//some settings
	$clear = false; //if set to true it will delete all the calendar events and set the requests in the calendar window back to non-synchronised
	if($clear)$log->logInfo("Clearing all!...");
	$cpd = array(4*30, 2*365); //days before and after today for calendar window
	
	//set the calendar window to look at
	$secsInDay = 24*60*60;
	$st = time() - $cpd[0]*$secsInDay;
	$et = time() + $cpd[1]*$secsInDay;
	$csd = date('Y-m-d', $st);
	$ced = date('Y-m-d', $et);
	
	//calendar parameters
	$params = array();
	$params['timeMin'] = $csd.'T00:00:00+0000';
	$params['timeMax'] = $ced.'T00:00:00+0000';
	$params['maxResults'] = 999999; //default is 250 and can easily be too small
	$log->logInfo("Get google calendar events using period: ".$params['timeMin'].' to '.$params['timeMax']);
	
	//set google event creator
	$eventCreator = new EventCreator();
	$eventCreator->setEmail('bill@bulan-baru.com');
	$eventCreator->setDisplayName('BULANBARU');
	
	//a list of calendars to events
	$calendars = array();
	$events2add = array();
	
	//generate the 'true' set of events from 'schedule' dataset
	$events = array();
	$rows = array();
	$filter = "depart_date<='$ced' AND arrive_date>='$csd' AND trp_schedule.active=1";
	$schds->select($rows, $filter, 'depart_date');
	$log->logInfo("Found ".count($rows)." active trips in the schedule relevant to the calendar window");
	foreach($rows as $row){
		$id = $row['id'];
		$sd = $row['depart_date'];
		$ed = $row['arrive_date'];
		$status = BBSchedule::getBookingStatus($row['booking_status']);
		
		$summary = "Trip: ".$row['depart_from'].' -> '.$row['arrive_at']." (".$status['status'].")";
		$lf = "\n";
		$desc = "Spaces available: ".$row['spaces_available'].$lf;
		if(!empty($row['booking_name']))$desc.= "Booked by: ".$row['booking_name'].$lf;
		$desc.= "Notes: ".$row['notes'];
		
		$eid = md5('schedule-'.$id.'-'.$sd.'-'.$ed.'-'.$summary.'-'.$desc);
		$ge = createCalendarEvent($eid, $summary, $sd, $ed);
		$ge->setColorId($status['google_color']); //set according to status
		$ge->setDescription($desc);
		$events[$eid] = $ge;
	}
	addEvents2Calendar($calendars, BBSchedule::GOOGLE_CALENDAR_ID, $events);
	
	//add more event groups here
	
	foreach($calendars as $calendarId=>$events){
		$log->logInfo("Updating calendar $calendarId");
		$c = $calendarService->events->listEvents($calendarId, $params);
		$items = $c->getItems(); //existing items
		$log->logInfo("Found ".count($items)." google events for $calendarId");
		
		foreach($items as $item){
			$eid = getSharedData($item, 'eid');
			if($eid){
				if(empty($events[$eid]) || $clear){ //this is not a valid event
					try{
						$calendarService->events->delete($calendarId, $item->id);
					} catch (Exception $e){
						$log->logException($e->getMessage());
						continue;
					}
					
				} else { //the event is already present in google cal so we can remove from the calendar this side
					 unset($events[$eid]);
				}
			}
		}

		//now we wrie to the calendar
		foreach($events as $eid=>$event){
			try{
				$log->logInfo("Adding event ".$event->summary." to calendar $calendarId");
				$ce = $calendarService->events->insert($calendarId, $event);
			} catch (Excepion $e){
				$log->logException($e->getMessage());
				continue;
			}
		}
	}
} catch (Exception $e){
	if($log)$log->logException($e->getMessage());
	mail('bill@bulan-baru.com', 'BB update google calendars exception', $e->getMessage());
}	
$log->finish();
?>

