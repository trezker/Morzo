<?php
	if($project == false) {
		echo "Something is broken";
		return;
	}
?>
<div id="project_details">
	<?php
	echo expand_template(
	'
	Time: {Cycle_time} hours<br />
	',
	$project['info']);
	?>
	
	<div id="recipe_outputs">
		Produces
		<ul class="selectable">
			<?php
			$output_template = '
			<li>
				{Amount}{Measure_desc} {Name}
			</li>';

			foreach ($project['recipe_outputs'] as $output) {
				$output['Measure_desc'] = '';
				if($output['Measure_name'] == 'Mass') {
					$output['Amount'] *= $output['Mass_factor'];
					$output['Measure_desc'] = ' g';
				}
				if($output['Measure_name'] == 'Volume') {
					$output['Amount'] *= $output['Volume_factor'];
					$output['Measure_desc'] = ' l';
				}
				echo expand_template($output_template, $output);
			}
			?>
		</ul>
	</div>

	<div id="recipe_inputs">
		Requirements
		<ul class="selectable">
			<?php
			$input_template = '
				<li>
					{Project_amount}/{Amount}{Measure_desc} {Name} {From_nature_text}
				</li>
			';
			
			$needs_resources = false;
			foreach ($project['recipe_inputs'] as $input) {
				$from_nature_text = "";
				if($input['From_nature'] == 1) {
					$from_nature_text = "from nature";
				} else {
					$needs_resources = true;
				}
				$input['Measure_desc'] = '';
				if($input['Measure_name'] == 'Mass') {
					$input['Amount'] *= $input['Mass_factor'];
					$input['Project_amount'] *= $input['Mass_factor'];
					$input['Measure_desc'] = ' g';
				}
				if($input['Measure_name'] == 'Volume') {
					$input['Amount'] *= $input['Volume_factor'];
					$input['Project_amount'] *= $input['Volume_factor'];
					$input['Measure_desc'] = ' l';
				}

				echo expand_template($input_template, 
												array(
													'Amount' => $input['Amount'],
													'Measure_desc' => $input['Measure_desc'],
													'Project_amount' => $input['Project_amount'],
													'Name' => $input['Name'],
													'From_nature_text' => $from_nature_text
												));
			}
			?>
		</ul>
		<?php 
			if($needs_resources) {
				echo '<span class="action" onclick="supply_project('.$actor_id.', '.$project['info']['ID'].')">Supply resources</span>';
			}
			echo ' <span class="action" onclick="cancel_project('.$actor_id.', '.$project['info']['ID'].')">Cancel</span>';
		?>
	</div>
</div>
