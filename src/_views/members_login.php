<div class="boxout">
	<div class="head">Log In</div>
	<div class="body">
		<div style="width:565px; margin:auto"><p>You can log in using the form below:</p><br/>
			<form action="/members/login/" method="post">
				<p><label for="l_email">Email address</label><input type="text" name="l_email" id="l_email" value="<? if(isset($_POST['l_email'])){ echo $_POST['l_email']; } ?>" /></p>
				<? if(isset($model['err_email'])){ echo $model['err_email']; } ?>
				<p><label for="l_password">Password</label><input type="password" name="l_password" id="l_password" /></p>
				<? if(isset($model['err_password'])){ echo $model['err_password']; } ?>
				<p><label for="l_forever"></label><input type="checkbox" name="l_forever" id="l_forever" style="width:10px;" value="y"/><span style="padding-left:4px; line-height:24px; font-size:12px;">Remember me</span></p>
				<p class="txt-c"><input type="submit" class="submit" value="Log in"/></p>
			</form>
		</div>
	</div>
</div>
