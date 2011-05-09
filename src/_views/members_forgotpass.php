<div class="boxout">
	<div class="head">Forgot Password</div>
	<div class="body">
		<div style="width:565px; margin:auto"><p>Enter your email address below. A new password will be sent to you.</p><br/>
			<form action="/members/forgotpass/" method="post">
				<p><label for="l_email">Email address</label><input type="text" name="l_email" id="l_email" value="<? if(isset($_POST['l_email'])){ echo $_POST['l_email']; } ?>" /></p>
				<br/>
				<br/>
				<p class="txt-c"><input type="submit" class="submit" value="Send password"/></p>
			</form>
		</div>
	</div>
</div>
