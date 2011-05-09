<div class="boxout">
	<div class="head">Reply to Message</div>
	<div class="body">
		<form action="/inbox/send/<?=$model['message']['from']['id']?>" method="post">
			<p><label>To</label> <?=$model['message']['from']['name']?>
				<input type="hidden" name="m_display_to" id="m_display_to" value="<?=$model['message']['from']['name']?>"/></p>
			<p><label for="r_email">Subject</label><input type="text" name="m_subject" id="m_subject" value="RE: <?=$model['message']['subject']?>" /></p>
			<p><label for="r_bio">Message </label><textarea name="m_body" id="m_body"></textarea></p>
			<p class="txt-l"><label>&nbsp;</label><input type="submit" class="submit" value="Send"/></p>
		</form>	
	</div>
</div>