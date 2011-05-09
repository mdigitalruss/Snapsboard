<?
	/* Configuration */
	$config = Array();
	
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	
	//Site configuration
	$config['signup_enabled'] = true;	//Enable or disable administration
	$config['site_url'] = "http://snapsboard.peterson-web.co.uk";
	$config['site_name'] = "SnapsBoard";
	$config['site_copyright'] = "Your name here";
	$config['site_email'] = "russ@peterson-web.co.uk";
	
	//Database configuration
	$config['dbHost'] = "s.saxon.sim-uk.net";
	$config['dbPort'] = "27017";
	$config['dbName'] = "SnapsBoard";

	//Allowed tags in user input
	$config['allowed_tags'] = "";
	
	$config['imaging_url'] = $config['site_url']; //Change this if your /src/standalone/ is on another server
	$config['images_url'] = "/grid/images/";	//path to the images script
	

	
?>