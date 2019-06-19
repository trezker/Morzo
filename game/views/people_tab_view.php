<div id="actors_feedback"></div>
<div id="actors">
	<div id="whisper_dialog" style="display:none;">
		<input type="text" name="whisper_message" id="whisper_message" />
		<span class="action" onclick="whisper(<?=$data['actor_id']?>);">Whisper</span>		
	</div>
	<div id="event_feedback"></div>
	<table class="actor_list">
		<?php
		$actor_name_template = "
			<tr>
				<td>
					<span class='action' onclick='set_actor_changer(\"{ID}\");'>{Name}</span>
				</td>
				<td>
					<span class='action' onclick='point_at_actor({My_actor_ID}, {ID});'>Point at</span>
				</td>
				<td>
					<span class='action' onclick='show_whisper({ID});'>Whisper to</span>
				</td>
				<td>
					<span class='action' onclick='attack_actor({My_actor_ID}, {ID});'>Attack</span>
				</td>
			</tr>";
		foreach ($data['actors'] as $person) {
			$person['My_actor_ID'] = $data['actor_id'];
			echo expand_template($actor_name_template, $person);
		}
		?>
	</table>
</div>
