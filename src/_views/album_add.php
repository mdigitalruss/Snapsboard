<div class="boxout">
	<div class="head">Add Album</div>
	<div class="body">
	
		<? if(isset($model['error'])){ echo $model['error']; } ?>
		<p>Here you can create an album. You can specify the title, description and choose if other people can upload to your album. You also need to upload the album's cover image, which will stay with the album forever.</p>
		
		<form action="/album/add/" method="post" enctype="multipart/form-data">
		
			<p><label for="g_name">Album Title</label><input type="text" name="g_name" id="g_name" <?=((isset($_POST['g_name']))? 'value="'.$_POST['g_name'].'"' : '')?>/></p>

			<p><label for="g_desc">Album Description </label><textarea name="g_desc" id="g_desc"><?=((isset($_POST['g_desc']))? $_POST['g_desc'] : '')?></textarea></p>
			
			<p><label for="g_image">Album Cover (png/jpg, max 5mb)</label><input type="file" name="g_image" id="g_image"></p>
			
			<p><label for="r_name">Allow anyone to upload to this album?</label> <input type="checkbox" name="g_public" id="g_public" style="width:20px;" value="y"/> <span style="padding-left:4px; line-height:24px; font-size:12px;">Enable Public uploads</span></p>

			<p class="txt-l"><label></label><input type="submit" class="submit" value="Add album"/></p>
		</form>
	</div>
</div>