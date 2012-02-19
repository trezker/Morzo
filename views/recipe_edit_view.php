<h2>Edit recipe <?php echo htmlspecialchars($recipe['recipe']['Name']); ?></h2>
<div id="recipe">
	<div class="action" onclick="save_recipe()">Save</div>
	
	<?php
	if($recipe['recipe']['Allow_fraction_output'] == 1)
		$recipe['recipe']['Allow_fraction_output_checked'] = 'checked=checked';
	else
		$recipe['recipe']['Require_full_cycle_checked'] = '';

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
	?>
</div>
