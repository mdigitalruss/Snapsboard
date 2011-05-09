<?
	/* Homepage view */
	
	global $framework,$config;
?>
<div class="col-60">
	<div class="boxout">
		<div class="head">Recently updated albums</div>
		<div class="cols">
		<?
			foreach($model['rs_recent'] as $thread)
			{
				echo'<div class="col-25 txt-c"><a href="/album/view/'. $thread['_id'] .'">
						<img class="dropshad-5" src="'. $config['images_url'] . $thread['thumbref'].'/h:75" style=" margin:5px;"/></a>
						<br/><span style="font-size:12px;">'. $framework->util_Truncate($thread['name'],20) .'<br/>Updated '. date("d/m H:i", $thread['updated']) .'</span></div>';
			}
		?>
		</div>
	</div>
	
	<div class="boxout">
		<div class="head">New albums <a href="/album/listall/" style="color:#ccc; font-size:12px;">View more</a></div>
		<div class="cols">
		<?
			foreach($model['rs_new'] as $thread)
			{
				echo '<div class="col-25 txt-c"><a href="/album/view/'. $thread['_id'] .'">
							<img class="dropshad-5" src="'. $config['images_url'] . $thread['thumbref'].'/h:75" style="margin:5px;"/></a>
							<br/><span style="font-size:12px;">'. $framework->util_Truncate($thread['name'],20) .'<br/>by <a href="/members/view/'. $thread['owner']['id'] .'">'. $thread['owner']['name'] .'</a></span></div>';
			}
		?>
		</div>
	</div>
	
	<div class="boxout">
		<div class="head">Latest Images</div>
		<div class="cols">
		<?
			foreach($model['rs_new_img'] as $image)
			{
				echo '<div class="col-25 txt-c"><a href="/album/view/'. $image['thread']['id'] .'#'.$image['imageref'].'">
							<img class="dropshad-5" src="'. $config['images_url'] . $image['imageref'].'/h:75" style="margin:5px;"/></a>
							<br/><span style="font-size:12px;">'. $framework->util_Truncate($image['name'],20) .'<br/>By <a href="/members/view/'. $image['owner']['id'] .'">'. $image['owner']['name'] .'</a></span></div>';
			}
		?>
		</div>
	</div>
</div>
<div class="col-40">
	<div class="boxout">
	<? 
		if($framework->user['isMember']){
	?>
		
		<div class="head">Hey there, <?=$framework->user['username']?>!</div>
		<div class="body">
			<p>As a member you can:</p>
			<p>&raquo; <a href="/album/add/">Add an album</a></p>
			<p>&raquo; <a href="/inbox/">View my Inbox</a></p>
			<p>&raquo; <a href="/members/me/">Edit my Account</a></p>
		</div>
		<br/>
	<?
		} else {
	?>
		<div class="head">Y U NO REGISTER?</div>
		<div class="body">
			<p>Register now for full access: comment as much as you like, start private and public albums, and upload as many photos as you want. All for free!</p>
			<p>&raquo; <a href="/members/register/">Register!</a></p>
			<p>&raquo; <a href="/members/login/">I already registered</a></p>
		</div>
		<br/>	
	<?
		}
	?>
		<div class="head">Latest Comments</div>
		<div class="body">
	<?
		foreach($model['rs_comments'] as $comment)
		{
			echo'<p>Re: <a href="/album/view/'. $comment['thread']['id'] .'">'. $framework->util_Truncate($comment['thread']['name'],20) .'</a> by <a href="/members/view/'.$comment['owner']['id'].'">'.$comment['owner']['name'].'</a> 
							at '. date("d/m H:i", $comment['time']) .'</p>';
		}
	?>
		</div>
	</div>
</div>
<p class="clear"></p>