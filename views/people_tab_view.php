<div id="actors_feedback"></div>
<div id="actors">
	<ul class="actor_list">
		<?php
		$actor_name_template = "<li><span class='action' onclick='set_actor_changer(\"{ID}\");'>{Name}</span></li>";
		foreach ($actors as $person) {
			echo expand_template($actor_name_template, $person);
		}
		?>
	</ul>
</div>
