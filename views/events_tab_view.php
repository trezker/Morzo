<input type="text" name="actor_message" id="actor_message" />
<span class="action" onclick="speak(<?=$actor_id?>);">Speak</span>
<div id="event_feedback"></div>
<table class="event_list">
	<?php
	$actor_name_template = "<span class='action' onclick='set_actor_changer(\"{id}\");'>{name}</span>";
	foreach ($events as $event) {
		echo '<tr>';
			$message = $event["Message"];
			if($event['From_actor_name'] == NULL)
				$event['From_actor_name'] = 'Unnamed actor';
			$actor_name = preg_replace('/{id}/', $event['From_actor_ID'], $actor_name_template);
			$actor_name = preg_replace('/{name}/', $event['From_actor_name'], $actor_name);
			$message = preg_replace('/{From_actor_name}/', $actor_name, $event["Message"]);
			$event_time = $event["Time_values"];
			echo "
				<td class='event_time'>
					[".$event_time['year'].':'.$event_time['month'].':'.$event_time['day'].':'.$event_time['hour']."]
				</td>
				<td>
					$message
				</td>
			";
		echo '</tr>';
	}
	?>
</ul>

