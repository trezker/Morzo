<?php
if(count($data['recipe_list']) == 0) {
	echo "No recipes found.";
} else {
	echo "<ul class='selectable' id='recipes'>";

		$template = '<li class="action" id="recipe_{ID}" onclick="show_project_start_form('.$data['actor_id'].', \'{ID}\')">{Name}</li>';
		foreach ($data['recipe_list'] as $recipe) {
			echo expand_template($template, $recipe);
		}

	echo "
		</ul>
		<div id='view_recipe'></div>";
}
