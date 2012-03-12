<div id="projects_feedback"></div>
<div id="projects">
	<table class="recipe_list">
		<?php
		$row_template = '
			<tr>
				<td>
					<span class="action" onclick="show_project({actor_id}, {id});">{name}</span>
				</td>
				<td>
					({progress_percent}%)
				</td>
				<td>
					{!Join/Leave}
				</td>
			</tr>';
		foreach ($projects as $project) {
			if($project["Joined"] == 0) {
				$joinleave = '<span class="action" onclick="join_project({actor_id}, {id})">Join</span>';
			} else {
				$joinleave = '<span class="action" onclick="leave_project({actor_id}, {id})">Leave</span>';
			}
			$vars = array(
				'Join/Leave' => $joinleave,
				'id' => $project["ID"],
				'name' => $project["Recipe_Name"],
				'progress_percent' => $project["Progress"] / $project["Cycle_time"],
				'actor_id' => $actor_id
			);
			echo expand_template($row_template, $vars);
		}
		?>
	</table>
</div>
