<?php
require('_settings.php');

$log->setEcho(Logger::ECHO_ONLY);
$log->start("Forecasts script");

function getData($url, $opts = null){
	$ch = null;
	try{
		if(empty($url))throw new Exception("No URL passed");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
	    if($opts){ //add options here
	    	
	    }
		$response = curl_exec($ch);
		if (curl_error($ch))throw new Exception("cURL error: ".curl_error($ch));
		curl_close ($curlSession);
			
		return response;
	} catch (Exception $e){
		if($ch)curl_close($ch);
		throw $e;
	}
}

$tries = array();
array_push($tries, 'f=2017-09-14+10%3A00');
array_push($tries, '2017-09-14+10%3A00');
array_push($tries, 'c=as2031&l=-9.6333000&g=120.2500000&u=%2Fas%2Fcentral-indonesia%2Fnangamesi-bay&f=2017-09-14+10%3A00');

$k = "4d54794dcc467647981b37da50d51bca";
$log->logInfo("Looking for k: $k");
foreach($tries as $s){
	$t = urldecode($s);
	$log->logInfo("Trying s/t: $s/$t");
	
	$k1 = hash("md5", $s);
	$k2 = hash("md5", $t);
	$log->logInfo("k1: $k1");
	$log->logInfo("k2: $k2");
}
die;


try{
	$datasources = array();
	
	//Sumba > Ganjas
	$datasource = array();
	$datasource['name'] = "Sumba > Ganjas";
	$datasource['source'] = 'www.tides4fishing.com';
	$datasource['url'] = 'http://wcache.staticserver1.com/?c=as2030&l=-9.7667000&g=119.6167000&u=%2Fas%2Fcentral-indonesia%2Fsendikari-bay&f=2017-09-14+09%3A00&k=6685b6158d579ff7e60abc54fe4da1bf';
	$datasource['format'] = 'json';
	array_push($datasources, $datasource);
	
	$datasource = array();
	$datasource['name'] = "Sumba > Ganjas";
	$datasource['source'] = '/www.tidetablechart.com';
	$datasource['url'] = 'http://www.tidetablechart.com/tides/q/50535960313323';
	$datasource['format'] = 'json';
	array_push($datasources, $datasource);
	
	//Sumba > Waingapu
	$datasource = array();
	$datasource['name'] = "Sumba > Waingapu";
	$datasource['source'] = 'www.tides4fishing.com';
	$datasource['url'] = 'http://wcache.staticserver1.com/?c=as2031&l=-9.6333000&g=120.2500000&u=%2Fas%2Fcentral-indonesia%2Fnangamesi-bay&f=2017-09-14+10%3A00&k=4d54794dcc467647981b37da50d51bca';
	$datasource['format'] = 'json'; 
	array_push($datasources, $datasource);
	
	$datasource = array();
	$datasource['name'] = "Sumba > Waingapu";
	$datasource['source'] = 'www.tidetablechart.com';
	$datasource['url'] = 'http://www.tidetablechart.com/tides/q/50535961313441';
	$datasource['format'] = 'json'; 
	array_push($datasources, $datasource);
	
	//Sumbawa
	$datasource = array();
	$datasource['name'] = "Sumbawa > ";
	$datasource['source'] = 'www.tides4fishing.com';
	$datasource['url'] = '';
	$datasource['format'] = 'json'; 
	array_push($datasources, $datasource);
	
	$datasource = array();
	$datasource['name'] = "Sumbaawa";
	$datasource['source'] = 'www.tidetablechart.com';
	$datasource['url'] = '';
	$datasource['format'] = 'json';
	array_push($datasources, $datasource);
	
	//Lombok 
	
	//Kupang
	
	
	//get data
	$data = null;
	try{
		$source = $datasoruces[0];
		$log->logInfo("Getting data for ".$source['name']);
		$data = getData($source['url']);
	} catch (Exception $e){
		throw $e;
	}

	echo $data;

} catch (Exception $e){
	$log->logException($e->getMessage());
	if($ch)curl_close($ch);
}
$log->finish();
?>