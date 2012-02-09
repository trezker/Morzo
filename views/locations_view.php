<ul class="location_list">
	<?php
	foreach ($locations as $location) {
		$id = $location["ID"];
		$name = $location["Name"];
		$compass = $location["Compass"];
		$x = $location["x"];
		$y = $location["y"];
		$current_location = $actor['Location_ID'];
		echo "
			<li>
				<span class='action' onclick='travel(\"$id\",\"$actor_id\",\"$current_location\")'>$name $compass, $x, $y</span>
				<span id='changelink_$id' class='action namechange' onclick='set_location_changer(\"$id\");'>Change name</span>
			</li>
			";
	}
	?>
</ul>
