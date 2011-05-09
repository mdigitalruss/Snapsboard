/* PhotoDB JS */
/* This whole js file is a bit crap. Excuse the mess */

var imageTitles = [];	//Array of image titles
var imageOwners = [];	//Array of image owners
var imageLikes = [];	//Array of image likes count
var hash = false;

function galleryExpandPic(mongoref)
{
	if($("#gallery_i").attr("src") != "/grid/images/"+ mongoref +"/w:560")
	{
		//make loader visible..
		$("#gallery_w").show('slow');
	
		//Load image
		$("#gallery_i").attr("src", "/grid/images/"+ mongoref +"/w:560");
		
		//Change lightbox link
		$("#gallery_lnk").attr("href", "/grid/images/"+ mongoref +"/w:1024");
		$("#gallery_lnk").attr("title", imageTitles[mongoref] + ' by ' + imageOwners[mongoref]);
		
		//Populate likes
		$("#gallery_nl").text(imageLikes[mongoref]);
		$("#gallery_l").attr("href", "/members/addlike/"+ mongoref);
		
		//Load callback
		$('#gallery_i').load(function() {
		  // Handler for .load() called.
		  $("#gallery_w").hide('fast');
		  $("#gallery_d").text('"' + imageTitles[mongoref] + '" by ' + imageOwners[mongoref]);
		});

	}	
}

function galleryHashListener()
{

	if(window.location.hash) 
	{
		if(hash != window.location.hash)
		{
			hash = window.location.hash;
		
			galleryExpandPic(hash.replace('#',''));
		}
	}
	
	t=setTimeout("galleryHashListener()",500); 
}