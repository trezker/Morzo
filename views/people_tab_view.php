<div id="actors_feedback"></div>
<div id="actors">
	<ul class="actor_list">
		<?php
		$actor_name_template = "<span class='action' onclick='set_actor_changer(\"{id}\");'>{name}</span>";
		foreach ($actors as $person) {
			$actor_name = preg_replace('/{id}/', $person['ID'], $actor_name_template);
			$actor_name = preg_replace('/{name}/', $person['Name'], $actor_name);
			echo "<li>$actor_name</li>";
		}
		?>
	</ul>
</div>
