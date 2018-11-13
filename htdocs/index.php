<?php
require_once('_settings.php');
require_once('_funcs.php');

$sid = null;
$ss = null;
$url = null;
$alert = null;
try{
	//default landing page
	$sid = isset($_GET['page']) && $_GET['page'] ? $_GET['page'] : 'home';
	if(strpos($sid, 'index') === 0)$sid = 'home';
	
	//check if a sitemap is being requested
	if(stripos($sid, 'sitemap') === 0){
		header('Content-Type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		require('Sitemap.xml');
		die;
	}
	
	//check if email is being requested
	if(stripos($sid, 'viewemail') === 0){
		$ekey = isset($_GET['ekey']) ? $_GET['ekey'] : null;
		$eseed = isset($_GET['eseed']) ? $_GET['eseed'] : null;
		try{
			if(!isValidEmailKey($eseed, $ekey)){
				echo "No valid key";
				die;
			}
			
			require('components/viewemail.php');
			die;
		} catch (Exception $e){
			echo $e->getMessage();
		}
	}
	
	//get structure (not used as yet)
    $ss = Website::getStructure($_db, $sid);
    $url = $ss->getURL();
    
    //get page
    try{
	    //$page = Website::getPageByURL($_db, $ss);
	    $page = Website::getPage($_db, $sid);
	    if(!$page)throw new PageNotFoundException("Cannot find page ".$url['url']);
	} catch (PageNotFoundException $e) {
		if($sid == 'home'){
			require('holding.html');
			die;
		} else {
			if(file_exists('404.html')){
				require('404.html');
			} else {
				echo "Page $sid not found";
			}
			die;
		}
	}
	
	//modify page title, description etc.
	$pageTitle = $page->get('title');
	if(!$pageTitle)$pageTitle = ucwords(str_replace('-', ' ', $page->getPath($page->getPathLength() - 1)));
	$pageDesc = $page->get('description');
	if(!$pageDesc)$pageDesc = "BULAN BARU is a surf charter company specialising in finding uncrowded waves in the Indonesian islands of Sumbawa, Sumba, Rote and Maluku regions.";
	
	//do actions
	if(isset($_GET['action'])){
		try{
			switch($_GET['action']){
				case 'booking':
					$fm = HTMLForm::restore('bookings');
					if(empty($fm))throw new Exception("Cannot restore booking form");
					$req = $_POST;
					$valid = $fm->validate($req);
					if($valid){
						$name = $fm->getValue('name');
						$email = $fm->getValue('email');
						$comments = $fm->getValue('comments');
						$scheduleid = $fm->getValue('schedule');
						
						$fm->resetFields(true);
						$fm->setField('name', $name);
						$fm->setField('email', $email);

						$si = getScheduleItem($page, $scheduleid);
						$subject = "Booking enquiry for ".$si['trip_and_dates'];
						$nameAndEmail = $name.' <'.$email.'>';
						$msg = $nameAndEmail." has made an enquiry about ".$si['trip_and_dates'];
						if($comments)$msg.= "\n\n".$comments;
						$vals = array('from'=>$email, 'name'=>$name, 'subject'=>$subject, 'body'=>$msg);
						if(!sendEmail($vals)){
							$fm->error = "There has been a problem sending your info.  Please try again";
						}
					}
					$fm->save();
					break;
				
				case 'contact':
					$fm = HTMLForm::restore('contact');
					if(empty($fm))throw new Exception("Cannot restore contact form");
					$req = $_POST;
					$valid = $fm->validate($req);
					if($valid){
						$name = $fm->getValue('name');
						$email = $fm->getValue('email');
						$comments = $fm->getValue('comments');
						$fm->resetFields(true);
						$fm->setField('name', $name);
						$fm->setField('email', $email);
						
						$subject = "Contact made from $email";
						$nameAndEmail = $name.' <'.$email.'>';
						$msg = $nameAndEmail." has sent the following message: \n\n".$comments;
						$vals = array('from'=>$email, 'name'=>$name, 'subject'=>$subject, 'body'=>$msg);
						if(!sendEmail($vals)){
							$fm->error = "There has been a problem sending your info.  Please try again";
						}
					}
					$fm->save();
					break;
					
				case 'test':
					$his = new SysHistory($_db); 
					
					//$apiClient = GoogleAPIClient::create('BULANBARU');
	
					//add services here (before authentication)
					
					//authenticate: 'false' means do not try and obtain new token if one doesn't exist in sys history
					//GoogleAPIClient::authenticate($apiClient, $his, true);
					
					die;
					break;
					
				case 'oauth2':
					$client = GoogleAPIClient::create('BULANBARU');
					GoogleAPIClient::authenticate($client, new SysHistory($_db)); //this will store the resulting token
					
					if($client->getAccessToken()){
						echo "Thank you, access has been successfully granted";
					}
					die;
					break;
				
			}
		} catch (Exception $e){
			$alert = $e->getMessage();
		}
	}
} catch (Exception $e) {
	//we process general action exception here
	$alert = $e->getMessage();
}

$js = array();
$css = array();
array_push($js, _JQUERY_);
array_push($css, '/css/common.css');
$useSlides = in_array($sid, array('home'));
$useFancyBox = in_array($sid, array('boat', 'home')) || ($page->getPath(0) == 'trips' && $page->getPathLength() > 1);
$hasForms = in_array($sid, array('bookings','contact-us'));  
//add slides
if($useSlides)array_push($js, "/lib/js/jquery/jquery.slides.min.js");
if($useFancyBox){
	array_push($js, "/lib/js/jquery/fancybox/jquery.fancybox.js?v=2.1.5");
	array_push($css, "/lib/js/jquery/fancybox/jquery.fancybox.css?v=2.1.5");
}
if($hasForms){
	array_push($css, "/css/forms.css");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html>
<head>
<!--  copyright chetch <?php echo date('Y-m-d H:i:s'); ?> -->
<title>BULAN BARU | <?php echo htmlentities($pageTitle); ?></title>
<meta name="description" content="<?php echo $pageDesc; ?>" />
<meta name="keywords" content="Surf charter, surf charters, surfcharters, Sumbawa, Sumba, Maluku, Sumatra, Enggano, Indonesia, Boat Charter, we make what we do" />
<meta name="robots" content="index,follow" />
<meta name="google-site-verification" content="ulZG3OeKng6r9rTh7HE6tCr6iOEuRhKN4rVJdN0Yyd4" />

<link rel="icon" type="image/x-icon" href="/images/favicon.png" />
<?php foreach($js as $j){
	if(_STAGING_)$j.= (strpos($j, '?') === false ? '?' : '&').'v='.time(); 
	?>
<script type="text/javascript" src="<?php echo $j; ?>"></script>
<?php } ?>

<script type="text/javascript">
var _settings = new Array();
_settings['staging'] = <?php echo _STAGING_ ? 'true' : 'false'; ?>;
_settings['debug'] = <?php echo _DEBUG_ ? 'true' : 'false'; ?>;
_settings['host'] = "<?php echo $_SERVER['HTTP_HOST']; ?>";
_settings['page.id'] = <?php echo $page->get('id'); ?>;
_settings['url.id'] = <?php echo $url['id']; ?>;
_settings['url.idpath'] = [<?php echo $url['id_path'].($url['id_path'] ? ',' : '').$url['id'] ?>];
_settings['url.this'] = "<?php echo Website::getPageURL($url['url']); ?>";
_settings['alert'] = "<?php echo $alert; ?>";

var slideshows = {};
var fancyboxes = {};

$(document).ready(function() {
	if(_settings.staging && top.Event_broadcast){
        top.Event_broadcast('PAGE_LOADED', {hWin: window, pageId: _settings['page.id']});
    }

	for(var p in slideshows){
		var slideshow = slideshows[p];
		$('#' + p).slidesjs(slideshow);
	}
	
	//boat
	<?php if($useFancyBox){ ?>$('.fancybox').fancybox(); <?php } ?>

	//alerting
	<?php if($alert){?>alert("<?php echo $alert; ?>");<?php } ?>
});
</script>

<!-- css -->
<?php foreach($css as $c){
	if(_STAGING_)$c.= (strpos($c, '?') === false ? '?' : '&').'v='.time(); 
	?>
<link rel="stylesheet" href="<?php echo $c?>" type="text/css" />
<?php } ?>

<style>
<?php if(_STAGING_){ ?>
.cms-ui{
	font-size: 11px;
	color: #666666;
}
<?php } ?>
html{
	height: 100%;
}
body{
	background-color: #ffffff;
	/*background-image: url(/images/bg_tile_07.jpg);*/
	/*background-image: url(/images/bg_tile_09.png);*/
	background-image: url(/images/bg_tile_12.png);
	/*background-image: url(images/bg_tile_wave.png);*/
	/*background-position: center center;
	background-repeat: no-repeat;*/
	
	font: normal 16px/1.5 "Helvetica Neue", Helvetica, Arial, sans-serif;
  	color: #232525;
	height: 100%;
	margin: 0;
	padding: 0;
	color: #CCCCCC;
}
a{
	text-decoration: none;
	font-weight: bold;
}

h2{
	margin: 0px;
	margin-bottom: 8px;  
	padding: 0px;
}
h4{
	margin: 0px;
	margin-bottom: 4px;  
	padding: 0px;
}

ul{
	margin: 0;
	padding: 0;
	list-style-type: none;
}

.bb-text{
	font-weight: bold;
	color: #ffffff;
}

p{
	padding-top: 0px;
	margin-top: 0px;
}

hr{
	border:none;
  	border-top:1px dotted #ffffff;
}
  
#nav{
	background: #000000; /* Old browsers */
	background: -moz-linear-gradient(top,  #7d7e7d 0%, #000000 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#7d7e7d), color-stop(100%,#0e0e0e)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  #7d7e7d 0%,#000000 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  #7d7e7d 0%,#000000 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  #7d7e7d 0%,#000000 100%); /* IE10+ */
	background: linear-gradient(to bottom,  #7d7e7d 0%,#000000 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#7d7e7d', endColorstr='#000000',GradientType=0 ); /* IE6-9 */
	background-color: #000000; 
	width: 100%; 
	position: fixed; 
	top: 0px; 
	z-index: 100;
	height: 57px;
	-webkit-box-shadow: 0px 4px 8px 0px rgba(50, 50, 50, 0.6);
	-moz-box-shadow:    0px 4px 8px 0px rgba(50, 50, 50, 0.6);
	box-shadow:         0px 4px 8px 0px rgba(50, 50, 50, 0.6);
}
#nav-inner{
	width: 1000px;
	margin: auto; 
	padding: 10px 0px 20px 0px;
}
#nav-left{
	float: left;
	 width: 350px; 
	 height: 28px;
}
#logo-tl{
	width: 347px;
	height: 28px;
}
#nav-right{
	float: right; 
	margin: 0px;
}
#nav-right ul{
	margin-top: 3px;
	padding: 0;
}
#nav-right ul li{
	display: inline;
	padding-left: 16px;
	padding-right: 4px;
	color: #eeeeee;
}

#nav a{
	color: #eeeeee;
}
#nav a:visited{
	color: #eeeeee;
}
#nav-right a{
	font-weight: normal;
	color: #eeeeee;
}

#nav-right a:hover{
	color: #ffffff;
}

.phase-icon{
	width: 12px;
	height: 12px;
	margin-right: 4px;
}

#breadcrumb{
	text-transform: uppercase;
}
#main{
	width: 1000px;
	margin: 0 auto;
	padding-bottom: 0px;
	margin-bottom: 0px;
	margin-top: 59px;
	border: none;
	-webkit-box-shadow: 4px 4px 17px 0px rgba(50, 50, 50, 0.75);
	-moz-box-shadow:    4px 4px 17px 0px rgba(50, 50, 50, 0.75);
	box-shadow:         4px 4px 17px 0px rgba(50, 50, 50, 0.75);
	
	
}
#slides {
	width: 1000px;
	height: 350px;
	overflow: hidden;
	padding: 0px;
	display: none;
	position: relative;
}

#content{
	background-color: #000000;
	margin-top: 1px;
	padding-top: 26px;
	padding-bottom: 30px;
	min-height: 400px;
	
	background-image: url(/images/floral-wave3.png); 
	background-repeat: no-repeat; 
	background-position: left bottom;
}

#content a{
	color: #ffffff;
}
#content a:hover{
	color: #cccccc;
}
.column1{
	float: left; 
}
.column2{
	float: right;
}

#content .no-columns{
	margin-left: 40px;
	margin-right: 40px;
}
#content .no-columns2{
	margin-left: 80px;
	margin-right: 100px;
}

#content .column1{
	width: 590px;
	padding-left: 30px;
	background: url(/images/toraja-sun.png); 
	background-repeat: no-repeat; 
	background-position: 50% 50px;
}

#content .column2{
	font-size: 14px;
	width: 282px;
	margin-right: 28px;
}

#content-links{
	margin-top: 30px;
}

#trippromoslides{
	display: none;
}

#trippromoslides .slidesjs-navigation {
	
}

#trippromoslides .slidesjs-previous {
	margin-right: 12px;
    float: left;
}

#trippromoslides .slidesjs-next {
	margin-right: 12px;
    float: left;
}

.trip-thumb, .vid-thumb{
	width: 280px;
	height: 120px;
	border: 1px solid #eeeeee;
}
.trip-side{
	width: 280;
	height: 210;
	border: 1px solid #eeeeee;
}

#feature{
	margin-top: 26px;
}
#feature h4{
	font-size: 14px;
	color: #eeeeee;
}

#footer{
	width: 1000px;
	margin: auto;
	margin-top: 1px; 
	padding: 0px;
	padding-top: 12px;
	background-color: #067C94;
	font-size: 12px;
	color: #333333;	
}


#footer a{
	color: #111111;
}
#footer a:hover{
	color: #cccccc;
}

#footer a:visited{
	color: #111111;
}

#footer .column1{
	float: left;
	padding-left: 30px;
	width: 220px;
}
#friends{
	margin-top: 22px;
	text-align: center;
	/*padding-left: 24px;*/
}
#friends li{
	display: inline;
}
#sumatrasurfresort{
	width: 64px;
	height: 64px;
}
#site-copyright{
	text-align: center;
	font-size: 11px;
	color: #000000;
}

</style>

</head>
<body>
	<!-- header -->
	<div id="nav">
		<div id="nav-inner">
			<div id="nav-left">
				<a href="<?php echo Website::getPageURL('home'); ?>"><img src="/images/bb-wb2.png" id="logo-tl" alt="Bulan Baru Surf Charters logo"/></a>
				<?php 
				$breadcrumb = getBreadcrumbHTML($page);
				$home = $sid == 'home' ? 'SURF CHARTERS IN REMOTE INDONESIA' : 'SURF CHARTERS';
				?>
				<span style="font-size: 11px; font-weight: bold; color: white"><a href="<?php echo Website::getPageURL('home'); ?>"><?php echo $home; ?></a></span><span id="breadcrumb" style="font-size: 11px; font-weight: bold; color: white"><?php echo $breadcrumb; ?></span>
			</div>
			<div id="nav-right">
				<ul>
					<li><img src="/images/phases/i-phase-full.png" class="phase-icon"/><a href="<?php echo Website::getPageURL('trips'); ?>">TRIPS</a></li>
					<li><img src="/images/phases/i-phase-half-right.png" class="phase-icon"/><a href="<?php echo Website::getPageURL('schedule'); ?>">SCHEDULE</a></li>
					<li><img src="/images/phases/i-phase-quarter-right.png" class="phase-icon"/><a href="<?php echo Website::getPageURL('bookings'); ?>">BOOKINGS</a></li>
					<li><img src="/images/phases/i-phase-new.png" width="14" class="phase-icon"/><a href="<?php echo Website::getPageURL('boat'); ?>">BOAT</a></li>
					<li><img src="/images/phases/i-phase-quarter-left.png" width="14" class="phase-icon"/><a href="<?php echo Website::getPageURL('team'); ?>">TEAM</a></li>
					<li><img src="/images/phases/i-phase-half-left.png" class="phase-icon"/><a href="<?php echo Website::getPageURL('contact-us'); ?>">CONTACT</a></li>
				</ul>
			</div>
			<div style="clear: both"></div> 
		</div>
	</div>
	
	<!-- main body -->
	<div id="main">
		<?php
		$component = $sid; 
		if($page->getPath(0) == 'trips') {
			$component = ($page->getPathLength() > 1) ? 'trip' : 'trips'; 
		} elseif(!in_array($sid, array('home','bookings','contact-us','schedule','boat','faq','checklist','team'))){
			$component = 'template1';
		} 
		$require = "components/$component.php";
		if(file_exists($require)){
			require($require);
		}
		?>
		<!--  footer -->
		<div id="footer">
			<div class="column1">
				<ul>
					<li><a href="<?php echo Website::getPageURL('terms-and-conditions'); ?>">:: TERMS & CONDITIONS</a></li>
					<li><a href="<?php echo Website::getPageURL('faq'); ?>">:: FAQ</a></li>
					<li><a href="<?php echo Website::getPageURL('checklist'); ?>">:: CHECKLIST</a></li>
				</ul>
			</div>
			<div class="column1">
				<ul>
					<li><a href="<?php echo Website::getPageURL('private-charter'); ?>">:: PRIVATE CHARTERS</a></li>
					<li><a href="<?php echo Website::getPageURL('bookings'); ?>">:: BOOKINGS</a></li>
				</ul>
			</div>
			<div class="column1">
				<ul>
					<li><a href="<?php echo Website::getPageURL('about-us'); ?>">:: ABOUT US</a></li>
					<li><a href="<?php echo Website::getPageURL('testimonials'); ?>">:: TESTIMONIALS</a></li>
				</ul>
			</div>
			<div class="column1">
				<ul>
					<li><a href="<?php echo _FB_; ?>" target="blank">:: <img src="/images/fb.png" align="absmiddle"/> FACEBOOK</a></li>
					<li><a href="<?php echo _IG_; ?>" target="blank">:: <img src="/images/ig.png" align="absmiddle"/> INSTAGRAM</a></li>
				</ul>
			</div>
			<div style="clear: both"></div>
			<ul id="friends">
				<!-- <li><img src="/images/clipper-city-logo.jpg" alt="Clipper City - urban salon"/></li>-->
				<!-- <li><a href="http://www.jpslupik.com/" target="_blank"><img src="/images/jpslupik-logo.png" alt="JP Slupik - Photographer"/></a></li>  -->
				<li><a href="http://www.sumatrasurfresort.com/" target="_blank"><img id="sumatrasurfresort" src="http://www.sumatrasurfresort.com/wp-content/themes/ssr/images/gorilla.png" alt="Sumatra Surf Resort"/></a></li>
				<li><a href="http://theaxiom.com.au/" target="_blank"><img src="/images/axiom-logo.png" alt="Josh Garner's Axiom Project"/></a></li>
				<!-- <li><a href="http://www.operavilla.com/" target="_blank"><img src="/images/operavilla.jpg" alt="Opera Villa" style="position: relative; left: 4px; top: -4px"/></a></li>  -->
				<li><a href="http://www.curvebodyboardshop.com/" target="_blank"><img src="/images/curvebodyboards.jpg" alt="Curve BodyboardsBali" style="position: relative; left: 4px; top: -4px"/></a></li>
				<li><a href="http://www.masseysurf.com.au/" target="_blank"><img src="/images/massey-logo.png" alt="Massey Surfboards" style="position: relative; left: 4px; top: -4px"/></a></li>
				<li><a href="http://www.pulauretreats.com/" target="_blank"><img src="/images/pulau-retreats-logo.png" alt="Pulau Retreats" style="position: relative; left: 4px; top: -4px"/></a></li>
			</ul>
		</div>
	</div>
	<div id="site-copyright">&copy; chetch 2014</div>
</body>

<?php
if(!_LOCAL_ && !_STAGING_)require('components/ga.php'); 
?>
</html>
