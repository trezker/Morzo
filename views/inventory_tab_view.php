<h1>Actor inventory</h1>
<table class="inventory_list">
	<?php
	$row_template = '
		<tr class="{alternate}">
			<td>
				{Name}
			</td>
			<td>
				{Amount}{Measure_desc}
			</td>
		</tr>';

	$alternate = '';
	foreach ($actor_inventory as $inventory) {
		$alternate = ($alternate == 'alternate1')? 'alternate2': 'alternate1';
		
		$inventory['Measure_desc'] = '';
		if($inventory['Measure_name'] == 'Mass') {
			$inventory['Amount'] *= $inventory['Mass'];
			$inventory['Measure_desc'] = ' g';
		}
		if($inventory['Measure_name'] == 'Volume') {
			$inventory['Amount'] *= $inventory['Volume'];
			$inventory['Measure_desc'] = ' l';
		}

		echo expand_template($row_template, array(
				'alternate' => $alternate,
				'Name' => $inventory['Name'],
				'Amount' => $inventory['Amount'],
				'Measure_desc' => $inventory['Measure_desc']
			));
	}
	?>
</table>

