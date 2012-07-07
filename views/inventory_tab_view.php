<div style="float: left; margin-right: 20px;">
	<h3>Actor inventory</h3>
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
					<input id="drop_amount_{Resource_ID}" type="number" value="0" size="4" style="text-align: right;" />
					<span class="action" onclick="drop_resource({actor_id}, {Resource_ID})">Drop</span>
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
					<input id="drop_product_amount_{Product_ID}" type="number" value="0" size="4" style="text-align: right;" />
					<span class="action" onclick="drop_product({actor_id}, {Product_ID})">Drop</span>
				</td>
			</tr>';

		foreach ($actor_inventory['products'] as $inventory) {
			$alternate = ($alternate == 'alternate1')? 'alternate2': 'alternate1';
			
			echo expand_template($row_template, array(
					'alternate' => $alternate,
					'actor_id' => $actor_id,
					'Name' => $inventory['Name'],
					'Amount' => $inventory['Amount'],
					'Product_ID' => $inventory['ID'],
				));
		}
		?>
	</table>
</div>

<div style="float: left;">
	<h3>Location inventory</h3>
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
					<input id="pick_up_amount_{Resource_ID}" type="number" value="0" size="4" style="text-align: right;" />
					<span class="action" onclick="pick_up_resource({actor_id}, {Resource_ID})">Pick up</span>
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
					<input id="pick_up_product_amount_{Product_ID}" type="number" value="0" size="4" style="text-align: right;" />
					<span class="action" onclick="pick_up_product({actor_id}, {Product_ID})">Pick up</span>
				</td>
			</tr>';

		foreach ($location_inventory['products'] as $inventory) {
			$alternate = ($alternate == 'alternate1')? 'alternate2': 'alternate1';
			
			echo expand_template($row_template, array(
					'alternate' => $alternate,
					'actor_id' => $actor_id,
					'Name' => $inventory['Name'],
					'Amount' => $inventory['Amount'],
					'Product_ID' => $inventory['ID']
				));
		}
		?>
	</table>
</div>
