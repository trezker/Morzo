<ul class="event_list">
	<input type="text" name="actor_message" id="actor_message" />
	<span class="action" onclick="speak(<?=$actor_id?>);">Speak</span>
	<div id="event_feedback"></div>
	<?php
	$actor_name_template = "<span class='action' onclick='set_actor_changer(\"{id}\");'>{name}</span>";
	foreach ($events as $event) {
		$message = $event["Message"];
		if($event['From_actor_name'] == NULL)
			$event['From_actor_name'] = 'Unnamed actor';
		$actor_name = preg_replace('/{id}/', $event['From_actor_ID'], $actor_name_template);
		$actor_name = preg_replace('/{name}/', $event['From_actor_name'], $actor_name);
		$message = preg_replace('/{From_actor_name}/', $actor_name, $event["Message"]);
		echo "
			<li>
				$message
			</li>
			";
	}
	?>
</ul>

