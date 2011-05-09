<? global $framework, $config; ?>
<div class="cols">
<div class="col-60 txt-c">

	<div id="gallery_w" style="display:none;"><img src="/interface/images/ajax-loader.gif"/> Loading, Please wait</div>
	
	<div id="gallery" style="padding-top:8px; background:#555; margin:10px;">
		<a href="<?=$config['images_url']?><?=$model['album_thread']['thumbref']?>/w:1024" id="gallery_lnk" title="<?=$model['album_thread']['name']?> by <?=$model['album_owner']['username']?>">
			<img id="gallery_i" src="<?=$config['images_url']?><?=$model['album_thread']['thumbref']?>/w:560"/>
		</a>
		<script type="text/javascript"> $(function() { $('#gallery_lnk').lightBox({fixedNavigation:true}); }); </script>
		
		<div id="gallery_d">"<?=$model['album_thread']['name']?>" by <?=$model['album_owner']['username']?> </div>
		<? 
			/* get # of likes */ 
			$likes = $framework->db->likes->find(array("imageref" => $model['album_thread']['thumbref']))->count();
		?>	
		<div id="gallery_f"><span id="gallery_nl"><?=$likes?></span> people like this <a href="/members/addlike/<?=$model['album_thread']['thumbref']?>" id="gallery_l">Like</a></div>
		
		<div style="clear:both;"></div>

	</div>
	
	
	<div class="txt-l"><br/><p style="padding-left:10px;">Images in this album:</p></div>
	<div id="gallery_t">
		<div class="cols">
			
		<? foreach($model['album_images'] as $image) { ?>
				<div class="col-25"><a href="#<?=$image['imageref']?>"><img class="dropshad-5" src="<?=$config['images_url']?><?=$image['imageref']?>/h:75" style="margin:5px;"/></a>
				
				<? if($image['imageref'] == $model['album_thread']['thumbref']) { ?>
				
					<? if($framework->user['isMember'] && ($model['album_thread']['owner']['id'] == $framework->user['mongoRef'] || (isset($framework->user['isAdmin']) && $framework->user['isAdmin'] == true))) { ?>
						<br/><span style="font-size:12px;">Album Cover</span>
					<? } ?>
				
				<? } else { ?>
				
					<? if($framework->user['isMember'] && 
							($image['owner']['id'] == $framework->user['mongoRef'] || 
							$model['album_thread']['owner']['id'] == $framework->user['mongoRef'] || 
							(isset($framework->user['isAdmin']) && $framework->user['isAdmin'] == true))) { ?>
						<br/><a href="/album/removeimage/<?=$image['_id']?>" style="font-size:12px; ">Remove image</a>
					<? } ?>
				
				<? } ?>
				</div>
				
				<script type="text/javascript"> 
					<? if($image['name'] == "") { ?>
					imageTitles["<?=$image['imageref']?>"] = "<?=$model['album_thread']['name']?>";
					<? } else { ?>
					imageTitles["<?=$image['imageref']?>"] = "<?=$image['name']?>";
					<? } ?>
					imageOwners["<?=$image['imageref']?>"] = "<?=$image['owner']['name']?>";
					<? 
						/* get # of likes */ 
						$likes = $framework->db->likes->find(array("imageref" => $image['imageref']))->count();
					?>
					imageLikes["<?=$image['imageref']?>"] = "<?=$likes?>";
				</script>
		<? } ?>
		</div>
		
	<? if($framework->isUser($model['album_thread']['owner']['id']) || ($model['album_thread']['is_public'] && $framework->user['isMember'])) { ?>
		<div class="txt-l clear"><br/><p><a href="/album/addimage/<?=$model['album_thread']['_id']?>">&raquo; Upload to this album</a></p></div>
	<? } ?>
	</div>		
</div>
<div class="col-40">	
	<div class="boxout">
		<div class="head cols">
			<div class="col-20"><img src="<?=$config['images_url']?><?=$model['album_thread']['thumbref']?>/w:65" style="padding:3px;" /></div>
			<div class="col-80"><h2 style="line-height:30px;"><?=$model['album_thread']['name']?></h2></div>
		</div>	
		<div class="body cols">
			<p><?=$model['album_thread']['info']?></p>
			<p style="font-size:12px">Album added by <a href="/members/view/<?=$model['album_owner']['_id']?>"><?=$model['album_owner']['username']?></a>, viewed <?=$model['album_views']?> times</p>
		</div>
		<div class="body" style="background:#333;">	
			<? if($model['album_thread']['is_public']) { ?>
				<p style="color:#8ae234; font-size:12px;">This album is public. Anyone can upload to it.</p>
			<? } elseif($framework->isUser($model['album_thread']['owner']['id'])) { ?>
				<p style="color:#8ae234; font-size:12px;">This album belongs to you.</p>
			<? } else { ?>
				<p style="color:#ef2929; font-size:12px;">This album is closed. Only the owner can upload to it.</p>
			<? } ?>
			<? if($framework->isUser($model['album_owner']['_id']) || ($model['album_thread']['is_public'] && $framework->user['isMember'])) { ?>
				<p style="color:#8ae234; font-size:12px;"><a href="/album/addimage/<?=$model['album_thread']['_id']?>">&raquo; Upload to this album</a></p>
			<? } ?>
			
			<? if($framework->isUser($model['album_thread']['owner']['id']) || (isset($framework->user['isAdmin']) && $framework->user['isAdmin'] == true)) { ?>
				<p style="color:#8ae234; font-size:12px;"><a href="/album/edit/<?=$model['album_thread']['_id']?>">&raquo; Edit album</a></p>
				<p style="color:#8ae234; font-size:12px;"><a href="/album/remove/<?=$model['album_thread']['_id']?>">&raquo; Remove album</a></p>
			<? } ?>
							
		</div>
	
		<br/><h2>Comments:</h2>
			
		<? foreach($model['album_comments'] as $comment) { ?>	
			<? $comment_owner = $framework->db->users->findOne(array("_id" => $comment['owner']['id'])); ?>
			<? $userid = $comment['owner']['id']; ?>
					
			<div class="clear">
				<div class="col-20">
					<img src="<?=$config['images_url']?><?=((isset($comment_owner['avatar']))? $comment_owner['avatar'] : 'noavatar')?>/w:65" style="padding:3px;" />
				</div>
				<div class="col-80">
					<p><a href="/members/view/<?=$comment['owner']['id']?>"><?=$comment['owner']['name']?></a> says:</p>
					<div class="comment"><?=$comment['text']?></div>
					<? if($framework->user['isMember'] && ($comment['owner']['id'] == $framework->user['mongoRef'] || (isset($framework->user['isAdmin']) && $framework->user['isAdmin'] == true))) { ?>
					<p class="txt-r"><a href="/album/removecomment/<?=$comment['_id']?>" style="font-size:12px;">Delete</a></p>
					<? } ?>
				</div>
			</div>
		<? } ?>
		
		<? if($framework->user['isMember']) { ?>
			<? if(isset($userid) && ($userid  == $framework->user['mongoRef'])) { ?>
			<p>You were the last person to comment, so you may not comment until someone else comments.</p>
			<? } else { ?>
				<div class="clear">
					<div class="col-20">
						<img src="<?=$framework->user['avatar_url']?>/w:65" style="padding:3px;" />
					</div>
					<div class="col-80">
						<p>Add a comment:</p>
						<form action="/album/addcomment/<?=$model['album_thread']['_id']?>" method="post">
							<div class="txt-l"><textarea class="comment"  name="c_body" id="c_body"></textarea></div>
							<div class="txt-r"><input type="submit" class="submit" value="Add Comment"/></div>
						</form>
					</div>
				</div>
				<br/>
			<? } ?>
		<? } else { ?>
			<p><a href="/members/register/">Sign up</a> or <a href="/members/login">log in</a> to comment on this album.</p>
		<? } ?>
		</div>
	</div>
</div>
<script type="text/javascript">galleryHashListener();</script>
</div>