<ul class="selectable" id="resources">
	<?php
	$template = '<li class="action" id="recipe_{ID}" onclick="show_project_start_form(\'{ID}\')">{Name}</li>';
	foreach ($recipe_list as $recipe) {
		echo expand_template($template, $recipe);
	}
	?>
</ul>
