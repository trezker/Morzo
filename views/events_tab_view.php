<input type="text" name="actor_message" id="actor_message" />
<span class="action" onclick="speak(<?=$actor_id?>);">Speak</span>
<div id="event_feedback"></div>
<table class="event_list">
	<?php
	$actor_name_template = "<span class='action' onclick='set_actor_changer(\"{id}\");'>{name}</span>";
	$location_name_template = "<span class='action' onclick='set_location_changer(\"{id}\");'>{name}</span>";
	$row_template = '<tr class="{alternate}"><td class="event_time">' . 
		'[{event_year}:{event_month}:{event_day}:{event_hour}]' .
		'</td><td>{!message}</td></tr>';
	
	$alternate = '';
	foreach ($events as $event) {
		$alternate = ($alternate == 'alternate1')? 'alternate2': 'alternate1';
		$message = $event["Message"];

		// build actor name sub-template
		if($event['From_actor_name'] == NULL)
			$event['From_actor_name'] = 'Unnamed actor';
		if($event['From_actor_ID'] == $actor_id)
			$event['From_actor_name'] = 'You';
		$actor_name = expand_template($actor_name_template, array(
			'id' => $event['From_actor_ID'],
			'name' => $event['From_actor_name']));

		$event_time = $event["Time_values"];

		// build location name sub-template
		if($event['From_location_name'] == NULL)
			$event['From_location_name'] = 'Unnamed actor';
		if($event['To_location_name'] == NULL)
			$event['To_location_name'] = 'Unnamed actor';

		$from_location_name = expand_template($location_name_template, array(
			'id' => $event['From_location_ID'],
			'name' => $event['From_location_name']));
		$to_location_name = expand_template($location_name_template, array(
			'id' => $event['To_location_ID'],
			'name' => $event['To_location_name']));

		// build message sub-template
		$message = expand_template($message, array(
			'From_location' => $from_location_name,
			'To_location' => $to_location_name,
			'From_actor_name' => $actor_name),
			true);

		echo expand_template($row_template, array(
			'message' => $message,
			'alternate' => $alternate,
			'event_year' => $event_time['year'],
			'event_month' => $event_time['month'],
			'event_day' => $event_time['day']));
			
	}
	?>
</table>

