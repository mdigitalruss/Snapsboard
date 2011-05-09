<div class="boxout">
	<div class="head">Reply to Message</div>
	<div class="body">
		<form action="/inbox/send/<?=$model['user']['_id']?>" method="post">
			<p><label>To</label> <?=$model['user']['username']?>
				<input type="hidden" name="m_display_to" id="m_display_to" value="<?=$model['user']['username']?>"/></p>
			<p><label for="r_email">Subject</label><input type="text" name="m_subject" id="m_subject" /></p>
			<p><label for="r_bio">Message </label><textarea name="m_body" id="m_body"></textarea></p>
			<p class="txt-l"><label>&nbsp;</label><input type="submit" class="submit" value="Send"/></p>
		</form>	
	</div>
</div>