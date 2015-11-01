<?php
function compress_image($source_url, $destination_url, $quality) 
	{
		$info = getimagesize($source_url); 
		if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source_url); 
		elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source_url); 
		elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source_url); 
		imagejpeg($image, $destination_url, $quality); return $destination_url; 
	}
//www.apptha.com/blog/how-to-reduce-image-file-size-while-uploading-using-php-code/#sthash.1eihSLm5.dpuf


//php recursive image compress
if(empty($_POST))
	{
?>
<form enctype="multipart/form-data" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
        <input type="file" name="fileToUpload" id="fileToUpload"><input type="text" name="test">
        
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
		
		//location to save the poster
		$target_dir = "upload/";
		$uploadOk = 1;

		
		
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
		
		
		$target_file = $target_dir . 'test'. time() .'.'. $extension;
		echo '<br />Filename: '.$target_file;
		
		// Check if file already exists
		if (file_exists($target_file)) {
			echo "<br />Sorry, file already exists.";
			$uploadOk = 0;
		}
		// Check file size
		$size = $_FILES["fileToUpload"]["size"];
		echo "<br />Size upload: ".$size." size meas: ".filesize($_FILES["fileToUpload"]["tmp_name"]);
		
		if ($size > 2000000) {
			echo "<br />Sorry, your file is too large.";
			$uploadOk = 0;
		}
		$file1 = $_FILES["fileToUpload"]["tmp_name"];
//		$file2 = $file1;
		$size = filesize($file1);
		echo"<br />No compression: ".$size;
		for($q=70;$size > 500000 && $q>30;$q-=10){
			//larger than 500kb, compress
			compress_image($file1,$file1.'.jpg',$q);
			$file1 = $file1.'.jpg';
			$size = filesize($file1);
		echo "<br />compressed with q=".$q.", new size: ".$size;
		
		}
		
		
		
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			echo "<br />Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($file1, $target_file)) {
				echo "<br />The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
			} else {
				echo "<br />Sorry, there was an error uploading your file.";
			}
		}
	}
?>
