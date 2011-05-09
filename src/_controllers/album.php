<?
	/* album system */
	
Class album
{

	/* __construct: optional, called at init */
	public function __construct()
	{
	}
	
	/* _default: required, default action if no subaction is specified */
	public function _default()
	{
		return $this->listall();
	}
	
	public function recent()
	{
		global $framework;
		$model = Array('view' => 'album_recent');

		$model['rs_recent'] = $framework->db->threads->find()->sort(array('updated' => -1))->limit(12);
		$model['rs_new'] = $framework->db->threads->find()->sort(array('started' => -1))->limit(12);
		$model['rs_new_img'] = $framework->db->images->find()->sort(array('uploaded' => -1))->limit(12);
		$model['rs_comments'] = $framework->db->comments->find()->sort(array('time' => -1))->limit(12);
		
		return $model;
	}
	
	public function listall($page = 1)
	{
		global $framework;
		$pageSize = 20;
		
		$model = Array('view' => 'album_list');
		
		$model['title'] = 'Album list, newest albums first';
		
		$model['albums'] = $framework->db->threads->find()->sort(array('started' => -1))->limit($pageSize)->skip($pageSize*($page-1));
		
		$count =  $framework->db->threads->find()->sort(array('started' => -1))->count();
		
		$model['pagination'] = "";
		
		// previous button
		if($page > 1)
		{
			$model['pagination'] .= '<a href="/album/listall/'. ($page-1) .'">&lt; Previous</a> - ';
		}

		// Show page number
		$model['pagination'] .= 'Page '.($page);

		// Next button
		if($count > ($pageSize*$page))
		{
			$model['pagination'] .= ' - <a href="/album/listall/'. ($page+1) .'">Next &gt;</a> ';
		}
		
		return $model;	
	}
	
	public function view($threadID)
	{
		global $framework;
		
		//get thread details
		$album = $framework->db->threads->findOne(array("_id" => new MongoID($threadID)));

		if($album)
		{
			$model = Array('view' => 'album_view');
			
			$model['album_thread'] = $album;
			$model['album_owner'] = $framework->db->users->findOne(array("_id" => $album['owner']['id']));
			$model['album_images'] = $framework->db->images->find(array("thread.id" => $album['_id']))->sort(array('uploaded' => 1));
			$model['album_comments'] = $framework->db->comments->find(array("thread.id" => $album['_id']))->sort(array('time' => 1));
			$model['album_views'] = $framework->db->pagestats->find(array("id" => $threadID))->count();

			
		}
		else
		{
			$model = Array('view' => 'generic_error');
			
			$model['title'] = "Error!";
			$model['message'] = "The album you are looking for does not exist!";
			
			$model['link_href'] = "/album/recent/";
			$model['link_title'] = "Back";
		}
		
		return $model;
	}
	
	public function addcomment($threadID)
	{
		global $framework;
		
		//Comment, with photo or not
		if(!$framework->user['isMember'])
		{
			$model = Array('view' => 'generic_login_message');
		}
		else
		{
			$model = Array('view' => 'generic_message');
			$model['title'] = "Add Comment";
			
			if(isset($_POST['c_body']) && $_POST['c_body'] != "")
			{
				//get thread name
				$thread = $framework->db->threads->findOne(array("_id" => new MongoID($threadID)));
				
				//Insert comment
				$comment = Array("text" => $framework->clean($_POST['c_body']),
								"thread" => Array("name" => $thread['name'], "id" => new MongoID($threadID)),
								"owner" => Array("name" => $framework->user['username'],"id" => $framework->user['mongoRef']),
								"time" => time());
			
				// Insert into the database
				$framework->db->comments->insert($comment);
				
				 ;
				//Update thread activity time
				$framework->db->threads->update(array("_id" => new MongoID($threadID)), array('$set' => array('updated' => time())));
				
				//done!
				
				$model['message'] = "Your comment was added";
				$model['link_href'] = "/album/view/".$threadID;
				$model['link_title'] = "Continue";

			}
			else
			{
				$model['message'] = "You must write something to comment!";
				$model['link_href'] = "/album/view/".$threadID;
				$model['link_title'] = "Back";
			}
		}
		
		return $model;
	}
	
	public function edit($threadID)
	{
		global $framework;
		//Add a photo to the thread
		if(!$framework->user['isMember'])
		{
			$model = Array('view' => 'generic_login_message');
		}
		else
		{
			//Check we can add to that thread
			$thread = $framework->db->threads->findOne(array("_id" => new MongoID($threadID)));
			
			if($framework->isUser($thread['owner']['id']) || (isset($framework->user['isAdmin']) && $framework->user['isAdmin'] == true)) 
			{
				//Let's go
				if(isset($_POST['g_name']))
				{	
					//Details to update
					$details = array('name' => $framework->clean($_POST['g_name']),
									 'info' => $framework->clean($_POST['g_desc']),
									 'updated' => time(),
									 'is_public' => (isset($_POST["g_public"]) && $_POST["g_public"] == 'y'));
					
					//image?
					if(($_FILES[$image_input]["type"] == "image/jpeg") || 
					($_FILES[$image_input]["type"] == "image/jpg") || 
					($_FILES[$image_input]["type"] == "image/pjpeg") || 
					($_FILES[$image_input]["type"] == "image/png") &&
					($_FILES[$image_input]["size"] < 10485760))
					{
						//Insert image into db
						$gridFS = $framework->db->getGridFS();
						$image_id = $gridFS->storeUpload('g_image',$_FILES['g_image']['name']); //load file into MongoDB  
						
						//Array
						$image = Array("name" => $_POST['g_name'],
										"thread" => Array("name" => $thread['name'], "id" => new MongoID($threadID)),
										"owner" => Array("name" => $framework->user['username'], "id" => $framework->user['mongoRef']),
										"uploaded" => time(),
										"imageref" => new MongoID($image_id));
					
						// Insert into the database
						$framework->db->images->insert($image);		
						
						$details['thumbref'] = new MongoID($image_id);
					}
					
					//Update!
					$framework->db->threads->update(array("_id" => new MongoID($threadID)), array('$set' => $details));
					
					$model = Array('view' => 'generic_message');
					$model['title'] = "Album Updated.";
					$model['message'] = "Your album was updated.";
					$model['link_href'] = "/album/view/".$threadID;
					$model['link_title'] = "View Album";
				}
				else
				{
					$model = Array('view' => 'album_edit');
					$model['name'] = $thread['name'];
					$model['info'] = $thread['info'];
					$model['threadID'] = $thread['_id'];
				}
			}
			else
			{
				$model = Array('view' => 'generic_message');
				$model['title'] = "Error.";
				$model['message'] = "Sorry, you do not have permission to edit this album.";
				$model['link_href'] = "/album/view/".$threadID;
				$model['link_title'] = "Back";
			}
		}
		return $model;
	}

	public function addimage($threadID)
	{
		global $framework;
		//Add a photo to the thread
		if(!$framework->user['isMember'])
		{
			$model = Array('view' => 'generic_login_message');
		}
		else
		{
			//Check we can add to that thread
			$thread = $framework->db->threads->findOne(array("_id" => new MongoID($threadID)));
			
			if($framework->isUser($thread['owner']['id']) || ($thread['is_public'] && $framework->user['isMember']))
			{
				$messages = Array();
				
				if(isset($_POST['i_name_1']))
				{
					//init generic message
					$model = Array('view' => 'generic_message');
					
					//Loop through uploads
					for ($i = 1; $i <= 5; $i++) 
					{
						$image_input = "i_image_".$i;
						$image_name = "i_name_".$i;
						
						//check image
						
						if($_FILES[$image_input]["name"] == "")
						{
							$messages[$i] = "Skipping image ".$i." - nothing uploaded";
						}
						else
						{
							//Insert image info gridfs
							if(($_FILES[$image_input]["type"] == "image/jpeg") || 
							($_FILES[$image_input]["type"] == "image/jpg") || 
							($_FILES[$image_input]["type"] == "image/pjpeg") || 
							($_FILES[$image_input]["type"] == "image/png") &&
							($_FILES[$image_input]["size"] < 10485760))
							{
								//Insert image into db
								$gridFS = $framework->db->getGridFS();
								$image_id = $gridFS->storeUpload($image_input,$_FILES[$image_input]['name']); //load file into MongoDB  
								
								// image details
								if($_POST[$image_name] == ""){
									$name = $thread['name'];
								}
								else{
									$name = $_POST[$image_name];
								}
								
								$image = Array("name" => $name,
												"thread" => Array("name" => $thread['name'], "id" => new MongoID($threadID)),
												"owner" => Array("name" => $framework->user['username'], "id" => $framework->user['mongoRef']),
												"uploaded" => time(),
												"imageref" => new MongoID($image_id));
							
								// Insert into the database
								$framework->db->images->insert($image);
								
								//Update thread
								$framework->db->threads->update(array("_id" => new MongoID($threadID)), array('$set' => array('updated' => time())));
							
								$messages[$i] = "Image ". $i ." (". $name .") added OK.";
							}
							else
							{
								$messages[$i] = "Error with image ".$i.": Wrong format or too big. Must be jpg/png and less than 5mb";
							}
						}
					}
					
					//output
					$model['title'] = "Add Images";
					$model['message'] = "Results:</p><p>";
					
					for ($i = 1; $i <= 5; $i++) 
					{
						$model['message'] .= $messages[$i].'</p><p>';
					}

					$model['link_href'] = "/album/view/".$threadID;
					$model['link_title'] = "Back to thread";					
				}
				else
				{
					$model = Array('view' => 'album_addimage');
					
					$model['threadID'] = $threadID;
				}
			}
			else
			{
				$model = Array('view' => 'generic_message');
				$model['title'] = "Error.";
				$model['message'] = "Sorry, you cannot add images to this album.";
				$model['link_href'] = "/album/view/".$threadID;
				$model['link_title'] = "Back";
			}
			
			
			$this->body .=	'</div></div>';
		}
		
		return $model;
	}

	public function add()
	{
		global $framework;
		
		if(!$framework->user['isMember'])
		{
			$model = Array('view' => 'generic_login_message');
		}
		else
		{
		
			if(isset($_POST["g_name"]) && $_POST["g_name"] != "" &&
			   isset($_POST["g_desc"]) && $_POST["g_desc"] != "" &&
				isset($_FILES["g_image"]) && $_FILES["g_image"]['name'] != "")
			{
				
				if(($_FILES["g_image"]["type"] == "image/jpeg") || 
				($_FILES["g_image"]["type"] == "image/jpg") || 
				($_FILES["g_image"]["type"] == "image/pjpeg") || 
				($_FILES["g_image"]["type"] == "image/png") &&
				($_FILES["g_image"]["size"] < 10485760))
				{
					// Insert image to GridFS
					$gridFS = $framework->db->getGridFS();
					$image_id = $gridFS->storeUpload('g_image',$_FILES['g_image']['name']); //load file into MongoDB  
					
					// thread details inc thumb details
					$thread = Array("name" => $framework->clean($_POST['g_name']),
									"info" => $framework->clean($_POST['g_desc']),
									"owner" => Array("name" => $framework->user['username'],"id" => $framework->user['mongoRef']),
									"started" => time(),
									"updated" => time(),
									"thumbref" => new MongoID($image_id),
									"is_public" => (isset($_POST["g_public"]) && $_POST["g_public"] == 'y'));
				
					// Insert into the database
					$framework->db->threads->insert($thread);

					// image details
					$image = Array("name" => $_POST['g_name'],
									"thread" => Array("name" => $_POST['g_name'], "id" => $thread['_id']),
									"owner" => Array("name" => $framework->user['username'], "id" => $framework->user['mongoRef']),
									"uploaded" => time(),
									"imageref" => new MongoID($image_id));
				
					// Insert into the database
					$framework->db->images->insert($image);
					
					$model = Array('view' => 'generic_message');
					$model['title'] = "Add album.";
					$model['message'] = 'The album was sucsessfully added.</p><p><img src="/grid/images/'. $image_id .'/h:128"/>';
					$model['link_href'] = "/album/view/".$thread['_id'];
					$model['link_title'] = "View album";	
				}
				else
				{
					if($_FILES["g_image"]["type"] != "")
					{
						$model = Array('view' => 'generic_message');
						$model['title'] = "Error";
						$model['message'] = "Your image is an invalid type (".$_FILES["g_image"]["type"]." is not supported). The image must be jpg or png format, and less than 5mb in size..";
						$model['link_href'] = "/album/add/";
						$model['link_title'] = "Back";					
					}
					else
					{
						$model = Array('view' => 'generic_message');
						$model['title'] = "Error";
						$model['message'] = "Your image failed to upload, possibly because it was an invalid size. The image must be jpg or png format, and less than 5mb in size..";
						$model['link_href'] = "/album/add/";
						$model['link_title'] = "Back";
					}

				}
			}
			else
			{
				$model = Array('view' => 'album_add');
				if(isset($_POST["g_name"]))
				{
					
					$model['error'] = '<p class="txt-error">Some details are missing - please try again:</p>';
				}
			}
		}
		
		return $model;
	}

	public function removecomment($commentID)
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
				$comment = $framework->db->comments->findOne(array("_id" => new MongoID($commentID)));
				
				if($comment && ($comment['owner']['id'] == $framework->user['mongoRef'] || (isset($framework->user['isAdmin']) && $framework->user['isAdmin'] == true)))
				{
					//remove comment
					$framework->db->comments->remove(array("_id" => new MongoID($commentID)));
					
					$model = Array('view' => 'generic_message');
					$model['title'] = "Remove comment";
					$model['message'] = "Comment was deleted.";
					$model['link_href'] = "/album/view/".$comment['thread']['id'];
					$model['link_title'] = "Back to album";	
				}
				else
				{
					$model = Array('view' => 'generic_message');
					$model['title'] = "Error";
					$model['message'] = "Comment does not exist, or you do not have permission to remove it.";
					$model['link_href'] = "/";
					$model['link_title'] = "Home";					
				}
			}
			else
			{
				$model = Array('view' => 'generic_ack');
				$model['title'] = "Remove Comment";
				$model['message'] = "Are you sure you want to remove this comment? This action cannot be reversed.";
				$model['form_action'] = "/album/removecomment/".$commentID;
				$model['form_button'] = "Remove Comment";
			}
		}
		
		return $model;
	}
	
	public function removeimage($imageID)
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
				$image = $framework->db->images->findOne(array("_id" => new MongoID($imageID)));
				
				if($image){
					$thread = $framework->db->images->findOne(array("_id" => new MongoID($image['thread']['id'])));
				}
				
				if($image && 
					($image['owner']['id'] == $framework->user['mongoRef'] || 
					 $thread['owner']['id'] == $framework->user['mongoRef'] || 
					 (isset($framework->user['isAdmin']) && $framework->user['isAdmin'] == true)))
				{
					//remove likes
					$framework->db->likes->remove(array("imageref" => new MongoID($image['imageref'])));
					
					//remove image
					$framework->db->images->remove(array("_id" => new MongoID($imageID)));
					
					
					$model = Array('view' => 'generic_message');
					$model['title'] = "Remove Image";
					$model['message'] = "Image was deleted.";
					$model['link_href'] = "/album/view/".$image['thread']['id'];
					$model['link_title'] = "Back to album";	
				}
				else
				{
					$model = Array('view' => 'generic_message');
					$model['title'] = "Error";
					$model['message'] = "Image does not exist, or you do not have permission to remove it.";
					$model['link_href'] = "/";
					$model['link_title'] = "Home";					
				}
			}
			else
			{
				$model = Array('view' => 'generic_ack');
				$model['title'] = "Remove Image";
				$model['message'] = "Are you sure you want to remove this image? This action cannot be reversed.";
				$model['form_action'] = "/album/removeimage/".$imageID;
				$model['form_button'] = "Remove Image";
			}
		}
		
		return $model;
	}
	
	public function remove($threadID)
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
				$thread = $framework->db->threads->findOne(array("_id" => new MongoID($threadID)));
				
				if($thread && ($thread['owner']['id'] == $framework->user['mongoRef'] || (isset($framework->user['isAdmin']) && $framework->user['isAdmin'] == true)))
				{
					//remove thread
					$framework->db->threads->remove(array("_id" => new MongoID($threadID)));
					
					//remove images
					$framework->db->images->remove(array("thread.id" => new MongoID($threadID)));
					
					//remove likes
					$framework->db->likes->remove(array("thread.id" => new MongoID($threadID)));
					
					//remove comments
					$framework->db->comments->remove(array("thread.id" => new MongoID($threadID)));
					
					$model = Array('view' => 'generic_message');
					$model['title'] = "Remove Album";
					$model['message'] = "Album was deleted.";
					$model['link_href'] = "/";
					$model['link_title'] = "Home";	
				}
				else
				{
					$model = Array('view' => 'generic_message');
					$model['title'] = "Error";
					$model['message'] = "Album does not exist, or you do not have permission to remove it.";
					$model['link_href'] = "/";
					$model['link_title'] = "Home";					
				}
			}
			else
			{
				$model = Array('view' => 'generic_ack');
				$model['title'] = "Remove Album";
				$model['message'] = "Are you sure you want to remove this Album?</p><p>Warning! ALL images and comments in this album will be deleted! This action CANNOT be reversed.";
				$model['form_action'] = "/album/remove/".$threadID;
				$model['form_button'] = "Remove Image";
			}
		}
		
		return $model;
	}
}
?>