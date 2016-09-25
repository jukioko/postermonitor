<?php
/*
Jeroen van Oorschot 2015
e.t.s.v. Thor Eindhoven posterviewer
General functions. All tasks that are used in multiple scripts
*/
function getPosterFilenames(){
	$location = "upload/";
	$filenames = glob($location.'{*.jpg,*.png,*.jpeg,*.gif}', GLOB_BRACE);
	//sort the array again because items are sorted by extension
	sort($filenames);
	return $filenames;
}
function deletePoster($poster){
	if(substr_count($poster,',')==2 and substr_count($poster,'.')==1 and substr_count($poster,'/')==0){
		if(file_exists('upload/'.$poster)){
			if(rename('upload/'.$poster,'upload_deleted/'.$poster)){ //move the poster to deleted-folder
				return "Poster deleted";
			}else{
				return "Error in deleting poster";
			}
		}else{
			return "Poster does not exist";
		}
	}else{
		return "Invalid command supplied: " . $poster;
	}
}
?>