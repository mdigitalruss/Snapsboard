<? global $config; ?>
<div class="boxout">
	<div class="head">Member List</div>

	<div class="cols">
	
	<? foreach($model['users'] as $user) { ?>
		<div class="col-20 txt-c">
			<a href="/members/view/<?=$user['_id']?>">
				<img class="dropshad-5" src="<?=$config['images_url']?><?=((isset($user['avatar']))? $user['avatar'] : 'annon')?>/h:75" style="margin:5px;"/></a>
			<br/>
			<span style="font-size:12px;"><?=$user['username']?></span>
		</div>
	<? } ?>	
	
	</div><br/>
	<p class="clear"><?=$model['pagination']?></p>

</div>