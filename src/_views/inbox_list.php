<div class="boxout">
	<div class="head"><? if(isset($model['title'])){ echo $model['title']; } ?></div>
	<div class="body">

	<? foreach($model['messages'] as $message) { ?>
		<div class="cell clear" style="width:150px; border-bottom:1px solid #ccc; ">
			<p><?=date("d/m/y H:i:s",$message['time'])?></p></div>
			
		<div class="cell" style="width:150px; border-bottom:1px solid #ccc; ">
			<? if($model['msg_view'] == "received") { ?>
				<p>From: <?=$message['from']['name']?></p>
			<? } else { ?>
				<p>To: <?=$message['to']['name']?></p>
			<? } ?>
		</div>
			
		<div class="cell"  style="width:500px; border-bottom:1px solid #ccc; ">
			<p>Subject: <a href="/inbox/view/<?=$message['_id']?>"><?=$message['subject']?></a> 
				<?=(($message['read'] == false && $model['msg_view'] == "received")? '<span style="color:#a40000">( New! )</span>' : '')?></p></div>
	<? } ?>
	
	</div>
</div>