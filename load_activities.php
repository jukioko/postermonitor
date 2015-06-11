<?php
/*
Jeroen van Oorschot 2015
e.t.s.v. Thor Eindhoven posterviewer
Gets list of activities from an exchange calendar and puts it to the client.
*/

require_once 'pswd.inc';
//$g = file_get_contents('Agenda - Current Events.txt');
$useCache = false;
$cacheFile = 'activities_cache.json';

//print('file exists: '.(string)file_exists($cacheFile));

if($_GET['Thor']=='gaaf'){

if($useCache!=true || !file_exists($cacheFile)){ //update cache file
	$url = "https://sites.ee.tue.nl/studentenverenigingen/thor/Lists/Calendar/MyItems.aspx";
	//$url = "https://sites.ee.tue.nl/studentenverenigingen/thor/_layouts/listfeed.aspx?List=%7BD4085279-CAD0-4F46-B3BF-40DE6BFE98D6%7D&Source=https%3A%2F%2Fsites%2Eee%2Etue%2Enl%2Fstudentenverenigingen%2Fthor%2FLists%2FCalendar%2Fcalendar%2Easpx";
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
	//$header = explode("\n", curl_exec($curl));
	curl_close($curl);

	
	//parse the incoming file
	$dom = new DOMDocument();
	$dom->loadHTML($ret);
	//each calendaritem has its own table row (tr)
	$trs = $dom->getElementsByTagName('tr');
	
	foreach($trs as $tr){
		if(stripos($tr->getAttribute('class'),'itmhover') !== false){
			$tds = $tr->getElementsByTagName('td');
			//print_r($tds->item(4)->nodeValue);
			$title = $tds->item(4)->nodeValue;
			$title = trim(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $title));
			$location = strlen($tds->item(5)->nodeValue)>1?str_replace('\r','',(strip_tags($tds->item(5)->nodeValue))):false;
			$start = $tds->item(6)->nodeValue?strtotime($tds->item(6)->nodeValue):false;
			$end = $tds->item(7)->nodeValue?strtotime($tds->item(7)->nodeValue):false;
			$allday = strlen($tds->item(8)->nodeValue)>1?true:false;
			//skip all options and intern activities
			if(substr(strtolower($title),0,4) === "[opt" || substr(strtolower($title),0,4) === "[int"){
				continue;
			}
			//give classes according to association
			if(stripos($title,'ieee') !== false){
				$ass = 'ieee';	
			}elseif(stripos($title,'waldur') !== false){
				$ass = 'waldur';
			}else{
				$ass = 'thor';
			}		
			$object = array("tit" => $title, "ass" => $ass, "loc" => $location, "sta" => $start, "end" => $end, "ald" => $allday);
		}else{
			//skip tds not beloning to the table
			continue;
		}
		$output[] = $object;
		$json = json_encode($output);
		$handle = fopen($cacheFile, 'w') or die("can't open file");
		fwrite($handle, $json);
		fclose($handle);
	}
}else{
	if(file_exists($cacheFile)){
		$json = file_get_contents($cacheFile);	
	}
}
print_r($json);
}else{
	print("You are not allowed to view this page");
}
?>