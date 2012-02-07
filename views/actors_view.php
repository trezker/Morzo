<ul class="actor_list">
	<?php
	foreach ($actors as $actor) {
		echo "<li><a href='actor/show_actor/".$actor["ID"]."'>".$actor["Name"]."</a></li>";
	}
	?>
</ul>
