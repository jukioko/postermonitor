// JavaScript voor Thor Postermonitor


var maxNoActivities = 11;
//reloadtimes, value in seconds
//how long to display a poster
var posterChangeTime = 15;
//how long till changing order of sponsors
var sponsorChangeTime = 5;

//how often to reload posters. False for reload each cycle
var posterReloadTime = false;
//reload activities every 10 minutes. Too often will block you from thor-exchange
var activityReloadTime = 10*60;
//reload sponsors each hour.
var sponsorReloadTime = 3600;
//time in ms for the animation from poster to poster.
var posterRefreshAnimation = 500; 
//use sponsors or use posters at the bottom row
var postersInsteadOfSponsors = true;

//variable to hold array of posters
var thumbslider;
var posters = [];
var thumbs = [];
var activities = [];
var n = 0;  //the number of the currently viewed poster
//var allowedImageExtensions = ["jpg","png","gif","JPEG","JPG","jpeg","PNG","GIF"];
//var allowedPDFExtensions = ["PDF","pdf"];
var daysOfWeek = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']; //weeks start at sunday
var daysOfWeekFull = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];


/*
//object for a poster
function poster(filename, ext){
	this.loc = filename;
	this.ext = ext;
	this.img = new Image()
	this.img.src = this.loc;
}

//object for a sponsor/thumb
function thumb(filename){
	this.loc = filename;
}
*/
//object for an activity
function activity(data){
	this.loc = data.loc;
	this.start = new Date((data.sta*1000));
	this.end = new Date((data.end*1000));
	this.association = data.ass;
	this.title = data.tit;
	this.allday = data.ald;
}



/********************/
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
}

//go to poster 'n'. n should be a valid number*/
function updatePoster(posterNr){
	var	poster = posters[posterNr];
	//go to next poster
	//animate and set width/heigth correct (scaling)
	$("#posterview1").animate({
		opacity: 0
	},posterRefreshAnimation/2,'',function(){
	$("#posterview1").css('background-image','url("'+poster.loc+'")');

	//change posters in the bottom if they are used instead of sponsor logo's
	if(postersInsteadOfSponsors){
		thumbslider.goToSlide(posterNr);
	}
		$("#posterview1").animate({
			opacity: 1
			},posterRefreshAnimation/2,'');
	});
}

//load the list of posters from the server
function loadPosters(){
	//update the array containing the poster objects
	console.log('update');
	$.getJSON('load_posters.php', {Thor:"gaaf",type:"posters"} , function (data){
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
		if(postersInsteadOfSponsors){
			loadThumbs();		
		}
	});
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
			var a = activities[i].start;
			html += '<div class="activity ' +  activities[i].association+'" ><h2>'+activities[i].title+'</h2><h3>';
			if(a != false){
				var astring = daysOfWeek[a.getDay()]+' '+a.getDate()+' '+months[a.getMonth()];
				html += astring;
				if(!activities[i].allday){ //non-allday activity, show starttime
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
}

//load the activities from the thor-sharepoint (sites.ele.tue.nl)
function loadActivities(){
	//update the array containing the sponsor objects
	$.getJSON('load_activities.php',{Thor:'gaaf'}, function (data){
		//empty old array
		activities = [];
		//fill array poster by poster
		$.each(data, function(key, val){
			var thisactivity = new activity(val);
			activities.push(thisactivity);
		});
		//sort the activities by start date
		activities.sort(function(a,b){return a.start-b.start});
		//show the activities
		updateActivities();
	});
	console.log('activities updated');
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
			html += '<li><div class="sponsorblock"><img class="sponsor" src="'+val.loc+'" /></div></li>';
		});
		$("#thumbcontainer").html(html);
		settings = {
			autoWidth: true,
			controls: false,
			pager: false,
			loop: true,
			auto: false,
/*			onBeforeSlide: function(){ /*This lets the posters scroll if you move the bottom bar*/
				/*updatePosters(this.getCurrentSlideCount());
			}*/
		};
		thumbslider = $("#thumbcontainer").lightSlider(settings);
	}else{
		//update the array containing the sponsor objects
		$.getJSON('load_posters.php',{Thor:"gaaf",type:"sponsors"}, function (data){
			//fill array sponsor by sponsor
			$.each(data, function(key, val){
				console.log(key,val);
				//var thissponsor = new thumb(val);
				thumbs.push({
					loc: val
				});
				html += '<li><div class="sponsorblock"><img class="sponsor" src="'+val+'" /></div></li>';
			});
			$("#thumbcontainer").html(html);
			settings = {
			/*item: 5,
			*item: 3,
			autoWidth: true,
			useCSS: true,
			controls: false,
			gallery: true,*/
			loop: true,
			auto: true,
			pause: sponsorChangeTime*1000,
			pager: false
			/*thumbMargin: 100*/
			};	
			thumbslider = $("#thumbcontainer").lightSlider(settings); 	
		});
	}
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

//key presses for changing the poster
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

//execute functions periodically and once at startup
$(function(){
	loadPosters();
	loadActivities();
	updateDateTime();
	//shift the posterarray, and switch the bottom bar with small posters if it is used.
	if(posterChangeTime){
		window.setInterval(function(){ updatePosters(); },posterChangeTime*1000);
	}
	//refresh the list of posters periodically
	if(posterReloadTime){
		window.setInterval(function(){ loadPosters(); },posterReloadTime*1000);
	}
	//refresh the sponsors if they are enabled
	if(sponsorReloadTime && !postersInsteadOfSponsors){
		window.setInterval(function(){ loadThumbs(); },sponsorReloadTime*1000);
	}
	if(activityReloadTime){
		window.setInterval(function(){ loadActivities(); },activityReloadTime*1000);
	}
	window.setInterval(function(){ updateDateTime(); },60*1000);
	
	
});