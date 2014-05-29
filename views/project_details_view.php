<div id="project_details">
	<?php
	echo expand_template(
		'
		Cycles left: {Cycles_left}<br />
		Time: {Cycle_time} hours<br />
		',
		$data['project']['info']
	);
	?>
	
	<div id="recipe_outputs">
		Produces
		<ul class="selectable">
			<?php
			$output_template = '
			<li>
				{Amount}{Measure_desc} {Name}
			</li>';

			foreach ($data['project']['recipe_outputs'] as $output) {
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

			$output_template = '
			<li>
				{Amount} {Name}
			</li>';

			foreach ($data['project']['recipe_product_outputs'] as $output) {
				echo expand_template($output_template, $output);
			}
			?>
		</ul>
	</div>

	<div id="recipe_inputs">
		Requirements (total supplied / required per cycle)
		<ul class="selectable">
			<?php
			$input_template = '
				<li>
					{Project_amount}/{Amount}{Measure_desc} {Name}
				</li>
			';
			
			echo "<li>Resources<ul>";
			$needs_resources = false;
			$only_from_nature = true;
			foreach ($data['project']['recipe_inputs'] as $input) {
				if($input['From_nature'] == 1) {
					continue;
				} else {
					$only_from_nature = false;
					if($input['Project_amount'] < $input['Amount']) {
						$needs_resources = true;
					}
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
													'Name' => $input['Name']
												));
			}
			if($only_from_nature == true) {
				echo '
					<li>
						None
					</li>
				';
			}
			echo "</ul></li>";

			$input_template = '
			<li>
				{Project_amount}/{Amount} {Name}
			</li>';

			echo "<li>Products<ul>";
			foreach ($data['project']['recipe_product_inputs'] as $input) {
				if($input['Project_amount'] < $input['Amount']) {
					$needs_resources = true;
				}
				echo expand_template($input_template, $input);
			}
			echo "</ul></li>";

			
			$tool_template = '
			<li class="{style}">
				{Name}
			</li>';

			echo "<li>Tools<ul>";
			foreach ($data['project']['recipe_tools'] as $tool) {
				$tool['style'] = '';
				if($tool['Project_amount'] == 0) {
					$tool['style'] = 'inactive_project';
				}
				echo expand_template($tool_template, $tool);
			}
			echo "</ul></li>";
			?>
		</ul>
		<?php 
			if($needs_resources) {
				echo '<span class="action" onclick="supply_project('.$data['actor_id'].', '.$data['project']['info']['ID'].')">Supply resources</span>';
			}
			echo ' <span class="action" onclick="cancel_project('.$data['actor_id'].', '.$data['project']['info']['ID'].')">Cancel</span>';
		?>
	</div>
</div>
