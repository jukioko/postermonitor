<?php
/*
Jeroen van Oorschot 2015
e.t.s.v. Thor Eindhoven posterviewer
Gets list of sponsors from thor.edu and puts it to the client in json
*/

$html = file_get_contents('http://static.thor.edu/company-information');
$needle = '<h2><a href="/company/';
$lastPos = 0;
$sponsors;

while (($lastPos = strpos($html, $needle, $lastPos))!== false) { //for each sponsor
	$lastPos = $lastPos + strlen($needle); //update the last place where a sponsor was found
	$sponsorsub = substr($html,$lastPos,1000);  // this is the subset of HTML that contains the sponsor image
	$sponsor = explode('"', $sponsorsub);  //Split that html on "

	$name = $sponsor[0];  	//name of the sponsor, not used atm
	$link = $sponsor[6];	//location of the image

	$sponsors[] = $link;	   //put in the array

}

print(json_encode($sponsors));  //show the array as json
?>