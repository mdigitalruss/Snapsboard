<?php
	include("../common/config.php");
	include("../common/class.imaging.php");

	
	if(isset($_GET['i']))
	{
		$imageID = $_GET['i'];
		
		if(isset($_GET['sz'])){
			$cache_sz = $_GET['sz'];
		}
		else{
			$cache_sz = "";
		}
		
		//Check cache..
		if(file_exists('cache/'.$imageID.'-'.$cache_sz.'.jpg') && (filemtime('cache/'.$imageID.'-'.$cache_sz.'.jpg') > (time()-600)))
		{
			//ACTION: LOAD FROM CACHE
			header('Content-Type: image/jpeg');
			header("Cache-Control: max-age=3600");
			header("Content-Length: " . filesize('cache/'.$imageID.'-'.$cache_sz.'.jpg'));
			echo file_get_contents('cache/'.$imageID.'-'.$cache_sz.'.jpg');
		}
		else
		{
			//No cache exists - Pull from gridfs
			
			//Connect
			try 
			{
				//Connect to mongo!
				$m = new Mongo("mongodb://".$config['dbHost'].':'.$config['dbPort'].'/'.$config['dbName']);
			
				$db = $m->selectDB($config['dbName']);
				$db->authenticate($config['dbUser'],$config['dbPass']);
			}
			catch(MongoConnectionException $e)
			{
				//ACTION: SHOW ERROR PNG
				$img = file_get_contents("../../interface/static/error.png");
				
				//Create a GD image from the backup
				$image = new Imaging($img); 
				
				displayImage($imageID,$image,false,false);
			}
			
			//get from grid
			if($db)
			{
				$grid = $db->getGridFS();   
				
				$imageID = new MongoID($imageID);
				$image = $grid->get($imageID);
				
				//Was there a match?
				if($image)
				{
					//ACTION: PREPARE FILE FROM GRIDFS
					$image = new Imaging($image->getBytes()); 
					
					//Get watermark
					$img = $db->images->findone(array("imageref" => $imageID));
					
					$imgusr = $db->users->findone(array("_id" => New MongoID($img['owner']['id'])));
					
					$blnWatermark = isset($imgusr['watermark']);
					
					if($blnWatermark){
						$watermark = $imgusr['watermark'];
					}
					else{
						$watermark = "";
					}
					
					displayImage($imageID,$image,true,$blnWatermark,$watermark);

				}
				else
				{
					//ACTION: MISSING IMAGE
					$img = file_get_contents("../../interface/static/nophoto.png");
					
					//Create a GD image from the backup
					$image = new Imaging($img); 
					
					displayImage($imageID,$image,false,false);
				}
				
			} 
		}
	}
	
	
	function displayImage($imageID,$image,$blnCache, $blnWatermark, $watermark = "")
	{
		//Do we need to resize this image?
		if(isset($_GET['sz'])){
			$sz_wh = current(explode(":",$_GET['sz']));
			$sz_value = end(explode(":",$_GET['sz']));
			
			if($sz_wh == "h"){
				$image->resizeToHeight($sz_value);
			}
			else{
				$image->resizeToWidth($sz_value);
			}
			
			$cache_sz = $_GET['sz'];
		}
		
		if(isset($sz_value) && $sz_value > 200 && $blnWatermark)
		{
			//Watermark
			if($sz_value > 600){
				$fsize = 20;
			}
			else{
				$fsize = 14;
			}
			
			$image->watermark($watermark,$fsize);
		}
		
		if($blnCache)
		{
			$image->save('cache/'.$imageID.'-'.$cache_sz.'.jpg');
		}
		else
		{
			header("Cache-Control: no-cache, must-revalidate");
		}
		
		//Output!
		$image->output();		
	}
	


?>