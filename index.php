<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?
/*
Jeroen van Oorschot 2015
e.t.s.v. Thor Eindhoven posterviewer
*/

?>
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
            	<div id="time"><canvas id="clock" width="128" height="128"></canvas></div>
                <div id="daydate">
            	<div id="day"></div>
                <br />
                <div id="date"></div>
                </div>
                
                <!--<div id="weatherdate">
                	<span id="dateText"></span>
                    <!--<img id="weatherIcon" src="" width="100" height="100" />-->
                    <!-- <span id="weatherText"></span>-->
                <!--</div>-->
			</div>
            <div id="activities">
            <div id="activitiesContainer"></div>
            </div>
        </div>
        <div id="posterview" class="main">
            <img src="" id="posterimage" class="center" />
        </div>
             
        <div id="thumbbar" class="main">
        <ul id="thumbcontainer"></ul>
        <!--<h2>Thor is sponsored by:</h2>
			<div class="sponsorblock"><img src="" class="sponsor center" id="sp0" /></div>
 			<div class="sponsorblock"><img src="" class="sponsor center" id="sp1" /></div>
			<div class="sponsorblock"><img src="" class="sponsor center" id="sp2" /></div>
 			<div class="sponsorblock"><img src="" class="sponsor center" id="sp3" /></div>
			<div class="sponsorblock"><img src="" class="sponsor center" id="sp4" /></div>
 			<div class="sponsorblock"><img src="" class="sponsor center" id="sp5" /></div>
			<div class="sponsorblock"><img src="" class="sponsor center" id="sp6" /></div>
 			<div class="sponsorblock"><img src="" class="sponsor center" id="sp7" /></div>
			<div class="sponsorblock"><img src="" class="sponsor center" id="sp8" /></div>
 			<div class="sponsorblock"><img src="" class="sponsor center" id="sp9" /></div>-->
        </div>

	</div>
</body>
</html>
