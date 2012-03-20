<h1>Actor inventory</h1>
<table class="inventory_list">
	<?php
	$row_template = '
		<tr class="{alternate}">
			<td>
				{Name}
			</td>
			<td>
				{Amount}
			</td>
		</tr>';

	$alternate = '';
	foreach ($actor_inventory as $inventory) {
		$alternate = ($alternate == 'alternate1')? 'alternate2': 'alternate1';

		echo expand_template($row_template, array(
				'alternate' => $alternate,
				'Name' => $inventory['Name'],
				'Amount' => $inventory['Amount']
			));
	}
	?>
</table>

