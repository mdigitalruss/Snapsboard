<?
	/* Configuration */
	$config = Array();
	
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	
	$arrSettings = parse_ini_file("config.ini",true);
	
	//Site configuration
	$config['signup_enabled'] = false;	//Enable or disable administration
	$config['site_url'] = $arrSettings['webserver']['domain'];
	$config['site_name'] = $arrSettings['website']['name'];
	$config['site_copyright'] = $arrSettings['website']['copyright'];
	
	//Database configuration
	$config['dbHost'] = $arrSettings['database']['dbhost'];
	$config['dbPort'] = $arrSettings['database']['dbport'];
	$config['dbUser'] = $arrSettings['database']['dbuser'];
	$config['dbPass'] = $arrSettings['database']['dbpass'];
	$config['dbName'] = $arrSettings['database']['dbname'];

	//Allowed tags in user input
	$config['allowed_tags'] = "";
	
	$config['imaging_url'] = $arrSettings['webserver']['imaging_domain']; //Change this if your /src/standalone/ is on another server
	$config['images_url'] = $arrSettings['webserver']['imaging_path'];	//path to the images script
	

	
?>