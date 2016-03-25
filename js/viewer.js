// Thor Postermonitor
// Jeroen van Oorschot 2015
// e.t.s.v. Thor Eindhoven
// Javascript code and settings for the viewer
/***********
Poster settings
***********/
//reloadtimes, value in seconds
//how long to display a poster
var posterChangeTime = 15;
//how often to reload posters. False for reload each cycle
var posterReloadTime = false;

//now done in css, not used anymore. Look in general.css for this time-setting.
//time in ms for the animation from poster to poster.
//var posterRefreshAnimation = 500; 

/**********
Activity overview settings
***********/
//reload activities every 10 minutes. Too often will block you from thor-exchange. The php-cache will limit to 5min refreshes.
var activityReloadTime = 10*60;
//number of activities that are shown
var maxNoActivities = 11;

/**********
Bottom bar options
**********/
//use sponsors or use posters at the bottom row
var postersInsteadOfSponsors = true;
//how long till changing order of sponsors
var sponsorChangeTime = 5;
//reload sponsors each hour.
var sponsorReloadTime = 3600;

/*********
Other variables, don't change these!
*********/
//mode to only show posters. Determined automatically by the existence of a thumbbar.
var posterOnlyMode;
//variable to hold array of posters
var thumbslider;
//arrays for the variable objects
var posters = [];
var thumbs = [];
var activities = [];
//settings for the bottomslider
var settings; 
//the number of the currently viewed poster
var n = 0;
//date names for the date and time
var daysOfWeek = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']; //weeks start at sunday
var daysOfWeekFull = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

/********************/
/*     posters      */
/********************/ 

//change poster, function is called every 'posterReloadTime'
function updatePosters(){
	if(posters.length == 0){
		loadPosters();
	}else{
		l = posters.length;
		o = n;
		n = (((n+1)%l)+l)%l;
		//reload array if end is reached. Array will be reloaded before the next poster has to be loaded.
		if( n == 0 ){
			//reload posters at end of cycle if posterReloadTime is false.
			if(posterReloadTime == false) {loadPosters()}
		}
		updatePoster(n,o);
	}
	//console.log('updatePosters complete (auto)');
}

//go to poster 'n'. n should be a valid number*/
function updatePoster(nextPoster,currentPoster){
	//update the poster number n global (if not done so yet)
	n = nextPoster;
	var	poster = posters[nextPoster];

	$($("div#posterview1 > img")[currentPoster]).removeClass("fadeIn").addClass("fadeOut");
	$($("div#posterview1 > img")[nextPoster]).removeClass("fadeOut").addClass("fadeIn");

	//change posters in the bottom if they are used instead of sponsor logo's
	if(postersInsteadOfSponsors && !posterOnlyMode){
		//thumbslider.goToSlide(posterNr);
		/*
		slide to the location of this image + the location of the previous image. 
		This works because UL is set to position absolute.
		If this is the first image it will be left aligned. Otherwise it is the second image in the row.
		*/
		var width = -1* ($($("img.thumb")[n]).position().left + n>0?$($("img.thumb")[n-1]).position().left:0);
		$("div#thumblist").css("transform","translateX("+width+"px)");
	}

	//console.log('updatePoster poster '+posterNr+' complete');
}

//load the list of posters from the server
function loadPosters(){
	//update the array containing the poster objects
	//console.log('update');
	$.getJSON('load_filenames.php', {Thor:"gaaf",type:"posters"} , function (data){
		//empty old array
		posters = [];
		var html = '';
		//fill array poster by poster
		$.each(data, function(key, val){
			loc = val;
			ext = loc.split('.').pop();
				posters.push({
					loc: loc
				});
			html += '<img class="imgPoster" src="'+val+'" />';
		});
		$("div#posterview1").html(html);
		//make the first one active
		$($("div#posterview1 > img")[0]).addClass("fadeIn");
		if(postersInsteadOfSponsors && !posterOnlyMode){
			loadThumbs();		
		}
	});
	//console.log('loadPosters complete');
}
/********************/
/*     bottom-bar   */
/********************/ 

//load the thumbs, either sponsors or postersthumbs
function loadThumbs(){
	thumbs = [];
	var html = '';
	if(postersInsteadOfSponsors){
		$.each(posters, function(key, val){
			//var thisposter = new thumb(val.loc);
			thumbs.push({
				loc: val.loc
			});
			html += '<img class="thumb" src="'+val.loc+'" data-index="'+(key)+'"/>';
			
		});
	}else{
		//update the array containing the sponsor objects
		$.getJSON('load_filenames.php',{Thor:"gaaf",type:"sponsors"}, function (data){
			//fill array sponsor by sponsor
			$.each(data, function(key, val){
				//console.log(key,val);
				//var thissponsor = new thumb(val);
				thumbs.push({
					loc: val
				});
				html += '<img class="thumb" src="'+val+'" />';
			});
		});
	}
	$("#thumblist").html(html);
}


/********************/
/*     activities   */
/********************/ 
//update the list of activities
function updateActivities(){
	var html = '';
	if(activities.length == 0){
		loadActivities();
	}
	else{
		for(var i=0;i<activities.length && i<maxNoActivities;i++){
			var a = activities[i].sta;
			html += '<div class="activity ' +  activities[i].ass+'" ><h2>'+activities[i].tit+'</h2><h3>';
			if(a != false){
				var astring = daysOfWeek[a.getDay()]+' '+a.getDate()+' '+months[a.getMonth()];
				html += astring;
				if(!activities[i].ald){ //non-allday activity, show starttime
					html += ', ' + a.getHours()+':'+ (a.getMinutes()<10?'0':'') + a.getMinutes();
				}else{ //allday activity, show endday
					var e = activities[i].end;
					var estring = daysOfWeek[e.getDay()]+' '+e.getDate()+' '+months[e.getMonth()];
					if( estring != astring){
						//only show enddate if it is different from the startdate
						html += ' to ' + estring;
					}
				}
			}
			html += '</h3></div>';
		}
		$("#activitiesContainer").html(html);
	}
	//console.log('updateActivities complete');
}

//load the activities from the thor-sharepoint (sites.ele.tue.nl)
function loadActivities(){
	//update the array containing the sponsor objects
	$.getJSON('load_activities.php',{Thor:'gaaf'}, function (data){
		//empty old array
		activities = [];
		//fill array poster by poster
		$.each(data, function(key, val){
			//var thisactivity = new activity(val);
			val.sta = new Date((val.sta*1000));
			val.end = new Date((val.end*1000));
	
			activities.push(val);
		});
		//sort the activities by start date
		activities.sort(function(a,b){return a.sta-b.sta});
		//show the activities
		updateActivities();
	});
	//console.log('loadActivities complete');
}


/*****************/
/* Date and Time */
/*****************/

function updateDateTime(){
	var d = new Date();
	var date = d.getDate()+' '+months[d.getMonth()];
    //var hour = d.getHours();
    //var minute = d.getMinutes().toString().length<2?'0'+d.getMinutes():d.getMinutes();
    //var second = d.getSeconds();
	var day = daysOfWeekFull[d.getDay()];
	//var time = hour + ":" + minute;
	$("#date").text(date);	
	$("#day").text(day);
}


//execute functions periodically and once at startup
$(function(){
	//var posteronlymode enabled
	posterOnlyMode = ($("#thumbcontainer").length)?false:true;


	loadPosters();
	//shift the posterarray, and switch the bottom bar with small posters if it is used.
	if(posterChangeTime){
		window.setInterval(function(){ updatePosters(); },posterChangeTime*1000);
	}
	//refresh the list of posters periodically
	if(posterReloadTime){
		window.setInterval(function(){ loadPosters(); },posterReloadTime*1000);
	}


	//key presses for changing the poster using arrow keys
	document.addEventListener('keydown', function(event) {
		l = posters.length;
		if(event.keyCode == 37) {
			var o = n;
			n = (((n-1)%l)+l)%l;
			updatePoster(n,o);
		}
		else if(event.keyCode == 39) {
			var o = n;
			n = (((n+1)%l)+l)%l;
			updatePoster(n,o);
		}
	});
	
	//click or touch on bottom row posterthumbs
	$("#thumbbar").on("click",function(event){
		//n is a global. Don't use it for possible undefined values
		clicknr = $(event.target).data('index');
		if(isFinite(clicknr) && clicknr<posters.length){
			//console.log(clicknr);
			updatePoster(clicknr,n);
		}
	});

	//timers and calls that are not needed in posteronly mode
	if(!posterOnlyMode){
		loadActivities();
		updateDateTime();
		
		//refresh the sponsors if they are enabled
		if(sponsorReloadTime && !postersInsteadOfSponsors){
			window.setInterval(function(){ loadThumbs(); },sponsorReloadTime*1000);
		}
		//periodically reload the activities.
		if(activityReloadTime){
			window.setInterval(function(){ loadActivities(); },activityReloadTime*1000);
		}
		//update the date every minute. This is not for the clock, only the date
		window.setInterval(function(){ updateDateTime(); },60*1000);
	}
	//console.log('init complete');
});
