<div id="measure_descriptions" style="display: none;">
<?php
$measure_descriptions = array();
foreach($measures as $key => $measure) {
	$measure_descriptions[$measure['ID']] = '';
	if($measure['Name'] == 'Mass') {
		$measure_descriptions[$measure['ID']] = 'kg';
	}
	elseif($measure['Name'] == 'Volume') {
		$measure_descriptions[$measure['ID']] = 'l';
	}
	echo '<span id="measuredesc_'.$measure['ID'].'">'.$measure_descriptions[$measure['ID']].'</span>';
}
?>
</div>

<h2>Edit recipe <?php echo htmlspecialchars($recipe['recipe']['Name']); ?></h2>
<div id="recipe">
	<?php
	if($recipe['recipe']['Allow_fraction_output'] == 1)
		$recipe['recipe']['Allow_fraction_output_checked'] = 'checked=checked';
	else
		$recipe['recipe']['Allow_fraction_output_checked'] = '';

	if($recipe['recipe']['Require_full_cycle'] == 1)
		$recipe['recipe']['Require_full_cycle_checked'] = 'checked=checked';
	else
		$recipe['recipe']['Require_full_cycle_checked'] = '';

	echo expand_template(
	'<table>
		<tr>
			<td class="label">Name:</td>
			<td><input type="text" id="recipe_name" value="{Name}" /></td>
		</tr>
		<tr>
			<td class="label">Cycle time:</td>
			<td><input type="number" id="cycle_time" value="{Cycle_time}" /></td>
		</tr>
		<tr>
			<td class="label">Allow fraction output:</td>
			<td><input type="checkbox" id="allow_fraction_output" {Allow_fraction_output_checked} /></td>
		</tr>
		<tr>
			<td class="label">Require full cycle:</td>
			<td><input type="checkbox" id="require_full_cycle" {Require_full_cycle_checked} /></td>
		</tr>
	</table>',
	$recipe['recipe']);

	$resource_select = '<select>';
	$resource_template = '
		<option value="{ID}" data-measure="{Measure}">{Name}</option>
	';
	foreach ($resources as $resource) {
		$resource_select .= expand_template($resource_template, $resource);
	}
	$resource_select .= '</select>';
	?>
	
	<div id="recipe_outputs">
		<?php
		$output_template = '
		<div class="output" id="{ID}">
			<input class="amount" type="number" value="{Amount}" />
			<span class="measuredesc">{Measuredesc}</span>
			<span class="resource" data-id="{Resource_ID}">{Resource_Name}</span>
			<span class="action" style="float: right;" onclick="remove_output({ID})">Remove</span>
		</div>';

		foreach ($recipe['recipe_outputs'] as $output) {
			$output['Measuredesc'] = $measure_descriptions[$output['Measure_ID']];
			echo expand_template($output_template, $output);
		}
		?>
	</div>
	<div id="new_output_form" style='margin-bottom: 10px;'>
		<span class="action" onclick="add_output()">Add output</span>
		<?php echo $resource_select; ?>
	</div>

	<div id="recipe_inputs">
		<?php
		$input_template = '
		<div class="input" id="{ID}">
			<input class="amount" type="number" value="{Amount}" />
			<span class="measuredesc">{Measuredesc}</span>
			<span class="resource" data-id="{Resource_ID}">{Resource_Name}</span>
			(from nature: <input type="checkbox" class="from_nature" {From_nature_checked} />)
			<span class="action" style="float: right;" onclick="remove_input({ID})">Remove</span>
		</div>';

		foreach ($recipe['recipe_inputs'] as $input) {
			if($input['From_nature'] == 1)
				$input['From_nature_checked'] = 'checked=checked';
			else
				$input['From_nature_checked'] = '';
			$input['Measuredesc'] = $measure_descriptions[$input['Measure_ID']];
			echo expand_template($input_template, $input);
		}
		?>
	</div>
	<div id="new_input_form" style='margin-bottom: 10px;'>
		<span class="action" onclick="add_input()">Add input</span>
		<?php echo $resource_select; ?>
	</div>

	<div id="resource_select" style="display: none;">
		<?php
		echo $resource_select;
		?>
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
	<span class="action" style="float: right;" onclick="save_recipe()">Save</span>
</div>
