<div class="boxout">
	<div class="head"><? if(isset($model['title'])){ echo $model['title']; } ?></div>
	<div class="body">
		<form action="<?=$model['form_action']?>" method="post">
		<input type="hidden" name="ACK" value="true" />
		<p><? if(isset($model['message'])){ echo $model['message']; } ?></p>
		<p><input type="submit" class="submit" value="<?=$model['form_button']?>"/></p>
		</form>
	</div>
</div> 