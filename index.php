<?php 
//with url-rewriting a GET['page'] is created. use another file in case /upload or /list is used.
	if($_GET['page']){
		switch($_GET['page']){	
			case 'upload':
				header("Location:upload_poster.php");
				break;
			case 'list':
				header("Location:list_posters.php");
				break;
		}
	}else{
//if no GET show the page below, this is the normal postermanager.
		?>
        
<!DOCTYPE html>
<html lang="en">
<head>
<!--
Jeroen van Oorschot 2015
e.t.s.v. Thor Eindhoven posterviewer
-->
<meta http-equiv="refresh" content="3600"><!--Do a regular refresh to prevent memory leak problems on raspberry pi-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="./css/poster.css" />
<link type="text/css" rel="stylesheet" href="./css/lightslider.css" />
<script type="text/javascript" src="./js/jquery-2.1.3.min.js"></script>
<script type="text/javascript" src="./js/poster.js"></script>
<script type="text/javascript" src="./js/clock.js"></script>
<script src="./js/lightslider.js"></script>
<title>Thor Postermonitor</title>
</head>

<body>

	<div id="container">
		<div id="sidebar" class="main">
           	<div id="general">
            	<div id="time"><canvas id="clock" width="152" height="152"></canvas></div>
                <div id="daydate">
            		<div id="day"></div>
                	<br />
                	<div id="date"></div>
                </div>
			</div>
            <div id="activities">
            	<div id="activitiesContainer"></div>
            </div>
        </div>
        <div class="poster" id="posterBg"></div>
        <div class="poster posterview" id="posterview1"></div>
        <!--<div class="poster posterview" id="posterview2"></div>-->
        <div id="thumbbar" class="main">
            <ul id="thumbcontainer"></ul>
        </div>
	</div>
    
</body>
</html>
<?php } ?>
