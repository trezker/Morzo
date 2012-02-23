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
				{Amount} {Resource_Name}
			</li>';

			foreach ($recipe['recipe_outputs'] as $output) {
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
					{Amount} {Resource_Name} {From_nature_text}
				</li>
			';

			foreach ($recipe['recipe_inputs'] as $input) {
				$from_nature_text = "";
				if($input['From_nature'] == 1)
					$from_nature_text = "from nature";

				echo expand_template($input_template, 
												array(
													'Amount' => $input['Amount'],
													'Resource_Name' => $input['Resource_Name'],
													'From_nature_text' => $from_nature_text
												));
			}
			?>
		</ul>
	</div>

	<div id="resource_select" style="display: none;">
		<select>
			<?php
			$resource_template = '
				<option value="{ID}">{Name}</option>
			';
			foreach ($resources as $resource) {
				echo expand_template($resource_template, $resource);
			}
			?>
		</select>
	</div>
	<div id="new_output" style="display: none;">
	<?php
		echo expand_template($output_template, $recipe['new_output']);
	?>
	</div>
	<div id="new_input" style="display: none;">
	<?php
		echo expand_template($input_template, $recipe['new_input']);
	?>
	</div>
</div>
