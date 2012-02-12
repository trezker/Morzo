<input type="text" name="actor_message" id="actor_message" />
<span class="action" onclick="speak(<?=$actor_id?>);">Speak</span>
<div id="event_feedback"></div>
<table class="event_list">
	<?php
	$actor_name_template = "<span class='action' onclick='set_actor_changer(\"{id}\");'>{name}</span>";
	$alternate = '';
	foreach ($events as $event) {
		$alternate = ($alternate == 'alternate1')? 'alternate2': 'alternate1';
		echo "<tr class='$alternate'>";
			$message = $event["Message"];
			if($event['From_actor_name'] == NULL)
				$event['From_actor_name'] = 'Unnamed actor';
			if($event['From_actor_ID'] == $actor_id)
				$event['From_actor_name'] = 'You';
			$actor_name = preg_replace('/{id}/', $event['From_actor_ID'], $actor_name_template);
			$actor_name = preg_replace('/{name}/', $event['From_actor_name'], $actor_name);
			$message = preg_replace('/{From_actor_name}/', $actor_name, $message);
			$event_time = $event["Time_values"];

			if($event['From_location_name'] == NULL)
				$event['From_location_name'] = 'Unnamed actor';
			if($event['To_location_name'] == NULL)
				$event['To_location_name'] = 'Unnamed actor';
			$message = preg_replace('/{From_location}/', $event['From_location_name'], $message);
			$message = preg_replace('/{To_location}/', $event['To_location_name'], $message);

			
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
</table>

