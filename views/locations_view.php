<table class="location_list">
	<?php
	foreach ($locations as $location) {
		$id = $location["ID"];
		$name = $location["Name"];
		$compass = $location["Compass"];
		$x = $location["x"];
		$y = $location["y"];
		$current_location = $actor['Location_ID'];
		echo "
				<tr>
					<td>
						<span class='action' onclick='set_location_changer(\"$id\");'>$name</span>
					</td>
					<td>
						<span class='action' onclick='travel(\"$id\",\"$actor_id\",\"$current_location\")'>Travel $compass</span>
					</td>
				</tr>
			</li>
			";
	}
	?>
</table>
