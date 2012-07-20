<div id="measure_descriptions" style="display: none;">
<?php
$measure_descriptions = array();
foreach($measures as $key => $measure) {
	$measure_descriptions[$measure['ID']] = '';
	if($measure['Name'] == 'Mass') {
		$measure_descriptions[$measure['ID']] = 'g';
	}
	elseif($measure['Name'] == 'Volume') {
		$measure_descriptions[$measure['ID']] = 'l';
	}
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

	$product_select = '<select>';
	$product_template = '
		<option value="{ID}">{Name}</option>
	';
	foreach ($products as $product) {
		$product_select .= expand_template($product_template, $product);
	}
	$product_select .= '</select>';
	?>
	
	<div id="recipe_outputs">
		<?php
		$output_template = '
		<div class="output" id="{ID}">
			<input class="amount" type="number" value="{Amount}" />
			<span class="measuredesc" data-id="{Measure_ID}">{Measuredesc}</span>
			<span class="resource" data-id="{Resource_ID}">{Resource_Name}</span>
			<span class="action" style="float: right;" onclick="remove_output({ID})">Remove</span>
		</div>';

		foreach ($recipe['recipe_outputs'] as $output) {
			$output['Measuredesc'] = $measure_descriptions[$output['Measure_ID']];
			if($output['Measure_name'] == 'Mass') {
				$output['Amount'] = $output['Mass'];
			}
			if($output['Measure_name'] == 'Volume') {
				$output['Amount'] = $output['Volume'];
			}
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
			<span class="measuredesc" data-id="{Measure_ID}">{Measuredesc}</span>
			<span class="resource" data-id="{Resource_ID}">{Resource_Name}</span>
			(from nature: <input type="checkbox" class="from_nature" {From_nature_checked} />)
			<span class="action" style="float: right;" onclick="remove_input({ID})">Remove</span>
		</div>';

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
	
	<div id="recipe_product_outputs">
		<?php
		$output_template = '
		<div class="product_output" id="product_output_{ID}" data-id="{ID}">
			<input class="amount" type="number" value="{Amount}" />
			<span class="product" data-id="{Product_ID}">{Product_Name}</span>
			<span class="action" style="float: right;" onclick="remove_product_output({ID})">Remove</span>
		</div>';

		foreach ($recipe['recipe_product_outputs'] as $output) {
			echo expand_template($output_template, $output);
		}
		?>
	</div>
	<div id="new_product_output_form" style='margin-bottom: 10px;'>
		<span class="action" onclick="add_product_output()">Add product output</span>
		<?php echo $product_select; ?>
	</div>

	<div id="recipe_product_inputs">
		<?php
		$input_template = '
		<div class="product_input" id="product_input_{ID}" data-id="{ID}">
			<input class="amount" type="number" value="{Amount}" />
			<span class="product" data-id="{Product_ID}">{Product_Name}</span>
			<span class="action" style="float: right;" onclick="remove_product_input({ID})">Remove</span>
		</div>';

		foreach ($recipe['recipe_product_inputs'] as $input) {
			echo expand_template($input_template, $input);
		}
		?>
	</div>
	<div id="new_product_input_form" style='margin-bottom: 10px;'>
		<span class="action" onclick="add_product_input()">Add product input</span>
		<?php echo $product_select; ?>
	</div>

	<div id="product_select" style="display: none;">
		<?php
		echo $product_select;
		?>
	</div>
	<div id="new_product_output" style="display: none;">
	<?php
		echo expand_template($output_template, $recipe['new_product_component']);
	?>
	</div>
	<div id="new_product_input" style="display: none;">
	<?php
		echo expand_template($input_template, $recipe['new_product_component']);
	?>
	</div>
	<span class="action" style="float: right;" onclick="save_recipe()">Save</span>
</div>
