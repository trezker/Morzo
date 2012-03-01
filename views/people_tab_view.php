<div id="actors_feedback"></div>
<div id="actors">
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
			</tr>";
		foreach ($actors as $person) {
			$person['My_actor_ID'] = $actor_id;
			echo expand_template($actor_name_template, $person);
		}
		?>
	</table>
</div>
