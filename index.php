<?php 
//with url-rewriting a GET['page'] is created. use another file in case /upload or /list is used.
		switch($_GET['page']){	
			case 'upload':
				header("Location:upload_poster.php");
				break;
			case 'list':
				header("Location:list_posters.php");
				break;
			case 'poster_only':
				header("Location:viewer_poster_only.php");
				break;
			default:
				header("Location:viewer.php");
				break;
		}
?>