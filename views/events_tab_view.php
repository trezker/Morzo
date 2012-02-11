<ul class="event_list">
	<input type="text" name="actor_message" id="actor_message" />
	<span class="action" onclick="speak(<?=$actor_id?>);">Speak</span>
	<div id="event_feedback"></div>
	<?php
	foreach ($events as $event) {
		$message = $event["Message"];
		echo "
			<li>
				$message
			</li>
			";
	}
	?>
</ul>

