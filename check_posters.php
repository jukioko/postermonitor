<?php
/*
Jeroen van Oorschot 2015
e.t.s.v. Thor Eindhoven posterviewer
Checks which posters are outdated and moves them to deleted
*/
require_once('functions.php');
//get filenames sorted soonest-latest
$filenames = getPosterFilenames();

$today =  date('Y-m-d');
//$today = "2015-11-25";

print('<br />');

//loop all files
foreach($filenames as $filename){
		print("<br />");
		$filename = split('/',$filename);
		$filename = $filename[1];
		print $filename;

//		$date = split('/',$filename);
		//print(:::');
	
//		$date = $date[2];
		//print('....'.$date.'....');
//		$date = split(',',$date);
		//print_r($date);
//		$date = $date[0];
//		print($date);
		$diff = strcmp($filename,$today);
		
		print("--diff:--".$diff."--");

		if($diff < 0){
			//date is smaller than today
			//delete poster
			print("to be deleted");
			deletePoster($filename);
		}
}
?>