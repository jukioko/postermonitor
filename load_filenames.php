<?php
/*
Jeroen van Oorschot 2015
e.t.s.v. Thor Eindhoven posterviewer
Gets list of posters and puts it to the client
*/
require_once('functions.php');
if($_GET['Thor']=='gaaf'){
	if($_GET['type']=='posters'){
		$filenames = getPosterFilenames();
		print(json_encode($filenames));
	}elseif($_GET['type']=='sponsors'){
		$location = "./posters/sponsors/";
		$filenames = glob($location.'{*.jpg,*.png}', GLOB_BRACE|GLOB_NOSORT);
		print(json_encode($filenames));
	}else{
		print("Invalid request");
	}
}else{
	print("You are not allowed to view this page");
}
