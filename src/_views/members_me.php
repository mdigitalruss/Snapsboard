<?
	global $framework, $config;
?>
<div class="boxout">
	<div class="head"><p style="float:left;"><img src="<?=$framework->user['avatar_url']?>/h:35"/></p><h2 style="float:left;"><?=$framework->user['username']?></h2></div>
	<div class="body">
		<h2>Details</h2>
		
		<form action="/members/update" method="post">
			<p><label for="r_email">Email address</label><?=$framework->user['email']?></p>
			<p><label for="r_name">Display Name</label><input type="text" name="r_name" id="r_name" value="<?=$framework->user['username']?>" /></p>
			<p><label for="r_password">New password</label><input type="password" name="r_password" id="r_password"  /> <span style="padding-left:4px; line-height:24px; font-size:12px;">Leave blank to keep the same</span></p>
			<p><label for="r_passconf">Confirm new password </label><input type="password" name="r_passconf" id="r_passconf" /> <span style="padding-left:4px; line-height:24px; font-size:12px;">Leave blank to keep the same</span></p>
			<p><label for="r_watermark">My Watermark </label><input type="text" name="r_watermark" id="r_watermark" value="<?=((isset($framework->user['watermark']))? $framework->user['watermark'] : '')?>" /> <span style="padding-left:4px; line-height:24px; font-size:12px;">Leave blank if none</span></p>
			<p><label for="r_bio">About me </label><textarea name="r_bio" id="r_bio"><?=((isset($framework->user['bio']))? $framework->user['bio'] : '')?></textarea></p>
			<p class="txt-l"><label></label><input type="submit" class="submit" value="Update my details"/> <a href="/members/goodbye/<?=$framework->user['mongoRef']?>">Delete my account</a></p>
		</form>
							
		<h2>Avatar</h2>
		<form action="/members/avataradd" method="post" enctype="multipart/form-data" >
			<p><label for="r_name" class="txt-r"><img src="<?=$framework->user['avatar_url']?>/h:50" style="padding-right:10px;"/></label><input type="file" name="avatar"></p>
			<p class="txt-l"><label></label><input type="submit" class="submit" value="Upload"/> Image file (png/jpg, max 1mb)</p>
		</form>
	</div>

							
	<h2>My Albums</h2>
	<br/>
	<div class="cols">
	<? foreach($model['albums'] as $album) { ?>
		<div class="col-20 txt-c">
		<a href="/album/view/<?=$album['_id']?>">
			<img class="dropshad-5" src="<?=$config['images_url']?><?=$album['thumbref']?>/h:75"/>
		</a>
		<br/><span style="font-size:12px;"><?=$album['name']?></span><br/><br/>
		</div>
	<? } ?>
	</div>
	<p class="clear"><a href="/album/add/">Add new album</a></p>
	
	<h2>My Likes</h2>
	<br/>
	<div class="cols">
	<? foreach($model['likes'] as $image) { ?>
		<div class="col-20 txt-c">
		<a href="/album/view/<?=$image['thread']['id']?>#<?=$image['imageref']?>">
			<img class="dropshad-5" src="<?=$config['images_url']?><?=$image['imageref']?>/h:75"/>
		</a>
		<br/><span style="font-size:12px;"><?=$image['name']?> by <a href="/members/view/<?=$image['owner']['id']?>"><?=$image['owner']['name']?></a> 
		<br/>
		<a href="/album/view/<?=$image['thread']['id']?>">View Thread</a> - <a href="/members/unlike/<?=$image['_id']?>">Unlike</a></span><br/><br/>
		</div>
	<? } ?>
	</div>
			
</div>