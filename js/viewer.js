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

3/********************/
/*     posters      */
/********************/ 

//change poster, function is called every 'posterReloadTime'
function updatePosters(){
	if(posters.length == 0){
		loadPosters();
	}else{
		l = posters.length;
		n = (((n+1)%l)+l)%l;
		//reload array if end is reached. Array will be reloaded before the next poster has to be loaded.
		if( n == 0 ){
			//reload posters at end of cycle if posterReloadTime is false.
			if(posterReloadTime == false) {loadPosters()}
		}
		updatePoster(n);
	}
	//console.log('updatePosters complete (auto)');
}

//go to poster 'n'. n should be a valid number*/
function updatePoster(posterNr){
	//update the poster number n global (if not done so yet)
	n = posterNr;
	var	poster = posters[posterNr];
	
	//old jquery animation system, no longer used.
	//go to next poster
	//animate and set width/heigth correct (scaling)
	//$("#posterview1").animate({
	//	opacity: 0
	//},posterRefreshAnimation/2,'',function(){
	
	//		$("#posterview1").animate({
//			opacity: 1
//			},posterRefreshAnimation/2,'');
//	});
	
	//what to do when fadeout is finished
	var fadeOutFunc = function(e) { 
   		//console.log("fin fadeout");
		//replace poster
   		$("#posterview1").css('background-image','url("'+poster.loc+'")');
   		$("#posterview1").removeClass("fadeOut");
		//fade in the poster
		$("#posterview1").addClass("fadeIn");
		//remove this event
		$(this).unbind(e);
	}
	
	//remove the previous classes
	$("#posterview1").removeClass("fadeIn");
	
	//event handler for fadeout completed
	//oanimationend webkitAnimationEnd
	$("#posterview1").bind('animationend', fadeOutFunc);
	
	//actually fadeout the image
	$("#posterview1").addClass("fadeOut");
	
	//change posters in the bottom if they are used instead of sponsor logo's
	if(postersInsteadOfSponsors && !posterOnlyMode){
		thumbslider.goToSlide(posterNr);
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
		//fill array poster by poster
		$.each(data, function(key, val){
			loc = val;
			ext = loc.split('.').pop();
				posters.push({
					loc: loc
				});
		});
		if(postersInsteadOfSponsors && !posterOnlyMode){
			loadThumbs();		
		}
	});
	//console.log('loadPosters complete');
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
			html += '<li><img class="thumb" src="'+val.loc+'" data-index="'+key+'"/></li>';
			
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
				html += '<li><img class="thumb" src="'+val+'" /></li>';
			});
		});
	}
	$("#thumblist").html(html);
	thumbslider.refresh();
	//if(thumbslider){thumbslider.destroy();}
	//thumbslider = $("#thumbcontainer").lightSlider(settings);
	//console.log('loadThumbs complete');
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
			n = (((n-1)%l)+l)%l;
			updatePoster(n);
		}
		else if(event.keyCode == 39) {
			n = (((n+1)%l)+l)%l;
			updatePoster(n);
		}
	});
	
	//click or touch on bottom row posterthumbs
	$("#thumbbar").on("click",function(event){
		//n is a global. Don't use it for possible undefined values
		clicknr = $(event.target).data('index');
		if(isFinite(clicknr) && clicknr<posters.length){
			//console.log(clicknr);
			updatePoster(clicknr);
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
		
		//settings for the slider
		if(postersInsteadOfSponsors){
			settings = {
				autoWidth: true,
				controls: false,
				pager: false,
				loop: true,
				auto: false
			//	gallery:true
			};
		}else{
			settings = {
				autoWidth: true,
				controls: false,
				gallery: true,
				loop: true,
				auto: true,
				pause: sponsorChangeTime*1000,
				pager: false
			};
		}
		//make the thumbslider
		thumbslider = $("#thumblist").lightSlider(settings);
		
		//refresh the thumbslider after (approximately) all images are loaded, so it adjusts to the image width
		//and switch to the first poster
		setTimeout(function(){
			thumbslider.refresh();
			updatePoster(0);
		}, 5000);
	}
	//console.log('init complete');
});
