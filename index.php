<?php
	$startLoadTime = microtime(true);
	
	/* Snapsboard v1.0 */
	include("./src/common/class.framework.php");
	
	$framework = new framework();
?>
<!doctype html>
<html>
<head>
	<title><?=$config['site_name'];?></title>
	<meta name="description" content="Snapsboard is an image sharing board." />
	<meta name="keywords" content="snaps,photos,board,forum,image,sharing" />
	
	<link rel="icon" href="/favicon.ico" type="image/x-icon"/> 
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
	
	<link rel="stylesheet" type="text/css" href="/interface/fonts/fonts.css" /> 
	<link rel="stylesheet" type="text/css" href="/interface/style.css?v2" /> 
	
	<script type="text/javascript" src="/interface/scripts/jquery.js"></script>
	<script type="text/javascript" src="/interface/scripts/photodb.js?v2"></script>

	<script type="text/javascript" src="/interface/scripts/jquery.lightbox-0.5.min.js?"></script>
	<link rel="stylesheet" type="text/css" href="/interface/jquery.lightbox-0.5.css"/>
</head>

<body>
	<div id="header">
		<div class="wrap cols">
			<div class="col-50">
				<img src="/interface/images/logo.png"/>
			</div>
			<div class="col-50 txt-r" id="userArea">
				
				<? 
					if(!$framework->user['isMember'])
					{
						echo '<p>Hey there Guest!</p><p>Why not <a href="/members/register">register</a> or <a href="/members/login">log in</a>?</p>';
					}
					else
					{
						echo '<p>' . $framework->user['username'].' <img src="'. $framework->user['avatar_url'] .'/h:50"/></p>';
					}					
				?>
				
			</div>
		</div>
	</div>
	
	<div id="nav">
		<div class="wrap">
			<a href="/">Home</a>
			<a href="/album/recent">Albums</a>
			<a href="/members/recent">Members</a>
		<?
			if($framework->user['isMember'])
			{
				$inboxcount = $framework->db->messages->find(array("to.id" => $framework->user['mongoRef'],'read' => false))->count();
				?>
					<a href="/members/logout" class="r">Log out</a>
					<a href="/members/me" class="r">My Account</a>
					<a href="/inbox/" class="r">Inbox <? echo (($inboxcount > 0)? '<span style="color:#a40000"><blink>( '.$inboxcount.' New )</blink></a>' : ''); ?></a>
				<?
			}
		?>
		</div>
	</div>
		
	<div class="wrap">
		<div id="content" class="cols">
		<?
			//Handle the requested action
			$framework->handleAction();
		?>
		</div>
	</div>
	
	<div id="footer"><br/>&copy; 2011 <?=$config['site_copyright'];?><br/>
		Powered by <a href="https://github.com/razorrusty/snapsboard/">SnapsBoard</a> - <a href="http://peterson-web.co.uk">&copy; Russell Peterson</a>
		<br/>Page generated in <? $loadtime = microtime(true)-$startLoadTime; echo(round($loadtime,3)); ?> seconds</div>
</body>
</html>