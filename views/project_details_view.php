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
				{Amount} {Name}
			</li>';

			foreach ($project['recipe_outputs'] as $output) {
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
					{Amount} {Name} {From_nature_text}
				</li>
			';

			foreach ($project['recipe_inputs'] as $input) {
				$from_nature_text = "";
				if($input['From_nature'] == 1)
					$from_nature_text = "from nature";

				echo expand_template($input_template, 
												array(
													'Amount' => $input['Amount'],
													'Name' => $input['Name'],
													'From_nature_text' => $from_nature_text
												));
			}
			?>
		</ul>
	</div>
</div>
