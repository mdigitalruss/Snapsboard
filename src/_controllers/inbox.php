<?

/* Inbox system - controller */

Class inbox
{

	
	/* _default: required, default action if no subaction is specified */
	public function _default()
	{
		return $this->received();
	}
	
	public function received()
	{
		global $framework;
		$model = Array('view' => 'inbox_list');
		
		$model['title'] = 'Received Messages <a href="/inbox/sent/"style="font-size:12px; color:#ccc;">View sent messages</a>';
		$model['msg_view'] = "received";
		
		//get messages
		$model['messages'] = $framework->db->messages->find(array("to.id" => $framework->user['mongoRef']))->sort(array('time' => -1));
		
		return $model;
	}
		
	//View sent messages
	public function sent()
	{
		global $framework;
		$model = Array('view' => 'inbox_list');
		
		$model['title'] = 'Sent messages <a href="/inbox/" style="font-size:12px; color:#ccc;">View received Messages</a>';
		$model['msg_view'] = "sent";
		
		//get messages
		$model['messages'] = $framework->db->messages->find(array("from.id" => $framework->user['mongoRef']))->sort(array('time' => -1));
		
		return $model;
	}
	
	//view a message
	public function view($messageID = "")
	{
		global $framework;
		
		if($messageID != "")
		{
			//Get message details
			$message = $framework->db->messages->findOne(array("_id" => new MongoID($messageID)));
			
			if($message && ($message['to']['id'] == $framework->user['mongoRef'] || $message['from']['id'] == $framework->user['mongoRef']))
			{
				$model = Array('view' => 'inbox_view');
				$model['message'] = $message;
				
				if($message['to']['id'] == $framework->user['mongoRef'] && $message['read'] == false)
				{
					//Update user's details
					$framework->db->messages->update(array("_id" => new MongoID($messageID)), array('$set' => array('read' => true)));
				}
			}
			else
			{	
				$model = Array('view' => 'generic_message');
				$model['title'] = "Error";
				$model['message'] = "Message does not exist";
			}

		}
		else
		{
			$model = Array('view' => 'generic_message');
			$model['title'] = "Error";
			$model['message'] = "Message does not exist";
		}
		
		return $model;
	}
	
	//Send a message
	public function reply($message = "")
	{
		global $framework;

		if($message != "")
		{
			//Get TO name
			
			$message = $framework->db->messages->findOne(array("_id" => new MongoID($message)));
			
			if($message)
			{
				$model = Array('view' => 'inbox_reply');
				$model['message'] = $message;
			}
			else
			{
				$model = Array('view' => 'generic_message');
				$model['title'] = "Error";
				$model['message'] = "Message does not exist";
			}
		}
		else
		{
			$model = Array('view' => 'generic_message');
			$model['title'] = "Error";
			$model['message'] = "Message does not exist";
		}
		
		return $model;
	}
	
	//Send a message
	public function send($userid = "")
	{
		global $framework;
		
		if(isset($_POST['m_body']) && $_POST['m_body'] != "" && 
			isset($_POST['m_subject']))
		{
			$m_subject = $_POST['m_subject'];
			$m_body = $_POST['m_body'];
			
			//Check
			if($m_subject == "") { $m_subject = "(No subject)"; }
				
			//Clean
			$m_body = $framework->clean($m_body);
			$m_subject = $framework->clean($m_subject);
			
			//Insert
			$message = Array("to" => Array('id' => new MongoID($userid), 'name' => $_POST['m_display_to']),
							 "from" =>  Array('id' => $framework->user['mongoRef'], 'name' => $framework->user['username']),
							 "subject" => $m_subject,
							 "body" => $m_body,
							 "time" => time(),
							 "read" => false);
							 
			$framework->db->messages->insert($message);
			
			$model = Array('view' => 'generic_message');
			$model['title'] = "Sent";
			$model['message'] = "Message Sent!";
			$model['link_href'] = "/inbox/";
			$model['link_title'] = "View Inbox";
		}
		else
		{
			if($userid != "")
			{
				$toUser = $framework->db->users->findOne(array("_id" => new MongoID($userid)));
				
				if($toUser)
				{
					//Get TO name
					$model = Array('view' => 'inbox_send');
					$model['user'] = $toUser;
				}
				else
				{
					$model = Array('view' => 'generic_message');
					$model['title'] = "Error";
					$model['message'] = "User does not exist";
				}
			}
			else
			{
				$model = Array('view' => 'generic_message');
				$model['title'] = "Error";
				$model['message'] = "User does not exist";
			}
		}
		
		return $model;
	}


}

?>