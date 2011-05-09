<div class="boxout">
	<div class="head"><? if(isset($model['title'])){ echo $model['title']; } ?></div>
	<div class="body">
		<p><? if(isset($model['message'])){ echo $model['message']; } ?></p>
		
		<? if(isset($model['link_href'])){ ?>
		<p><a href="<?=$model['link_href']?>"><? if(isset($model['link_title'])){ echo $model['link_title']; } ?></a></p>
		<? } ?>
	</div>
</div>