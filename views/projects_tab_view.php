<div id="projects_feedback"></div>
<div id="projects">
	<table class="recipe_list">
		<?php
		$row_template = '
			<tr>
				<td>
					<span class="action" onclick="show_project(\'{id}\');">{name}</span>
				</td>
				<td>
					({progress_percent}%)
				</td>
			</tr>';
		foreach ($projects as $project) {
			$vars = array(
				'id' => $project["ID"],
				'name' => $project["Recipe_Name"],
				'progress_percent' => $project["Progress"] / $project["Cycle_time"]
			);
			echo expand_template($row_template, $vars);
		}
		?>
	</table>
</div>
