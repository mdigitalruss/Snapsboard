<?
	/* Homepage view */
	
	global $framework, $config;
?>
<div class="col-20">
	<div class="boxout">
		<div class="head">Bonjour!</div>
		<div class="body cols">	
			<p>Welcome to Farmsnaps, a great place to share farming and countryside pictures.</p>
			<br/>
			<div class="col-50 txt-c">
				<iframe src="http://www.facebook.com/plugins/like.php?href=farmsnaps.com&amp;layout=box_count&amp;show_faces=true&amp;width=60&amp;action=like&amp;font=segoe+ui&amp;colorscheme=light&amp;height=65" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:60px; height:65px; margin:auto;" allowTransparency="true"></iframe>
			</div>
			<div class="col-50 txt-c">
				<a href="http://twitter.com/share" class="twitter-share-button" data-count="vertical" data-via="Razor_Rusty">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
			</div>
		</div>
		<br/>
		<div class="head">FarmSnapsStats</div>
		<div class="body">
			<p>Members: <? echo $framework->db->users->count(); ?></p>
			<p>Images: <? echo $framework->db->images->count(); ?></p>
			<p>Comments: <? echo $framework->db->comments->count(); ?></p>
			<p>Albums: <? echo $framework->db->threads->count(); ?></p>
		</div>
		<br/>
	<? 
		if($framework->user['isMember']){
	?>
		<div class="body">
			<p>As a member you can:</p>
			<p>&raquo; <a href="/album/add/">Add an album</a></p>
			<p>&raquo; <a href="/inbox/">View my Inbox</a></p>
			<p>&raquo; <a href="/members/me/">Edit my Account</a></p>
		</div>
		<br/>
		
		<div class="body">
			<p>Bug with the site? Comment? Suggestion? Rant? Rage? Feel free to send us a message</p>
			<p>&raquo; <a href="/inbox/send/4d84f42b1158e403d8000009">Message the Admin</a></p>
		</div>
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
		<br/>
		<div class="txt-c"><a href="http://www.mongodb.org/"><img src="/interface/images/PoweredMongoDBblue66.png"/></a></div>
	</div>
</div>
<div class="col-80" style="margin-left:20%;">

	<div class="boxout"><div class="head">Latest Images <a href="/album/recent/" style="color:#ccc; font-size:12px;">View more</a></div></div>
	<div class="cols">
	<?
		if(isset($model['rs_new_img']))
		{
			
			foreach($model['rs_new_img'] as $image)
			{
				echo '<div class="col-20 txt-c"><a href="/album/view/'. $image['thread']['id'] .'#'.$image['imageref'].'">
							<img class="dropshad-5" src="'. $config['images_url'] . $image['imageref'].'/h:75" style="margin:5px;"/></a>
							<br/><span style="font-size:12px;">'. $framework->util_Truncate($image['name'],20) .'<br/>By <a href="/members/view/'. $image['owner']['id'] .'">'. $image['owner']['name'] .'</a></span></div>';
			}
		}
	?>
	</div>
	
	<div class="boxout">
	<div class="head">New albums <a href="/album/listall/" style="color:#ccc; font-size:12px;">View more</a></div></div>
	<div class="cols">
	<?
		if(isset($model['rs_new']))
		{
			
			foreach($model['rs_new'] as $thread)
			{
				echo '<div class="col-50 txt-c"><a href="/album/view/'. $thread['_id'] .'">
							<img class="dropshad-5" src="'. $config['images_url'] . $thread['thumbref'].'/w:350" style="margin:5px;"/></a>
							<br/>'. $framework->util_Truncate($thread['name'],20) .' by <a href="/members/view/'. $thread['owner']['id'] .'">'. $thread['owner']['name'] .'</a></div>';
			}
		}
	?>
	</div>
	
	<div class="boxout"><div class="head">Recently active albums <a href="/album/recent/" style="color:#ccc; font-size:12px;">View more</a></div></div>
	<div class="cols">
	<?

		if(isset($model['rs_recent']))
		{
			
			foreach($model['rs_recent'] as $thread)
			{
				echo'<div class="col-20 txt-c"><a href="/album/view/'. $thread['_id'] .'">
						<img class="dropshad-5" src="'. $config['images_url'] . $thread['thumbref'].'/h:75" style=" margin:5px;"/></a>
						<br/><span style="font-size:12px;">'. $framework->util_Truncate($thread['name'],20) .'<br/>Updated '. date("d/m H:i", $thread['updated']) .'</span></div>';
			}
		}
	
	?>
	</div>
	

</div>
<p class="clear"></p>