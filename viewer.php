
<!DOCTYPE html>
<html lang="en">
<head>
<!--
Jeroen van Oorschot 2015
e.t.s.v. Thor Eindhoven posterviewer
-->
<meta http-equiv="refresh" content="3600"><!--Do a regular refresh to prevent memory leak problems on raspberry pi-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="css/viewer.css" />
<link type="text/css" rel="stylesheet" href="css/lightslider.css" />
<script type="text/javascript" src="js/jquery-2.1.3.min.js"></script>
<script type="text/javascript" src="js/viewer.js"></script>
<script type="text/javascript" src="js/clock.js"></script>
<script src="js/lightslider.js"></script>
<title>Thor Postermonitor</title>
</head>

<body>

	<div id="container">
		<div id="sidebar" class="main">
           	<div id="general">
            	<div id="time"><canvas id="clock" width="230" height="230"></canvas></div>
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
            <div id="thumbcontainer">
            	<ul id="thumblist"></ul>
            </div>
        </div>
	</div>
    
</body>
</html>
