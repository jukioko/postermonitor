<?php
/*
Jeroen van Oorschot 2015
e.t.s.v. Thor Eindhoven posterviewer
Gets list of activities from an exchange calendar and puts it to the client.
*/

//This file contains the username and password used to login the Thor-sharepoint calendar
require_once '../pswd.inc';
$useCache = false;
//Whether to use the presaved cachefile
//$useCache = true;
$cacheFile = 'activities_cache.json';
$lastModFile = 'lastmod.dt';
if(file_exists($lastModFile)){
	$lastMod = file_get_contents($lastModFile);
	if((time() - $lastMod) > (60*5)){
		$useCache = false;
	}else{
		$useCache = true;
	}
}
//print $useCache;


//A small GET check just for fun.
if($_GET['Thor']=='gaaf'){
	//update cache file
	if($useCache!=true || !file_exists($cacheFile)){
		//Open the html calendar page using php-CURL
		$url = "https://tuenl.sharepoint.com/sites/ee_root/studentenverenigingen/thor/Lists/Calendar/calendar.aspx";
		$user_agent = "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5";
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_HEADER => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_NOBODY => true));
		$cookie_file_path = dirname(__FILE__) . '/cookies.txt';
		$ch = curl_init();
		//==============================================================
		curl_setopt($ch, CURLOPT_USERPWD, $username. ':' . $password);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 100);
		//=============================================================
		$ret = curl_exec($ch);
		curl_close($curl);


		//parse the incoming file as HTML, don't log the errors.
		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($ret);
		libxml_clear_errors();
		//each calendaritem has its own table row (tr)
		$trs = $dom->getElementsByTagName('tr');

		//in the sharepoint webinterface can be set how many events are shown on one page,
		// this is also the amount of events you get here, and so the amount of events this script returns

		//loop through all table entries to find all events
		foreach($trs as $tr){
			//All tr with class 'itmhover' are actual events, the others are just for layout.
			if(stripos($tr->getAttribute('class'),'itmhover') !== false){
				$tds = $tr->getElementsByTagName('td');
				//4th TD contains the title
				$title = $tds->item(4)->nodeValue;
				$title = trim(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $title));

				//skip all options and intern activities. If title starts with [option] [optie] [internal] [intern] or so
				if(substr(strtolower($title),0,4) === "[opt" || substr(strtolower($title),0,4) === "[int"){
					continue;
				}
				//5th TD has the location, 6th the starttime, 7th the endtime and 8 whether the event is all-day
				$location = strlen($tds->item(5)->nodeValue)>1?str_replace('\r','',(strip_tags($tds->item(5)->nodeValue))):false;
				$start = $tds->item(6)->nodeValue?strtotime($tds->item(6)->nodeValue):false;
				$end = $tds->item(7)->nodeValue?strtotime($tds->item(7)->nodeValue):false;
				$allday = strlen($tds->item(8)->nodeValue)>1?true:false;

				if($end < time()){
					//event already finished
					continue;
				}

				//give classes according to association
				if(stripos($title,'ieee') !== false){
					$ass = 'ieee';
				}elseif(stripos($title,'waldur') !== false){
					$ass = 'waldur';
				}elseif(stripos($title,'odin') !== false){
					$ass = 'odin';
				}elseif(stripos($title,'[eir') !== false){
					$ass = 'eir';}
				elseif(stripos($title,'[lustrum') !== false){
					$ass = 'lustrum';
				}elseif((stripos($title,'thor') || stripos($title,'acci') || stripos($title,'ivaldi') || stripos($title,'kvasir')) !== false){
					$ass = 'thor';
				}else{
					$ass = 'gen';
				}

				//turn this objects data to an array
				$object = array("tit" => $title, "ass" => $ass, "loc" => $location, "sta" => $start, "end" => $end, "ald" => $allday);
			}else{
				//skip tds not beloning to the table
				continue;
			}
			//concat all event-objects into the output-object
			$output[] = $object;
		}
		$json = json_encode($output);

		$handle1 = fopen($cacheFile, 'w') or die("can't open file");
		$handle2 = fopen($lastModFile, 'w') or die("can't open file");
		if($handle1 && $handle2){
			fwrite($handle1, $json);
			fwrite($handle2, time());
			fclose($handle1);
			fclose($handle2);
		}else{
			print('Cache file writing failed');
		}
	//use the cachefile
	}else{
		if(file_exists($cacheFile)){
			$json = file_get_contents($cacheFile);
		}else{
			print('Cache file reading failed');
		}
	}

	//finally print the event data as json
	print($json);

}else{
	print("You are not allowed to view this page");
}
?>
