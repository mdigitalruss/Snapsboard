<div class="boxout">
	<div class="head">Register</div>
	<div class="body">
		<div style="width:565px; margin:auto"> 
			<p>It's quick and easy to register, just fill in the boxes below:</p>
			<br/>
			<? if(isset($model['error_message'])){ echo $model['error_message']; } ?>
			
			<form action="/members/register/" method="post">
			
				<p><label for="r_email">Email address</label><input type="text" name="r_email" id="r_email" value="<? if(isset($_POST['r_email'])){ echo $_POST['r_email']; } ?>" /></p>
				<? if(isset($model['err_email'])){ echo $model['err_email']; } ?>
				
				<p><label for="r_password">Password</label><input type="password" name="r_password" id="r_password" /></p>
				<? if(isset($model['err_password'])){ echo $model['err_password']; } ?>
				
				<p><label for="r_passconf">Password (confirm)</label><input type="password" name="r_passconf" id="r_passconf" /></p>
				<? if(isset($model['err_passconf'])){ echo $model['err_passconf']; } ?>
				
				<p><label for="r_name">Display name</label><input type="text" name="r_name" id="r_name" value="<? if(isset($_POST['r_name'])){ echo $_POST['r_name']; } ?>" /></p>
				<? if(isset($model['err_name'])){ echo $model['err_name']; } ?>
				
				<p class="txt-c"><input type="submit" class="submit" value="Register"/></p>
				
			</form>
		</div>
	</div>
</div>