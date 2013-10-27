<div id="measure_descriptions" style="display: none;">
<?php
foreach($measures as $key => $measure) {
	echo '
		<div id="measuredesc_'.$measure['ID'].'">
			<span class="measuredesc" data-id="'.$measure['ID'].'">'.$measure_descriptions[$measure['ID']].'</span>
		</div>';
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
	'<div class="edit_panel">
		<table>
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
		</table>
	</div>',
	$recipe['recipe']);

	$resource_select = '<select>';
	$resource_template = '
		<option value="{ID}" data-measure="{Measure}">{Name}</option>
	';
	foreach ($resources as $resource) {
		$resource_select .= expand_template($resource_template, $resource);
	}
	$resource_select .= '</select>';

	$product_select = '<select>';
	$product_template = '
		<option value="{ID}">{Name}</option>
	';
	foreach ($products as $product) {
		$product_select .= expand_template($product_template, $product);
	}
	$product_select .= '</select>';

	$tool_select = '<select>';
	$tooloption_template = '
		<option value="{ID}">{Name}</option>
	';
	foreach ($tools as $tool) {
		$tool_select .= expand_template($tooloption_template, $tool);
	}
	$tool_select .= '</select>';
	?>
	
	<div id="recipe_outputs" class="edit_panel">
		<div class="panel_header">Resource outputs</div>
		<div id="new_output_form">
			<span class="action" onclick="add_output()">Add output</span>
			<?php echo $resource_select; ?>
		</div>
		<?php
		foreach ($recipe['recipe_outputs'] as $output) {
			$output['Measuredesc'] = $measure_descriptions[$output['Measure_ID']];
			if($output['Measure_name'] == 'Mass') {
				$output['Amount'] = $output['Mass'];
			}
			if($output['Measure_name'] == 'Volume') {
				$output['Amount'] = $output['Volume'];
			}
			echo expand_template($resource_output_template, $output);
		}
		?>
	</div>

	<div id="recipe_inputs" class="edit_panel">
		<div class="panel_header">Resource inputs</div>
		<div id="new_input_form">
			<span class="action" onclick="add_input()">Add input</span>
			<?php echo $resource_select; ?>
		</div>
		<?php
		foreach ($recipe['recipe_inputs'] as $input) {
			if($input['From_nature'] == 1)
				$input['From_nature_checked'] = 'checked=checked';
			else
				$input['From_nature_checked'] = '';
			if($input['Measure_name'] == 'Mass') {
				$input['Amount'] = $input['Mass'];
			}
			if($input['Measure_name'] == 'Volume') {
				$input['Amount'] = $input['Volume'];
			}
			$input['Measuredesc'] = $measure_descriptions[$input['Measure_ID']];
			echo expand_template($resource_input_template, $input);
		}
		?>
	</div>

	<div id="recipe_product_outputs" class="edit_panel">
		<div class="panel_header">Product outputs</div>
		<div id="new_product_output_form">
			<span class="action" onclick="add_product_output()">Add product output</span>
			<?php echo $product_select; ?>
		</div>
		<?php
		foreach ($recipe['recipe_product_outputs'] as $output) {
			echo expand_template($product_output_template, $output);
		}
		?>
	</div>

	<div id="recipe_product_inputs" class="edit_panel">
		<div class="panel_header">Product inputs</div>
		<div id="new_product_input_form">
			<span class="action" onclick="add_product_input()">Add product input</span>
			<?php echo $product_select; ?>
		</div>
		<?php
		foreach ($recipe['recipe_product_inputs'] as $input) {
			echo expand_template($product_input_template, $input);
		}
		?>
	</div>

	<div id="recipe_tools" class="edit_panel">
		<div class="panel_header">Tools</div>
		<div id="new_tool_form">
			<span class="action" onclick="add_tool()">Add tool</span>
			<?php echo $tool_select; ?>
		</div>
		<?php
		foreach ($recipe['recipe_tools'] as $tool) {
			echo expand_template($tool_template, $tool);
		}
		?>
	</div>

	<a href="javascript:void(0)" style="float: right;" onclick="save_recipe()">Save</a>
</div>
