<?php
/*
Jeroen van Oorschot 2015
e.t.s.v. Thor Eindhoven posterviewer
Checks which posters are outdated and moves them to deleted
*/
chdir('/var/www/poster/public');
require_once('functions.php');
//get filenames sorted soonest-latest
$filenames = getPosterFilenames();

$today =  date('Y-m-d');
print("<br /><br />day: ".$today);

//loop all files
foreach($filenames as $filename){
		print("<br />");
		$filename = split('/',$filename);
		$filename = $filename[1];
		print $filename;

		$diff = strcmp($filename,$today);
		if($diff < 0){
			//date is smaller than today
			//delete poster
			print(" <b>Deleted</b>");
			deletePoster($filename);
		}
}
?>