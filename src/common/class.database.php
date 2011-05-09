<?
/* Database logic */
	
class database
{
	public $db;
	
	public function __construct()
	{
		global $config;
		
		try 
		{
			//Connect to mongo!
			$m = new Mongo("mongodb://".$config['dbHost'].':'.$config['dbPort'].'/'.$config['dbName']);
			
			$mdb = $m->selectDB($config['dbName']);
			
			if(isset($config['dbUser']) && isset($config['dbPass']))
			{
				$mdb->authenticate($config['dbUser'],$config['dbPass']);
			}
			
			$this->db = $mdb;
		}
		catch(MongoConnectionException $e)
		{
			//No connection to mongo *sadface* Let's throw this error:
			die(file_get_contents("./interface/static/500.html"));
		}
	}
	
	public function __destruct()
	{
		//drop any connection
	}
}

?>