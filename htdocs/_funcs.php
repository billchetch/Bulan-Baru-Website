<?php
function getCMSForm($form, $obj = null, $settings = null){
	if(!_STAGING_)return '';
	switch($form){
		case 'images':
			return CMS::getFunctionality("image", "peimage2", "pageelements", $obj['id'], $settings);
			
		case 'copy':
			return CMS::getFunctionality('copy', 'pehtml', 'pageelements', $obj['id'], $settings);
			
		case 'banner':
			return getCMSForm('images', $obj, 'fileDirId=10');
			
		case 'boat-gallery':
			return CMS::getFunctionality('boat images', 'gallery', 'galleries', 2, 'fileDirId=9&imageWidth=90&imageHeight=60');
			
		case 'slides-gallery':
			return CMS::getFunctionality('slides', 'gallery', 'galleries', 1, 'fileDirId=8&imageWidth=120&imageHeight=42');
			
		case 'trip':
			return CMS::getFunctionality('trip', 'misc/bulan_baru/trips', 'trips', $obj['id'], $settings);
			
		case 'vid-promo':
			return CMS::getFunctionality("image", "peimagetitlecaption", "pageelements", $obj['id'], 'fileDirId=11');
	}
	return '';
}

function getResourceHTML($resource, $vals = null){
	switch($resource){
		case '{BB}':
			return '<span class="bb-text">BULAN BARU</span>';
		case '{BBSC}':
			return '<span class="bb-text">BULAN BARU SURF CHARTERS</span>';	
		case '{TEL}':
			return '<span class="tel-text">+62 (0)81 239 380 893</span>';
			
		case 'submit-button':
			$html = '<button type="submit" class="submit-button">'.$vals.'</button>';
			return $html;
			
		case '{EMAIL}':
			$html = '<a href="mailto:'._EMAIL_.'">'._EMAIL_.'</a>';
			return $html;
			
		case '{FB}':
		case '{FACEBOOK}':
			$html = '<a href="'._FB_.'" target="blank"><img src="/images/fb.png" align="absmiddle"/> FACEBOOK</a>';
			return $html;
			
		case '{IG}':
		case '{INSTAGRAM}':
			$html = '<a href="'._IG_.'" target="blank"><img src="/images/ig.png" align="absmiddle"/> INSTAGRAM</a>';
			return $html;	
			
		case '{BP}':
			return '<a href="http://bulan-purnama.com/" target="blank">Bulan Purnama</a>';
			
		case '{CONTACT}':
			return getResourceHTML('{link:contact-us?new=1:contact us}');
			
		case '{TERMS}':
			return getResourceHTML('{link:terms-and-conditions:terms}');
		
		case '{PROMO_IMAGE_OV}': //opera villa
			$html = '<a href="http://www.operavilla.com" class="image-link"><img src="/images/promo-image-1.png" class="trip-thumb"/></a>';
			return $html;
			
		case '{PROMO_IMAGE_PR}': //pulau retreats
			$html = '<a href="http://www.pulauretreats.com" class="image-link"><img src="/images/promo-image-pr.jpg" class="trip-thumb"/></a>';
			return $html;
			
		case '{PROMO_IMAGE_MS}': //massey surf
			$html = '<a href="http://www.masseysurf.com.au" class="image-link"><img src="/images/promo-image-ms.png" class="trip-thumb"/></a>';
			return $html;
	}
	
	if(stripos($resource, '{link:') !== false){
		$tag = trim($resource);
		$html = '';
		$ar = explode(':', substr($tag, 1, strlen($tag) - 2));
		if(stripos($ar[1], 'www.') !== false){
			$href = $ar[1];
			$txt = isset($ar[2]) ? $ar[2] : $ar[1];
			$html = '<a href="http://'.$href.'" target="_blank">'.$txt.'</a>';
		} else {
	    	$href = Website::getPageURL($ar[1]);
	    	$txt = isset($ar[2]) ? $ar[2] : $ar[1];
	    	$html = '<a href="'.$href.'">'.$txt.'</a>';
		}
    	return $html;
	}
	
}

function getElementCopy($page, $elt){
	$copy = isset($elt['copy']) ? $elt['copy'] : null;
    if($copy){
    	$copy = $page->cleanCopy($copy, 'simple', '<h1><h2><h3><h4><span><img>');
    	$resources = explode(',', '{BB},{BBSC},{EMAIL},{FB},{TEL},{CONTACT},{TERMS},{BP},{PROMO_IMAGE_1},{PROMO_IMAGE_OV},{PROMO_IMAGE_PR},{PROMO_IMAGE_MS}');
    	foreach($resources as $resource){
    		$copy = str_replace($resource, getResourceHTML($resource), $copy);
    	}
    	$n = 0;
    	while($n = stripos($copy, '{link:', $n)){
    		$m = stripos($copy, '}', $n + 1);
    		if($m !== false){
    			$tag = substr($copy, $n, $m - $n + 1);
    			$html = getResourceHTML($tag);
    			$copy = str_replace($tag, $html, $copy);
    		} else {
    			throw new Exception("Cannot find closing brace");
    		}
    	}
    }
    return $copy;
}

function getBreadcrumbHTML($page, $qs = null){
	if($page->get('sid') == 'home')return '';
	$delimiter = ' :: ';
	
	$html = $delimiter;
	$path = $page->getPath();
	$sid = '';
	for($i = 0; $i < count($path); $i++){
		$sid.= ($sid ? '/' : '').$path[$i];
		$url = Website::getPageURL($sid, $qs);
		$header = Website::getPageHeader($sid, $page->getDB());
		if($header && !empty($header['title'])){
			$title = $header['title'];
		} else {
			$title = $path[$i];
			$title = str_replace('-', ' ', $title);
		}
		$html.= ($i > 0 ? $delimiter : '')."<a href=\"$url\">$title</a>";
	}
	return $html;
}

function getImages($page, $imageIDs, $sizePrefix = null){
	$db = $page->getDB();
	$srcpath = $sizePrefix ? "/copies/$sizePrefix" : '/';
	$sql = "*, alt_text AS caption, CONCAT('/uploads/', dirpath,'$srcpath',filename) AS src";
	if(is_array($imageIDs))$imageIDs = count($imageIDs) ? implode(',', $imageIDs) : '0';
	$sql.= " FROM ast_images WHERE id IN ($imageIDs)";
	$images = array();
	$db->select($sql, $images);
	
	$prefixes = array('xl', 'lg', 'md', 'sm', 'xs');
	foreach($images as &$img){
		foreach($prefixes as $pfx){
			$img[$pfx.'_src'] = '/uploads/'.$img['dirpath']."/copies/$pfx".'_'.$img['filename'];
		}
	}
	
	return $images;
}

function getImage($page, $imageID, $sizePrefix = null){
	$images = getImages($page, $imageID, $sizePrefix);
	return count($images) ? $images[0] : null;
}

function getGalleryImages($page, $gallery){
	$gmap = array('slides'=>1, 'boat'=>2);
	$gid = isset($gmap[$gallery]) ? $gmap[$gallery] : null;
	if(!$gid)throw new Exception("No such gallery as $gallery");
	$db = $page->getDB();
	$sql = "* FROM ast_gallery_images WHERE gallery_id=$gid AND online=1 ORDER BY position";
	$rows = array();
	$db->select($sql, $rows);
	$images = array();
	foreach($rows as $r){
		$img = getImage($page, $r['image_id']);
		$img['caption'] = $r['caption'];
		array_push($images, $img);
	} 
	return $images;
}

function getTrips($page, $promote = false){
	$db = $page->getDB();
	
	$trips = array();
	$sql = "* FROM trp_trips t WHERE active=1 ";
	if($promote){
		$sql.= "ORDER BY promote DESC, position, id";	
	} else {
		$sql.= "ORDER BY position, id";
	}
	$db->select($sql, $trips);

	foreach($trips as &$trip){
		$tid = $trip['id'];
		$trip['url'] = 'trips/'.$trip['sid'];
		$trip['href'] = Website::getPageURL($trip['url']);
		$img = getImage($page, $trip['image_id']);
		$trip['thumb'] = $img;
		$rows = array();
		$db->select("* FROM trp_trip_images WHERE trip_id=$tid ORDER BY position", $rows);
		$images = array();
		foreach($rows as $row){
			$img = getImage($page, $row['image_id'], 'sm_');
			//$img['sm_src'] = 
			array_push($images, $img);
		}
		$trip['small_images'] = $images;
		
		$trip['map_image'] = null;
		if($trip['map_id']){
			$img = getImage($page, $trip['map_id']);
			$trip['map_image'] = $img;
		}
		
		$rows = array();
		$db->select("* FROM trp_trip_locations t INNER JOIN trp_locations l ON t.location_id=l.id WHERE trip_id=$tid AND l.active=1", $rows);
		$locations = array();
		foreach($rows as $row){
			$row['image'] = null;
			if($row['image_id']){
				$img = getImage($page, $row['image_id']);
				$row['image'] = $img;
			}
			array_push($locations, $row);	
		}
		$trip['locations'] = $locations;		
	}
	return $trips;
}	

function getTrip($page, $tid){
	$searchOver = explode(',', is_numeric($tid) ? 'id' : 'sid,url');
	$trips = getTrips($page);
	foreach($trips as $trip){
		foreach($searchOver as $f){
			if(!empty($trip[$f]) && $trip[$f] == $tid)return $trip;
		}
	}	
	return null;
}

function getLocations($page){
	$db = $page->getDB();
	
	
	$filter = 'active=1';
	
	$sql = "* FROM trp_locations ";
	if($filter)$sql.= "WHERE $filter ";
	$sql.= " ORDER BY location_title";
	$locations = array();
	$db->select($sql, $locations);
	
	return $locations;
}

function getSchedule($page, $tid = null, $season = null){
	$db = $page->getDB();
	
	$filter = 's.active=1 AND t.active=1 AND datediff(depart_date,now())>0';
	if($season)$filter.= " AND season=$season";
	if($tid)$filter.= " AND trip_id=$tid";
	$sql = "s.*, t.title AS trip, dh.location_title AS depart_from, ah.location_title AS arrive_at ";
	$sql.= "FROM trp_schedule s INNER JOIN trp_trips t ON s.trip_id=t.id INNER JOIN trp_locations dh ON s.depart_harbour_id=dh.id INNER JOIN trp_locations ah ON s.arrive_harbour_id=ah.id ";
	$sql.= "WHERE $filter ORDER BY season, depart_date";
	$schedule = array();
	$db->select($sql, $schedule);
	
	foreach($schedule as &$si){
		$si['href'] = Website::getPageURL('bookings', 'new=1&schedule='.$si['id']);
		$availability = $si['spaces_available'];
		if($availability == 1){
			$availability = "1 place left";
		} elseif($availability > 9){
			$availability = "Yes";
		} elseif(!$availability || $availability < 1) {
			$availability = 'FULL';
		} else {
			$availability = "$availability left";
		}
		$si['availablility'] = $availability;
		$si['fprice'] = number_format($si['price'], 0). ' AUD';
		$dtime = strtotime($si['depart_date']) + 1000;
		$atime = strtotime($si['arrive_date']) + 1000;
		$si['depart_fdate'] = date('j F', $dtime);
		$si['arrive_fdate'] = date('j F', $atime);
		$si['depart'] = $si['depart_from'].'<br/><span class="schedule-date">'.$si['depart_fdate'].'</span>';
		$si['arrive'] = $si['arrive_at'].'<br/><span class="schedule-date">'.$si['arrive_fdate'].'</span>';
		$si['comments'] = !empty($si['comments']) ? trim($si['comments']) : null;
		if(date('Y', $dtime) != date('Y', $atime)){
			$si['trip_and_dates'] = $si['trip']." (".date('j F, Y', $dtime)." to ".date('j F, Y', $atime).")";
		} else {
			$si['trip_and_dates'] = $si['trip']." (".$si['depart_fdate']." to ".$si['arrive_fdate'].", ".date('Y', $dtime).")";
		}
		 
	}
	
	return $schedule; 
}

function getScheduleItem($page, $id){
	$schedule = getSchedule($page);
	foreach($schedule as $si){
		if($si['id'] == $id)return $si;
	}
	return null;
}

function getEmailScore($body){
	$score = 0;
	$badStrings = array('http:'=>1, 'https:'=> 1, 'www.'=>1, 'website'=>0.5, 'traffic'=>0.5, 'ranking'=>0.5, 'design'=>0.5, 'search engine'=>1);
	foreach($badStrings as $bs=>$sc){
		$matches = array();
		preg_match_all('/'.addslashes($bs).'/', $body, $matches);
		if(count($matches) && count($matches[0])){
			$count = count($matches[0]);
			$score +=$sc*$count;
		}
	}
	return $score;
}

function sendEmail($email){
	//do some validating here
	$body = $email['body'];
	$score = getEmailScore($body);
	if($score >= 1)return false;
	
	
	
	$phplib = _SITESROOT_.'webapps/lib/php/';
	require($phplib.'phpmailer/class.phpmailer.php');
	$mail = new PHPMailer();
	$mail->SetLanguage('en', $phplib.'phpmailer/language/');
	$mail->IsHTML(false);
						
	$mail->Body = $email['body'];
	$mail->Subject = $email['subject'];
	$mail->From = $email['from'];
	$mail->FromName = $email['name'];
	
	$mail->AddAddress(_EMAIL_);
	return $mail->Send();
}

function getEmailKey($eseed){
	if(empty($eseed))throw new Exception("No seed supplied");
	return crypt($eseed, 'BBEMAIL');
}

function isValidEmailKey($eseed, $ekey){
	if(empty($eseed))throw new Exception("No seed supplied");
	if(empty($ekey))throw new Exception("No key supplied");
	
	return getEmailKey($eseed) == $ekey;
}

function createEmailBodyFromTemplate($db, $domain, $emailName, $template, $data = null){
	if(!file_exists($template))throw new Exception("Cannot locate file $template for email $emailName");
	$emailBody = file_get_contents($template);
	
	$eseed = time();
	$ekey = getEmailKey($eseed);
	
	$replacements = array();
	$replacements['EMAIL_URL'] = 'http://'.$domain.'/viewemail?db=staging&eseed='.$eseed.'&ekey='.$ekey;
	$replacements['IMAGE_SRC_ROOT'] = 'http://'.$domain.'/images';
	$replacements['TOP_LOGO'] = 'http://'.$domain.'/images/email/logo-black.png';
	$replacements['BANNER_TOP_IMAGE'] = $replacements['IMAGE_SRC_ROOT'].'/email/banner-top-'.rand(1,5).'.jpg';
	$replacements['BANNER_BOTTOM_IMAGE'] = $replacements['IMAGE_SRC_ROOT'].'/email/banner-bottom-'.rand(1,2).'.jpg';
	$replacements['PHOTOG_NAME'] = '@dobbydigital';
	$replacements['PHOTOG_URL'] = 'http://www.instagram.com/dobbydigital';
	$replacements['PHOTOG_PPP'] = '300 AUD';
	$replacements['BEER_FREE_CRATES'] = '10';
	$replacements['BEER_CRATE_COST'] = '50 AUD';
	$replacements['EMAIL_REPLYTO_ADDRESS'] = 'info@bulan-baru.com';
	$replacements['CHECKLIST_URL'] = 'http://'.$domain.'/checklist';
	$replacements['FAQ_URL'] = 'http://'.$domain.'/faq';
	$replacements['TERMS_URL'] = 'http://'.$domain.'/terms-and-conditions';
	switch($emailName){
		case 'emailb4trip':
			$scheduleid = isset($data['id']) ? $data['id'] : null;
			if(isset($scheduleid)){
				$replacements['EMAIL_URL'].='&scheduleid='.$scheduleid;
				$filter = "trp_schedule.id=$scheduleid";
				$schedule = $db->selectRow("* FROM trp_schedule WHERE $filter");
				$tripid = $schedule['trip_id'];
				$trip = $db->selectRow("* FROM trp_trips WHERE id=$tripid");
				$departHarbour = $db->selectRow("* FROM trp_locations WHERE id=".$schedule['depart_harbour_id']);
				$arriveHarbour = $db->selectRow("* FROM trp_locations WHERE id=".$schedule['arrive_harbour_id']);
				$departAirport = $db->selectRow("* FROM trp_locations WHERE id=".$schedule['depart_airport_id']);
				$arriveAirport = $db->selectRow("* FROM trp_locations WHERE id=".$schedule['arrive_airport_id']);
				
				
				$replacements['DEPART_HARBOUR'] = $departHarbour['location_title'];
				$replacements['DEPART_AIRPORT'] = $departAirport['location_title'];
				$replacements['ARRIVE_HARBOUR'] = $arriveHarbour['location_title'];
				$replacements['ARRIVE_AIRPORT'] = $arriveAirport['location_title'];
				$replacements['DEPART_DATE'] = date('l jS F', strtotime($schedule['depart_date']));
				$replacements['ARRIVE_DATE'] = date('l jS F', strtotime($schedule['arrive_date']));
			}
			break;
	}
	
	
	//get some images from the galleries to use
	$gnames = array(1=>'SLIDE', 2=>'BOAT');
	$gcounts = array(1=>0, 2=>0);
	$prefixes = array('xl', 'lg', 'md', 'sm', 'xs');
	$sql = "* FROM ast_gallery_images WHERE online=1 ORDER BY gallery_id, rand()";
	$rows = array();
	$db->select($sql, $rows);
	foreach($rows as $r){
		$iid = $r['image_id'];
		$img = $db->selectRow("* FROM ast_images WHERE id=$iid");
		if($img){
			$gid = $r['gallery_id'];
			$gcounts[$gid]++;
			foreach($prefixes as $pfx){
				$idx = strtoupper($gnames[$gid]).'_IMAGE_'.strtoupper($pfx).'_'.$gcounts[$gid];
				$src = 'http://'.$domain.'/uploads/'.$img['dirpath']."/copies/$pfx".'_'.$img['filename'];
				$replacements[$idx] = $src;
			}
		}
	}
	
	
	switch($emailName){
		case 'emailb4trip':
			foreach($replacements as $r=>$v){
				$emailBody = str_ireplace('{'.$r.'}', $v, $emailBody);
			}
			break;
	}
	return $emailBody;
}
?>