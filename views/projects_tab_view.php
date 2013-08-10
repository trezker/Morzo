<div id="recipe_menu_container">
	<span class="action" id="open_recipe_menu" onclick="toggle_recipe_menu(<?php echo $actor_id;?>);">Start a new project</span>
	<div id="recipe_menu_content" style="display: none;">
		<span class="action" onclick="toggle_recipe_menu();">Close recipe menu</span>
		<?php echo $recipe_selection_view;?>
	</div>
</div>

<div id="projects_feedback"></div>
<div id="projects">
	<table class="project_list">
		<?php
		$alternate = '';
		$row_template = '
			<tr id="project_row_{id}" class="{alternate}">
				<td>
					<span class="action" onclick="show_project({actor_id}, {id});">{name}</span>
				</td>
				<td class="{active_class}">
					({progress_percent}%)
				</td>
				<td>
					{!Join/Leave}
				</td>
			</tr>';
		foreach ($projects as $project) {
			$alternate = ($alternate == 'alternate1')? 'alternate2': 'alternate1';
			if($project["Joined"] == 0) {
				$joinleave = '<span class="action" onclick="join_project({actor_id}, {id})">Join</span>';
			} else {
				$joinleave = '<span class="action" onclick="leave_project({actor_id})">Leave</span>';
			}
			if($project["Active"] == 0) {
				$active_class = 'inactive_project';
			} else {
				$active_class = 'active_project';
			}
			$vars = array(
				'Join/Leave' => $joinleave,
				'id' => $project["ID"],
				'name' => $project["Recipe_Name"],
				'progress_percent' => 100 * $project["Progress"] / $project["Cycle_time"],
				'actor_id' => $actor_id,
				'active_class' => $active_class,
				'alternate' => $alternate
			);
			echo expand_template($row_template, $vars);
		}
		$row_template = '
			<tr id="hunt_row_{id}" class="{alternate}">
				<td>
					<span class="action" onclick="show_hunt({actor_id}, {id});">{name}</span>
				</td>
				<td class="{active_class}">
					({progress_percent}%)
				</td>
				<td>
					{!Join/Leave}
				</td>
			</tr>';
		echo '
			<tr>
				<td colspan="3">
					<h2>Hunts</h2>
				</td>
			</tr>';
		foreach ($hunts as $hunt) {
			$alternate = ($alternate == 'alternate1')? 'alternate2': 'alternate1';
			if($hunt["Joined"] == 0) {
				$joinleave = '<span class="action" onclick="join_hunt({actor_id}, {id})">Join</span>';
			} else {
				$joinleave = '<span class="action" onclick="leave_hunt({actor_id})">Leave</span>';
			}
			if($hunt["Participants"] == 0) {
				$active_class = 'inactive_project';
			} else {
				$active_class = 'active_project';
			}
			$vars = array(
				'Join/Leave' => $joinleave,
				'id' => $hunt["ID"],
				'name' => $hunt["Description"],
				'progress_percent' => 100 * ($hunt["Duration"] - $hunt["Hours_left"]) / $hunt["Duration"],
				'actor_id' => $actor_id,
				'active_class' => $active_class,
				'alternate' => $alternate
			);
			echo expand_template($row_template, $vars);
		}
		echo '
			<tr id="project_details_row" style="display: none;">
				<td id="project_details_container" colspan="3" style="max-width: 300px;">
					Here comes the details, later, when it is implemented. Need to do a bit of styling on this table to make things loook alright.
				</td>
			</tr>
			<tr id="hunt_details_row" style="display: none;">
				<td id="hunt_details_container" colspan="3" style="max-width: 300px;">
					Here comes the details, later, when it is implemented. Need to do a bit of styling on this table to make things loook alright.
				</td>
			</tr>
			';
		?>
	</table>
</div>
