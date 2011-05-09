<div class="boxout">
	<div class="head">Edit Album</div>
	<div class="body">
	
		<p>Here you edit your album.</p>
		
		<form action="/album/edit/<?=$model['threadID']?>" method="post" enctype="multipart/form-data">
		
			<p><label for="g_name">Album Title</label><input type="text" name="g_name" id="g_name" value="<?=$model['name']?>"/></p>

			<p><label for="g_desc">Album Description </label><textarea name="g_desc" id="g_desc"><?=$model['info']?></textarea></p>
			
			<p><label for="g_image">Album Cover (png/jpg, max 5mb)</label><input type="file" name="g_image" id="g_image"> <span style="padding-left:4px; line-height:24px; font-size:12px;">Leave blank to keep the same</span></p>
			
			<p><label for="r_name">Allow anyone to upload to this album?</label> <input type="checkbox" name="g_public" id="g_public" style="width:20px;" value="y"/> <span style="padding-left:4px; line-height:24px; font-size:12px;">Enable Public uploads</span></p>

			<p class="txt-l"><label></label><input type="submit" class="submit" value="Update Album"/></p>
		</form>
	</div>
</div>