<? global $framework; ?>
<div class="boxout">
	<div class="head">View Message</div>
	<div class="body">
	
		<div class="cell clear" style="width:100px;"><p>To</p></div>
			<div class="cell" style="width:400px;"><p><a href="/members/view/<?=$model['message']['to']['id']?>"><?=$model['message']['to']['name']?></a></p></div>
		<div class="cell clear" style="width:100px;"><p>From</p></div>
			<div class="cell" style="width:400px;"><p><a href="/members/view/<?=$model['message']['from']['id']?>"><?=$model['message']['from']['name']?></a></p></div>
			
		<div class="cell clear" style="width:100px; border-bottom:1px solid #ccc; "><p>Subject</p></div>
			<div class="cell" style="width:400px; border-bottom:1px solid #ccc; "><p><?=$model['message']['subject']?></p></div>
			
		<div class="cell clear" style="width:500px; border-bottom:1px solid #ccc; "><p><?=nl2br($model['message']['body'])?></p></div>
		
		<div class="cell clear" style="width:500px;"><p>
		<? if ($model['message']['to']['id'] == $framework->user['mongoRef']) { ?>
			<a href="/inbox/reply/<?=$model['message']['_id']?>">Reply</a> | 
		<? } ?>
			<a href="/inbox/">Inbox</a>
		</p></div>
	</div>
</div>
