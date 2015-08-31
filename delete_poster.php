<?
/*
Jeroen van Oorschot 2015
e.t.s.v. Thor Eindhoven posterviewer
*/
include_once("../pswd_poster.inc");
$poster = 'upload/' . $_POST["poster"];
	if($_POST['password'] == $pswd_poster){
	if(substr_count($poster,',')==2 and substr_count($poster,'.')==1){
		if(file_exists($poster)){
			if(unlink($poster)){
				echo "Poster deleted";
			}else{
				echo "Error in deleting poster";
			}
		}else{
			echo "File does not exist";
		}
	}else{
		echo "Invalid command supplied: " . $_POST["poster"];
	}
}else{
	echo "Invalid password";
}

	
?>