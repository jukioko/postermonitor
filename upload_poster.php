<!DOCTYPE html>
<html lang="en">
<head>
<!--
Jeroen van Oorschot 2015
e.t.s.v. Thor Eindhoven posterviewer
Page to upload a poster, and code to process the poster for publishing.
-->
<title>PromoniThor Manage | Upload</title>
<style type="text/css">
html,body{
	font-family: Verdana, Geneva, sans-serif;
}
td{
	padding:10px;
}
</style>
</head>

<body>
<h1>PromoniThor Manage</h1>
    <?php 
	include_once("../pswd_poster.inc");
if(empty($_POST))
	{
		//formulier*/
	?>
    <h2>Upload poster:</h2>
  <form enctype="multipart/form-data" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
    <h4>Enter title and enddate of your activity. The poster will be visible till the end of the selected day.</h4>
    <table>
      <tr>
        <td>Title</td>
        <td><input type="text" maxlength="50" size="10" name="title" /></td>
      </tr>
      <tr>
        <td>End date [JJJJ-MM-DD]</td>
        <td><input type="date" name="date"  /></td>
      </tr>
      <tr>
        <td>Poster. Max 2MB, alleen JPG, JPEG, PNG of GIF<br />Poster dimensions for boardroom TV is 1024px by 576px or 1440px by 810px</td>
        <td><input type="file" name="fileToUpload" id="fileToUpload"></td>
      </tr>
      <tr>
      	<td>Password:</td>
        <td><input type="password" name="passwd" /></td>
      </tr>
    </table>
    <br />
    <input type="submit" value="Add poster" />
    <input type="reset" value="Clear form" />
  </form>
  <?php
	}	else{
	//einde formulier
	?>
    <h2>Upload status:</h2><p>
    <?php
		//clean up the date. If NULL is entered, use today
		$enddate = $_POST["date"];
		$title = $_POST["title"];
		
		$enddate = $enddate==NULL?date('Y-m-d'):filter_var($enddate,FILTER_SANITIZE_NUMBER_INT);
		//clean up the title, to be used as filename
		$title = preg_replace('/[^0-9a-z\_\-]/i','_',trim($title));
		//$title = substr(filter_var(str_replace(array(" ",'"',"'",',','.'),'_',),FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH),0,20);
		if(strlen($title)==0){
			$title = 'untitled';
		}

		//location to save the poster
		$target_dir = "upload/";
		$uploadOk = 1;

		//check the password
		if($_POST['passwd'] != $pswd_poster){
			echo "<br />Wrong password";
			$uploadOk = 0;
		}
		
		// Check if image file is a actual image or fake image
		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if($check !== false) {
			echo "<br />File is an image - " . $check["mime"] . ".";
		} else {
			echo "<br />File is not an image.";
			$uploadOk = 0;
		}
		
		$extension = strtolower(substr($check["mime"],strpos($check["mime"],'/')+1));
		
		
		// Allow certain file formats
		if($extension != "jpg" && $extension != "png" && $extension != "jpeg"
		&& $extension != "gif" ) {
			echo "<br />Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			$uploadOk = 0;
		}
		
		//adding time to filename to prevent duplicate filenames
		$target_file = $target_dir . $enddate .','. $title .','. time() .'.'. $extension;
		echo '<br />Filename: '.$target_file;
		
		// Check if file already exists
		if (file_exists($target_file)) {
			echo "<br />Sorry, file already exists.";
			$uploadOk = 0;
		}
		// Check file size
		if ($_FILES["fileToUpload"]["size"] > 10000000) {
			echo "<br />Sorry, your file is too large.";
			$uploadOk = 0;
		}
		
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			echo "<br />Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				echo "<br />The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
				echo "<h3>Poster on Flux monitors</h3>Mail this poster, the enddate (".$enddate.") and on which floors you would like your poster to the communication department of EE.";
			} else {
				echo "<br />Sorry, there was an error uploading your file.";
			}
		}
?>
<h2>Navigate</h2>
<a href="http://poster.thor.edu/list" title="List all submitted posters">List all submitted posters</a>
<br />
<a href="http://poster.thor.edu" title="Show posterviewer">Show posterviewer</a>
<br />
<a href="http://poster.thor.edu/upload" title="Upload new poster">Upload new poster</a>

</p>
<?php
	}
?>

</body>
</html>