<ul class="location_list">
	<?php
	foreach ($locations as $location) {
		$id = $location["ID"];
		$name = $location["Name"];
		$compass = $location["Compass"];
		$x = $location["x"];
		$y = $location["y"];
		echo "
			<li>
				<a href='/user/travel/$id'>$name $compass, $x, $y</a>
				<span id='changelink_$id' class='action namechange' onclick='set_location_changer(\"$id\");'>Change name</span>
			</li>
			";
	}
	?>
</ul>
