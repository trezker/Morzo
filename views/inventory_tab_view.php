<div style="float: left; margin-right: 20px;">
	<h3>Actor inventory</h3>
	<?php
	echo expand_template('<a href="javascript:transfer_to_inventory({actor_id}, {inventory_id})">Transfer here</a>', 
	array(
		'actor_id' => $actor_id,
		'inventory_id' => $inventory_ids['Actor_inventory']
		));
	?>
	<table class="inventory_list">
		<?php
		$alternate = '';

		$row_template = '
			<tr class="{alternate}">
				<td>
					{Name}
				</td>
				<td>
					{Amount}{Measure_desc}
				</td>
				<td>
					<input class="resource_input" id="drop_resource_{Inventory_ID}_{Resource_ID}" data-inventory_id="{Inventory_ID}" data-resource_id="{Resource_ID}" type="number" value="0" size="4" style="text-align: right;" />
				</td>
			</tr>';

		foreach ($actor_inventory['resources'] as $inventory) {
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
					'actor_id' => $actor_id,
					'Name' => $inventory['Name'],
					'Inventory_ID' => $inventory['Inventory_ID'],
					'Amount' => $inventory['Amount'],
					'Resource_ID' => $inventory['Resource_ID'],
					'Measure_desc' => $inventory['Measure_desc']
				));
		}

		$row_template = '
			<tr class="{alternate}">
				<td>
					{Name}
				</td>
				<td>
					{Amount}
				</td>
				<td>
					<input class="product_input" id="drop_product_{Inventory_ID}_{Product_ID}" data-inventory_id="{Inventory_ID}" data-product_id="{Product_ID}" type="number" value="0" size="4" style="text-align: right;" />
				</td>
			</tr>';

		foreach ($actor_inventory['products'] as $inventory) {
			$alternate = ($alternate == 'alternate1')? 'alternate2': 'alternate1';
			
			echo expand_template($row_template, array(
					'alternate' => $alternate,
					'actor_id' => $actor_id,
					'Name' => $inventory['Name'],
					'Inventory_ID' => $inventory['Inventory_ID'],
					'Amount' => $inventory['Amount'],
					'Product_ID' => $inventory['ID'],
				));
		}
		?>
	</table>
</div>

<div style="float: left;">
	<h3>Location inventory</h3>
	<?php
		echo expand_template('<a href="javascript:transfer_to_inventory({actor_id}, {inventory_id})">Transfer here</a>', 
		array(
			'actor_id' => $actor_id,
			'inventory_id' => $inventory_ids['Location_inventory']
			));
	?>
	<table class="inventory_list">
		<?php
		$alternate = '';
		
		$row_template = '
			<tr class="{alternate}">
				<td>
					{Name}
				</td>
				<td>
					{Amount}{Measure_desc}
				</td>
				<td>
					<input class="resource_input" id="drop_resource_{Inventory_ID}_{Resource_ID}" data-inventory_id="{Inventory_ID}" data-resource_id="{Resource_ID}" type="number" value="0" size="4" style="text-align: right;" />
				</td>
			</tr>';

		foreach ($location_inventory['resources'] as $inventory) {
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
					'actor_id' => $actor_id,
					'Name' => $inventory['Name'],
					'Inventory_ID' => $inventory['Inventory_ID'],
					'Amount' => $inventory['Amount'],
					'Resource_ID' => $inventory['Resource_ID'],
					'Measure_desc' => $inventory['Measure_desc']
				));
		}

		$row_template = '
			<tr class="{alternate}">
				<td>
					{Name}
				</td>
				<td>
					{Amount}
				</td>
				<td>
					<input class="product_input" id="drop_product_{Inventory_ID}_{Product_ID}" data-inventory_id="{Inventory_ID}" data-product_id="{Product_ID}" type="number" value="0" size="4" style="text-align: right;" />
				</td>
			</tr>';

		foreach ($location_inventory['products'] as $inventory) {
			$alternate = ($alternate == 'alternate1')? 'alternate2': 'alternate1';
			
			echo expand_template($row_template, array(
					'alternate' => $alternate,
					'actor_id' => $actor_id,
					'Name' => $inventory['Name'],
					'Inventory_ID' => $inventory['Inventory_ID'],
					'Amount' => $inventory['Amount'],
					'Product_ID' => $inventory['ID']
				));
		}
		?>
	</table>
</div>
