<?
	global $framework, $config;
	$i = 0;
?>
<div class = "boxout">
	<div class = "head">
		<p style="float:left;"><img src="<?=$config['images_url']?><?=((isset($model['user']['avatar']))? $model['user']['avatar'] : 'noavatar')?>/h:35"/></p>
		<h2 style="float:left;"><?=$model['user']['username']?></h2>
	</div>
	<div class = "body">
	
		<?if(isset($model['user']['IsAdmin'])) { ?>
			<p class="txt-ok">This user is an administrator of FarmSnaps.</p>
		<? } ?>
		
		<?if(isset($model['user']['bio'])) { ?>
			<h2>About me</h2>
			<p><?=nl2br($model['user']['bio'])?></p>
		<? } ?>
		

	
		<h2>Actions</h2>
		
		<? if($model['user']['_id'] == $framework->user['mongoRef']){ ?>
			<p class="txt-ok">This is your profile. you can edit your profile by <a href="/members/me/">clicking here</a></p>
		<? } else { ?>
			<p><a href="/inbox/send/<?=$model['user']['_id']?>">Send user a Message</a></p>
		<? } ?>
		
		<?if(isset($framework->user['isAdmin']) && $framework->user['isAdmin'] == true) { ?>
			<p><a href="/members/goodbye/<?=$model['user']['_id']?>">Delete this account</a></p>
		<? } ?>

	</div>	

	<h2>Albums</h2>
	<br/>
	<div class="cols">
	<? foreach($model['albums'] as $album) { ?>
		
		<div class="col-20 txt-c">
		<a href="/album/view/<?=$album['_id']?>">
			<img class="dropshad-5" src="<?=$config['images_url']?><?=$album['thumbref']?>/h:75"/>
		</a>
		<br/><span style="font-size:12px;"><?=$album['name']?></span><br/><br/>
		</div>
		<? $i++ ?>
	<? } ?>
	</div>

	<? if($i == 0) { ?>
		<p>This user has no albums :( </p>
	<? } ?>
	<br/>
	
	<h2>Likes</h2>
	<br/>
	<div class="cols">
	<? foreach($model['likes'] as $image) { ?>
		<div class="col-20 txt-c">
		<a href="/album/view/<?=$image['thread']['id']?>#<?=$image['imageref']?>">
			<img class="dropshad-5" src="<?=$config['images_url']?><?=$image['imageref']?>/h:75"/>
		</a>
		<br/><span style="font-size:12px;"><?=$image['name']?> by <a href="/members/view/<?=$image['owner']['id']?>"><?=$image['owner']['name']?></a></span><br/><br/>
		</div>
	<? } ?>
	</div>

</div>	