<?
	/* Configuration */
	$config = Array();
	
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	
	//Site configuration
	$config['signup_enabled'] = false;	//Enable or disable administration
	$config['site_url'] = "http://snapsboard.domain.com";
	$config['site_name'] = "SnapsBoard";
	$config['site_copyright'] = "Your name here";
	
	//Database configuration
	$config['dbHost'] = "mongo.domain.com";
	$config['dbPort'] = "27017";
	$config['dbUser'] = "database_user";
	$config['dbPass'] = "S3cur3pa55w0rd";
	$config['dbName'] = "SnapsBoard";

	//Allowed tags in user input
	$config['allowed_tags'] = "";
	
	$config['imaging_url'] = $config['site_url']; //Change this if your /src/standalone/ is on another server
	$config['images_url'] = "/grid/images/";	//path to the images script
	

	
?>