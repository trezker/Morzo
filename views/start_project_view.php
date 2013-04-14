<h2>Start project</h2>
<div id="recipe">
	<?php
	echo expand_template(
	'
	{Name}<br />
	Time: {Cycle_time} hours<br />
	',
	$recipe['recipe']);
	?>
	
	<div id="recipe_outputs">
		Produces
		<ul class="selectable">
			<?php
			$output_template = '
			<li>
				{Amount}{Measure_desc} {Resource_Name}
			</li>';

			foreach ($recipe['recipe_outputs'] as $output) {
				$output['Measure_desc'] = '';
				if($output['Measure_name'] == 'Mass') {
					$output['Amount'] = $output['Mass'];
					$output['Measure_desc'] = ' g';
				}
				if($output['Measure_name'] == 'Volume') {
					$output['Amount'] = $output['Volume'];
					$output['Measure_desc'] = ' l';
				}
				echo expand_template($output_template, $output);
			}

			$output_template = '
			<li>
				{Amount} {Product_Name}
			</li>';
			foreach ($recipe['recipe_product_outputs'] as $output) {
				echo expand_template($output_template, $output);
			}
			?>
		</ul>
	</div>

	<div id="recipe_inputs">
		Requires
		<ul class="selectable">
			<?php
			$input_template = '
				<li>
					{Amount}{Measure_desc} {Resource_Name} {From_nature_text}
				</li>
			';

			foreach ($recipe['recipe_inputs'] as $input) {
				$from_nature_text = "";
				if($input['From_nature'] == 1)
					$from_nature_text = "from nature";
				$input['Measure_desc'] = '';
				if($input['Measure_name'] == 'Mass') {
					$input['Amount'] = $input['Mass'];
					$input['Measure_desc'] = ' g';
				}
				if($input['Measure_name'] == 'Volume') {
					$input['Amount'] = $input['Volume'];
					$input['Measure_desc'] = ' l';
				}

				echo expand_template($input_template, 
												array(
													'Amount' => $input['Amount'],
													'Measure_desc' => $input['Measure_desc'],
													'Resource_Name' => $input['Resource_Name'],
													'From_nature_text' => $from_nature_text
												));
			}

			$input_template = '
			<li>
				{Amount} {Product_Name}
			</li>';
			foreach ($recipe['recipe_product_inputs'] as $input) {
				echo expand_template($input_template, $input);
			}
			?>
		</ul>
	</div>
	<table>
		<tr>
			<td>
				<input type="checkbox" id="supply_resources_option" style="width: 30px;" />
			</td>
			<td>
				Supply requirements from inventory
			</td>
		</tr>
		<tr>
			<td>
				<input type="number" id="cycle_count" style="width: 30px;" />
			</td>
			<td>
				Number of cycles
			</td>
		</tr>
	</table>
	<div class="action" onclick="start_project(<?php echo $actor_id.', '.$recipe['recipe']['ID'];?>)">Start project</div>
</div>
