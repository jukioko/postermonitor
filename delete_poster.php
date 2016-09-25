<?
/*
Jeroen van Oorschot 2015
e.t.s.v. Thor Eindhoven posterviewer
*/
require_once("../pswd_poster.inc");
require_once("functions.php");
$postername = $_POST["poster"];
//$poster = 'upload/' . $postername;
if($_POST['password'] == $pswd_poster){
	$result = deletePoster($postername);
	print($result);
}else{
	echo "Invalid password";
}
	
?>