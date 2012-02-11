<ul class="event_list">
	<input type="text" name="actor_message" id="actor_message" />
	<span class="action" onclick="speak(<?=$actor_id?>);">Speak</span>
	<div id="event_feedback"></div>
	<?php
	foreach ($events as $event) {
		$message = $event["Message"];
		if($event['From_actor_name'] == NULL)
			$event['From_actor_name'] = 'Unnamed actor';
		$message = preg_replace('/{From_actor_name}/', $event['From_actor_name'], $message);
		echo "
			<li>
				$message
			</li>
			";
	}
	?>
</ul>

