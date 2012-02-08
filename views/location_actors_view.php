<ul class="actor_list">
	<?php
	foreach ($actors as $a) {
		$id = $a["ID"];
		echo "
		<li>
			".$a["Name"]."
			<span id='changeactorname_$id' class='action namechange' onclick='set_actor_changer(\"$id\");'>Change name</span>
		</li>";
	}
	?>
</ul>
