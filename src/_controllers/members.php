<?
	/* Membership system - controller */
	
Class members
{
	private $title;
	private $body;
	private $showBody = true;

	/* _default: required, default action if no subaction is specified */
	public function _default()
	{
		return $this->recent();
	}
	
	public function addlike($imageRef)
	{
		global $framework;
		
		if(!$framework->user['isMember'])
		{
			$model = Array('view' => 'generic_login_message');
		}
		else
		{
			$image = $framework->db->images->findone(array("imageref" => new MongoID($imageRef)));
			$like = $framework->db->likes->findone(array("imageref" => new MongoID($imageRef),"userid" => new MongoID($framework->user['mongoRef'])));
			
			if($image)
			{
				if($like)
				{
					//Already a member / Just logged in
					$model = Array('view' => 'generic_message');
					
					$model['title'] = 'Like Image';
					$model['message'] = 'You already liked this image!';
					
					$model['link_href'] = '/album/view/'.$image['thread']['id'];
					$model['link_title'] = 'Back';
				}
				else
				{
					//Add like
					$newLike = Array("userid" => new MongoID($framework->user['mongoRef']),
									 "imageref" => new MongoID($imageRef),
									 "thread" => Array("name" => $image['thread']['name'], "id" => new MongoID($image['thread']['id'])),
									 "owner" => Array("name" => $image['owner']['name'], "id" => new MongoID($image['owner']['id'])),
									 "name" => $image['name'],
									 "timestamp" => time());
					
					//Insert into the database
					$framework->db->likes->insert($newLike);
					
					$model = Array('view' => 'generic_message');
					
					$model['title'] = 'Like Image';
					$model['message'] = 'You have liked this image - you can view the images you like in the "My Account" area';
					
					$model['link_href'] = '/album/view/'.$image['thread']['id'];
					$model['link_title'] = 'Back';
					//msg
				}
			}
			else
			{
				$model = Array('view' => 'generic_message');
				
				$model['title'] = 'Like Image';
				$model['message'] = 'The image you are trying to like no longer exists';
				
				$model['link_href'] = '/';
				$model['link_title'] = 'Home';
			}
			
		}
		
		return $model;
	}
	
	public function unlike($likeID)
	{
		global $framework;
		
		if(!$framework->user['isMember'])
		{
			$model = Array('view' => 'generic_login_message');
		}
		else
		{
			$framework->db->likes->remove(array("_id" => new MongoID($likeID)));
			
			$model = Array('view' => 'generic_message');
			$model['title'] = "Unlike Image";
			$model['message'] = "You have unliked that image.";
			$model['link_href'] = "/members/me/";
			$model['link_title'] = "Back";				
		}
		
		return $model;
	}
	
	public function forgotpass()
	{
		global $framework;
		
		if($framework->user['isMember'])
		{
			$model = Array('view' => 'generic_message');
			$model['title'] = "Error";
			$model['message'] = "You are already logged in.";
			$model['link_href'] = "/";
			$model['link_title'] = "Home";	
		}
		else
		{
			if(isset($_POST['l_email']) && $_POST['l_email'] != "")
			{
				//look up user
				$member = $framework->db->users->findOne(array("email" => $_POST['l_email']));
				
				if($member)
				{
					//user exists: generate and set new pw
					$salt = $framework->randString(8);
					$srcPassword = $framework->randString(8);
					$password = $salt . sha1($srcPassword . $salt);
					
					$details = array('password' => $password);
					
					$framework->db->users->update(array("_id" => $member['_id']), array('$set' => $details));
					
					//Email user the pw
					$message = "Hello ". $member['username']. ", <br/><br/>Recently you requested a new password, so here it is! 
								<br/><br/>Simply go to <a href=\"http://farmsnaps.com/members/login\">Farmsnaps.com/members/login</a> and
								enter the following information: <br/><br/>
								<b>Email:</b> ". $_POST['l_email'] ."<br/>
								<b>Password:</b> ". $srcPassword ."<br/><br/>
								Please remember to change your password as soon as you log in. Enjoy!
								<br/><br/>
								P.S, I am a robot. Please don't reply to me, because I won't understand what you're saying.";
								
					
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= 'From: Farmsnaps <no-reply@farmsnaps.com>' . "\r\n";
					
					mail($_POST['l_email'], 'Farmsnaps.com: New password!', $message, $headers);
					//Status message
					
					$model = Array('view' => 'generic_message');
					$model['title'] = "Forgot Password";
					$model['message'] = "Congratulations. A new password was generated, and sent to your email address. <br/><br/>
										Please check your email for instructions. <br/><br/>
										If you can't find the email, please check your spam folder, 
										and failing that you could add no-reply@farmsnaps.com to your contacts, then request another password.";
					$model['link_href'] = "/members/login";
					$model['link_title'] = "Log In";	
				}
				else
				{
					$model = Array('view' => 'generic_message');
					$model['title'] = "Error";
					$model['message'] = "There is no account related to that email address. Please register a new account.";
					$model['link_href'] = "/members/register";
					$model['link_title'] = "Register";	
				}

			}
			else
			{
				$model = Array('view' => 'members_forgotpass');
			}
		}

		
		return $model;
	}
	
	public function goodbye($memberid)
	{
		global $framework;
		
		if(!$framework->user['isMember'])
		{
			$model = Array('view' => 'generic_login_message');
		}
		else
		{
			if(isset($_POST['ACK']))
			{
				$member = $framework->db->users->findOne(array("_id" => new MongoID($memberid)));
				
				if($member && ($member['_id'] == $framework->user['mongoRef'] || (isset($framework->user['isAdmin']) && $framework->user['isAdmin'] == true)))
				{

					//remove albums
					$threads = $framework->db->threads->find(array("owner.id" => new MongoID($memberid)));
					
					foreach($threads as $thread)
					{
						//Remove images
						$framework->db->images->remove(array("thread.id" => new MongoID($thread['_id'])));
						
						//Remove comments
						$framework->db->comments->remove(array("thread.id" => new MongoID($thread['_id'])));
						
						//Remove
						$framework->db->threads->remove(array("_id" => new MongoID($thread['_id'])));
					}
					
					//remove images
					$framework->db->images->remove(array("owner.id" => new MongoID($memberid)));
					
					//remove likes
					$framework->db->likes->remove(array("owner.id" => new MongoID($memberid)));	//Likes on images which i own
					$framework->db->likes->remove(array("userid" => new MongoID($memberid)));	//Items i have liked
					
					//remove comments
					$framework->db->comments->remove(array("owner.id" => new MongoID($memberid)));
					
					//remove member
					$framework->db->users->remove(array("_id" => new MongoID($memberid)));
					
					
					$model = Array('view' => 'generic_message');
					$model['title'] = "Account Removed";
					$model['message'] = "The account, along with all albums, images and comments was deleted.";
					$model['link_href'] = "/";
					$model['link_title'] = "Home";	
				}
				else
				{
					$model = Array('view' => 'generic_message');
					$model['title'] = "Error";
					$model['message'] = "User does not exist, or you do not have permission to remove the user.";
					$model['link_href'] = "/";
					$model['link_title'] = "Home";					
				}
			}
			else
			{
				$model = Array('view' => 'generic_ack');
				$model['title'] = "Remove Account";
				$model['message'] = "Are you sure you want to delete this account?</p><p>Warning! ALL albums, images and comments will be deleted!</p>
										<p> This action CANNOT be reversed - all data is removed permentantly and cannot be restored.</p>
										<p>Only continue if you are certain that you do not want this account any more. The administrators will NOT be able to un-delete this account";
				$model['form_action'] = "/members/goodbye/".$memberid;
				$model['form_button'] = "Remove Account";
			}
		}
		
		return $model;
	}
	
	public function login()
	{
		global $framework;

		if($framework->user['isMember'])
		{
			//Already a member / Just logged in
			$model = Array('view' => 'generic_message');
			
			$model['title'] = 'Log In';
			$model['message'] = 'Login successful. Welcome back, '. $framework->user['username'] .'!';
			
			$model['link_href'] = '/';
			$model['link_title'] = 'Continue';
		}
		elseif($framework->user['failedLogin'])
		{	
			//Login failed
			$model = Array('view' => 'generic_message');
			$model['title'] = 'Log In';
			
			$model['message'] = 'Uhoh, login failed - The email or password you provided was not recognised.</p>
								<p>&bull; Did you typo your details? <a href="/members/login/">Try again</a></p>
								<p>&bull; Forgot your details? <a href="/members/forgotpass/">Send me my password</a></p>
								<p>&bull; Don\'t have an account? <a href="/members/login/">Register now!</a>';
		}
		elseif(!isset($_POST['l_email']) || $_POST['l_email'] == "" || !isset($_POST['l_password']) || $_POST['l_password'] == "")
		{
			//none of the above - Show login form
			$model = Array('view' => 'members_login');
			
			if(isset($_POST['l_email']) ||
				isset($_POST['l_password']))
			{
				$r_email = $_POST['l_email'];	//Store name
				$r_password = $_POST['l_password'];	//Store email
				
				//Was there a login error? missing details?
				if($_POST['l_email']){ $model['err_email'] = '<p class="txt-error txt-r">Please provide your email address</p>'; }
				if($_POST['l_password']){ $model['err_password'] = '<p class="txt-error txt-r">Please provide your password</p>'; }
			}

		}
		
		return $model;
	}
	
	public function logout()
	{
		$model = Array('view' => 'generic_message');
		
		$model['title'] = 'Log Out';
		$model['message'] = 'You are now logged out. Thanks for visiting!';
		
		$model['link_href'] = '/';
		$model['link_title'] = 'Continue';
		
		return $model;
	}
	
	public function register()
	{
		$this->title = "Register";
		
		global $framework, $config;
		
		if($config['signup_enabled'] == false)
		{
			$model = Array('view' => 'generic_message');
				
			$model['title'] = 'Register';
			$model['message'] = 'Hey you there! Registration is closed at the moment, but we will be open soon.. Check back later!</p><p><img src="/interface/images/you-cant-come.jpg" alt="You can\'t come! nuh-nuh nuh nuh-nuh!"/><br/>You can\'t come! nuh-nuh nuh nuh-nuh!';
			
			$model['link_href'] = '/';
			$model['link_title'] = 'Continue';
		}
		else
		{
			if($framework->user['isMember'])
			{
				$model = Array('view' => 'generic_message');
				
				$model['title'] = 'Register';
				$model['message'] = 'You don\'t need to register - you already have an account!';
				
				$model['link_href'] = '/';
				$model['link_title'] = 'Continue';
			}
			elseif(isset($_POST['r_name']) && $_POST['r_name'] != "" &&
					isset($_POST['r_password']) && $_POST['r_password'] != "" &&
					isset($_POST['r_passconf']) && $_POST['r_passconf'] != "" &&
					isset($_POST['r_email']) && $_POST['r_email'] != "" &&
					$_POST['r_passconf'] == $_POST['r_password'])
			{
				// Check the account is not a duplicate		
				$user = $framework->db->users->findOne(array("email" => $_POST['r_email']));
				
				if($user)
				{
					$model = Array('view' => 'generic_message');
					
					$model['title'] = 'Register';
					$model['message'] = '<p>We already found an account belonging to "'.$_POST['r_email'].'". 
											If you have already registered, you can log in or reset your password.</p><br/>
										<p>&bull; Forgot your details? <a href="/members/forgotpass/">Send me my password</a></p>
										<p>&bull; Forgot you even had an account? <a href="/members/login/">Log in</a></p>
										<p>&bull; Did you typo your email address? <a href="/members/register/">Try again</a></p>';
				}
				else
				{
					//Build the superdooper password
					$salt = $framework->randString(8);
					$password = $salt . sha1($_POST['r_password'] . $salt);
					
					//build the json doc
					$newUser = Array("email" => $framework->clean($_POST['r_email']),
									 "password" => $password,
									 "uid" => md5(microtime()),
									 "username" => $framework->clean($_POST['r_name']),
									 "regtime" => time());
					
					//Insert into the database
					$framework->db->users->insert($newUser);
					
					//Output:
					$model = Array('view' => 'generic_message');
				
					$model['title'] = 'Register';
					$model['message'] = 'Congratulations, your membership is now active! You can now log in<br/>';
					
					$model['link_href'] = '/members/login/';
					$model['link_title'] = '>Log In';

				}
			}
			else
			{
				//Output:
				$model = Array('view' => 'members_register');
				
				// Handle possible errors with the submitted data
				if(isset($_POST['r_name']) ||
					isset($_POST['r_password']) ||
					isset($_POST['r_passconf']) ||
					isset($_POST['r_email']))
				{
					$r_name = $_POST['r_name'];	//Store name
					$r_email = $_POST['r_email'];	//Store email
					
					//Check the passwords match/don't match. Either way, the user will have to fill them in again.
					if($_POST['r_passconf'] != $_POST['r_password']){
						$model['error_message'] = '<p class="txt-error">Oops! The passwords do not match! Please fix the errors highlighted below:</p><br/>';
					}
					else{
						$model['error_message'] = '<p class="txt-error">Oops! Looks like something was not filled in! Please fix the errors below:</p><br/>';
					}
					
					//Handle other errors
					if($_POST['r_name'] == ""){ 
						$model['err_name'] = '<p class="txt-error txt-r">Please provide a display name</p>'; 
					} 
					else { 
						$model['err_name'] = '<p class="txt-ok txt-r">Your display name is OK</p>'; 
					}
					
					
					if($_POST['r_email'] == ""){ 
						$model['err_email'] = '<p class="txt-error txt-r">Please provide a valid email</p>'; 
					}
					else{ 
						$model['err_email'] = '<p class="txt-ok txt-r">Your email address is OK</p>'; 
					}
					
					if($_POST['r_password'] == "" || $_POST['r_passconf'] != $_POST['r_password']){ $model['err_password'] = '<p class="txt-error txt-r">Please provide a password</p>'; }
					if($_POST['r_passconf'] == "" || $_POST['r_passconf'] != $_POST['r_password']){ $model['err_passconf'] = '<p class="txt-error txt-r">Please confirm your password</p>'; }
				}
			}
		}
		
		return $model;
	}

	public function me()
	{
		global $framework;
		
		
		
		if(!$framework->user['isMember'])
		{
			$model = Array('view' => 'generic_login_message');
		}
		else
		{ 
			$model = Array('view' => 'members_me');
			$model['albums'] = $framework->db->threads->find(array("owner.id" => $framework->user['mongoRef']));
			$model['likes'] = $framework->db->likes->find(array("userid" => $framework->user['mongoRef']));
		}
		
		return $model;
	}

	public function avataradd()
	{
		global $framework;
		
		if(!$framework->user['isMember'])
		{
			$model = Array('view' => 'generic_login_message');
		}
		else
		{
			$model = Array('view' => 'generic_message');
			
			//Upload avatar
			$model['title'] = 'Avatar';
			
			if((isset($_FILES["avatar"]) && $_FILES["avatar"]['name'] != "") &&(
				($_FILES["avatar"]["type"] == "image/jpeg") || 
				($_FILES["avatar"]["type"] == "image/jpg") || 
				($_FILES["avatar"]["type"] == "image/pjpeg") || 
				($_FILES["avatar"]["type"] == "image/png") &&
				($_FILES["avatar"]["size"] < 1048576)))
			{
				$filename = $_FILES['avatar']['name']; // original filename
				
				$gridFS = $framework->db->getGridFS();
				$image_id = $gridFS->storeUpload('avatar',$filename); //load file into MongoDB  
				
				//Update user with the mongo id as an avatar
				$details = array('avatar' => new MongoID($image_id));
				
				$framework->db->users->update(array("_id" => $framework->user['mongoRef']), array('$set' => $details));
				
				if($image_id != "")
				{
					$model['message'] = '<p>Your avatar was added</p><br/>';
				}
				else
				{
					$model['message'] = '<p>There was a problem with your upload..</p><br/>';
				}
			}
			else
			{
				$model['message'] = '<p>You avatar is an invalid type or size. The image must be jpg or png format, and less than 1mb in size.</p><br/>';
			}
			
			$model['link_href'] = '/members/me/';
			$model['link_title'] = 'Continue';

		}
		
		return $model;
		
	}
	
	public function update()
	{
		global $framework;
		
		if(!$framework->user['isMember']){
			$model = Array('view' => 'generic_login_message');
		}
		else{
		
			$model = Array('view' => 'generic_message');
			$model['title'] = 'Update settings';
			
			if(isset($_POST['r_name']) && $_POST['r_name'] != "" &&
					isset($_POST['r_password']) && 
					isset($_POST['r_passconf']) && 
					$_POST['r_passconf'] == $_POST['r_password'])
			{	
				//Update user's details
				$details = array('username' => $framework->clean($_POST['r_name']));
				
				//Updating the password?
				if($_POST['r_password'] != "")
				{
					//Build the superdooper password
					$salt = $framework->randString(8);
					$password = $salt . sha1($_POST['r_password'] . $salt);
					
					$details['password'] = $password;
				}
				
				//adding / updating bio?
				if($_POST['r_bio'] != ""){
					$details['bio'] = $framework->clean($_POST['r_bio']);
				}
				
				//adding / updating watermark?
				if($_POST['r_watermark'] != ""){
					$details['watermark'] = $framework->clean($_POST['r_watermark']);
				}
				
				$framework->db->users->update(array("_id" => $framework->user['mongoRef']), array('$set' => $details));
				
				$model['message'] = '<p>You account has been updated</p><br/>';
			}
			else
			{
				$model['message'] = '<p>Some details were missing, or your new passwords did not match. Your details were not updated.</p><br/>';
			}
			
			$model['link_href'] = '/members/me/';
			$model['link_title'] = 'Continue';
		}
		
		return $model;
	}
	
	public function view($memberID)
	{
		//View a member..
		global $framework;
		//get my inbox
		if(!$framework->user['isMember'])
		{
			$model = Array('view' => 'generic_login_message');
		}
		else
		{
			$user = $framework->db->users->findOne(array("_id" => new MongoID($memberID)));
			
			if($user)
			{
				$model = Array('view' => 'members_view');
				
				$model['user'] = $user;
					
				$model['albums'] = $framework->db->threads->find(array("owner.id" => new MongoID($memberID)));
				
				$model['likes'] = $framework->db->likes->find(array("userid" => new MongoID($memberID)));

			}
			else
			{
				$model = Array('view' => 'generic_message');
				$model['title'] = "View profile";
				$model['message'] = "<p>The member you are looking for does not exist.</p>";
			}
		}
		
		return $model;
	}
	
	public function recent($page = 1)
	{
		global $framework;
		$pageSize = 20;
		
		$model = Array('view' => 'members_recent');
		
		$model['users'] = $framework->db->users->find()->sort(array('regtime' => -1))->limit($pageSize)->skip($pageSize*($page-1));
		
		$count =  $framework->db->users->find()->sort(array('regtime' => -1))->count();
		
		$model['pagination'] = "";
		
		// previous button
		if($page > 1)
		{
			$model['pagination'] .= '<a href="/members/recent/'. ($page-1) .'">&lt; Previous</a> - ';
		}

		// Show page number
		$model['pagination'] .= 'Page '.($page);

		// Next button
		if($count > ($pageSize*$page))
		{
			$model['pagination'] .= ' - <a href="/members/recent/'. ($page+1) .'">Next &gt;</a> ';
		}
		
		return $model;	
	}
}
?>