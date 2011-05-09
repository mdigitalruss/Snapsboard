<?
/* Main site framework */
include("config.php");
include("class.database.php");

class framework extends database
{
	public $user = Array();
	
	public function __construct()
	{
		global $config;
		//Check for installation
		if(!isset($config['dbHost']))
		{
			//Go to install page
			header("Location: /install.php");
		}
		else
		{
			parent::__construct();	//Connect Database
			
			//get user details
			$this->getUserDetails();
		}
	}
	
	public function handleAction()
	{
		//Get the requested action
		if(isset($_GET['act']))
		{
			$action = $_GET['act'];
		}
		else{
			$action = "home";
		}
		
		//Include
		if(file_exists("./src/_controllers/". $action .".php"))
		{
			//include the class
			include("./src/_controllers/". $action .".php");
			
			//initiate class
			$actionObject = new $action();

			// subaction?
			if(isset($_GET['sub'])){
				$subaction = $_GET['sub'];
			}
			else{
				$subaction = "_default";
			}
			
			if(isset($_GET['id'])){
				//Call subaction / default with id
				$model = $actionObject->$subaction($_GET['id']);
			}
			else{
				//Call subaction / default, no id
				$model = $actionObject->$subaction();
			}
			
			//now get view
			if(isset($model['view']) && file_exists("./src/_views/". $model['view'] .".php"))
			{
				include("./src/_views/". $model['view'] .".php");
			}
			else
			{
				include("./interface/static/404.html");	
			}
		}
		else
		{
			include("./interface/static/404.html");
		}
		
		//Log a stat!
		$this->addStat();
	}
	
	public function addStat()
	{
		//Details required:
		/*
			Remote address
			action
			subaction
			objectID
			time
			username (if applicable)
		*/
		
		$stat = array("ip" => $_SERVER["REMOTE_ADDR"],
					  "action" => ((isset($_GET['act']))? $_GET['act'] : ''),
					  "subaction" => ((isset($_GET['sub']))? $_GET['sub'] : ''),
					  "id" => ((isset($_GET['id']))? $_GET['id'] : ''),
					  "time" => time(),
					  "username" => $this->user['username']);
		
		$this->db->pagestats->insert($stat);
	}
	
	public function getUserDetails()
	{
		global $config;
		$user = "";	
		
		//Handle a log in
		if(isset($_POST['l_email']) && $_POST['l_email'] != "" &&
				isset($_POST['l_password']) && $_POST['l_password'] != "")
		{
			//Attempt to log the user in - check password/email
			$user = $this->db->users->findOne(array("email" => $_POST['l_email']));

			
			if($user)
			{
				$salt = substr($user['password'], 0,8);
				$passHash = substr($user['password'], 9);
				
				if(($salt . sha1($_POST['l_password'] . $salt)) != $user['password'])
				{
					//Salted passwords don't match?!
					$user = null;
				}
			}

			
		}
		elseif(isset($_COOKIE['userSessionID']))
		{
			
			if(isset($_GET['act']) && $_GET['act'] == "members" && isset($_GET['sub']) && $_GET['sub'] == "logout")
			{
				//Log this user out
				setCookie('userSessionID', "NA",time()-3600, "/");
			}
			else
			{
				$user = $this->db->users->findOne(array("uid" => $_COOKIE['userSessionID']));
			}
		}
		
		if($user)
		{
		
			//Logged in forever?
			if(isset($_COOKIE['userForever'])){
				if($_COOKIE['userForever'] == "true"){
					$timeout = time()*2;
					setCookie('userForever', "true",$timeout, "/");
				}
				else{
					$timeout = time()+3600;
					setCookie('userForever', "false",$timeout, "/");
				}
			}
			elseif(isset($_POST['l_forever']) && $_POST['l_forever'] != "")
			{
				$timeout = time()*2;
				setCookie('userForever', "true",$timeout, "/");
			}
			else
			{
				$timeout = time()-1; //Old members -> get new login cookies
			}
			
			//Set the cookie
			setCookie('userSessionID', $user['uid'],$timeout, "/");
			
			
			//Grab user details
			$this->user['mongoRef'] = $user['_id'];
			$this->user['username'] = $user['username'];
			$this->user['isMember'] = true;
			$this->user['failedLogin'] = false;
			$this->user['email'] = $user['email'];
			
			//Avatar?
			if(isset($user['avatar'])){
				$this->user['avatar_url'] = $config['images_url'].$user['avatar']; //= $user['avatar'];
			}
			else{
				$this->user['avatar_url'] = $config['images_url']."annon";
			}
			
			//Bio?
			if(isset($user['bio'])){
				$this->user['bio'] = $user['bio'];
			}
			
			//badge?
			if(isset($user['badge'])){
				$this->user['badge'] = $user['badge'];
			}
			
			//Admin?
			if(isset($user['IsAdmin'])){
				$this->user['isAdmin'] = $user['IsAdmin'];
			}
			
			//watermark?
			if(isset($user['watermark'])){
				$this->user['watermark'] = $user['watermark'];
			}

		}
		else
		{
			$this->user['username'] = "Guest";
			$this->user['isMember'] = false;
			
			if(isset($_POST['l_email']) || isset($_POST['l_password'])){
				$this->user['failedLogin'] = true;
			}
			else{
				$this->user['failedLogin'] = false;
			}
		}
	}

	
	public function isUser($mongoRef)
	{
		return (isset($this->user['mongoRef']) && ($this->user['mongoRef'] == $mongoRef));
	}

	
	public function randString($len)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$string = '';    


		for ($p = 0; $p < $len; $p++) {
			$string .= $characters[mt_rand(0, strlen($characters))];
		}

		return $string;
	}

	public function clean($string)
	{
		global $config;
		return strip_tags($string,$config['allowed_tags']);
	}
	
	public function util_Truncate($textToTrundicate,$intLength)
	{
		if(strlen($textToTrundicate) > $intLength)
		{
			//Cut the string to less than 32 chars
			$title = substr($textToTrundicate,0,$intLength);
			//Get the last space
			$subto = strrpos($title," ");
			//cut again to the last word
			$title = substr($title,0,$subto);
			$title.="...";
		}
		else{
			$title = $textToTrundicate;
		}
		
		return $title;
	}
}

?>