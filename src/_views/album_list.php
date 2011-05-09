<?
	global $framework,$config;
?>
<div class="boxout">
	<div class="head"><?=$model['title']?></div>

	<div class="cols">
	
	<? foreach($model['albums'] as $album) { ?>
		<div class="col-20 txt-c">
			<a href="/album/view/<?=$album['_id']?>">
				<img class="dropshad-5" src="<?=$config['images_url']?><?=$album['thumbref']?>/h:75" style="margin:5px;"/></a>
			<br/>
			<span style="font-size:12px;"><?=$framework->util_Truncate($album['name'],20)?><br/>by <a href="/members/view/<?=$album['owner']['id']?>"><?=$album['owner']['name']?></a></span>
		</div>
	<? } ?>	
	
	</div>
	<br/>
	<p class="clear"><?=$model['pagination']?></p>

</div>