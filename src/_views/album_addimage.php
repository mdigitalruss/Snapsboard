<div class="boxout">
	<div class="head">Add Images</div>
	<div class="body">
	
		<? if(isset($model['error'])){ echo $model['error']; } ?>
		<p>Here you can add 1 - 5 images to the album. If you don't specify a name for the image, the image will take the album's name automatically.</p>
		
		<form action="/album/addimage/<?=$model['threadID'];?>" method="post" enctype="multipart/form-data">
			
			<div class="cell" style="width:250px;"><p>Image name</p></div>
			<div class="cell" style="width:300px;"><p>Image File</p></div>
			
			<div class="cell clear" style="width:250px;"><p><input type="text" name="i_name_1" id="i_name_1" style="width:200px; height:14px; font-size:12px;"/></p></div>
			<div class="cell" style="width:300px;"><p><input type="file" name="i_image_1" id="i_image_1" /></p></div>
			
			<div class="cell clear" style="width:250px;"><p><input type="text" name="i_name_2" id="i_name_2" style="width:200px; height:14px; font-size:12px;"/></p></div>
			<div class="cell" style="width:300px;"><p><input type="file" name="i_image_2" id="i_image_2" /></p></div>
			
			<div class="cell clear" style="width:250px;"><p><input type="text" name="i_name_3" id="i_name_3" style="width:200px; height:14px; font-size:12px;"/></p></div>
			<div class="cell" style="width:300px;"><p><input type="file" name="i_image_3" id="i_image_3" /></p></div>
			
			<div class="cell clear" style="width:250px; "><p><input type="text" name="i_name_4" id="i_name_4" style="width:200px; height:14px; font-size:12px;"/></p></div>
			<div class="cell" style="width:300px;"><p><input type="file" name="i_image_4" id="i_image_4" /></p></div>
			
			<div class="cell clear" style="width:250px;"><p><input type="text" name="i_name_5" id="i_name_5" style="width:200px; height:14px; font-size:12px;"/></p></div>
			<div class="cell" style="width:300px; "><p><input type="file" name="i_image_5" id="i_image_5" /></p></div>

			<p class="txt-l"><label></label><input type="submit" class="submit" value="Add Images"/></p>
			
		</form>
	</div>
</div>



