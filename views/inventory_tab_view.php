<div style="float: left; margin-right: 20px;">
	<h3>Actor inventory</h3>
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
				<td>
					<input id="drop_amount_{Resource_ID}" type="number" value="0" size="4" style="text-align: right;" />
					<span class="action" onclick="drop_resource({actor_id}, {Resource_ID})">Drop</span>
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
					'actor_id' => $actor_id,
					'Name' => $inventory['Name'],
					'Amount' => $inventory['Amount'],
					'Resource_ID' => $inventory['Resource_ID'],
					'Measure_desc' => $inventory['Measure_desc']
				));
		}
		?>
	</table>
</div>

<div style="float: left;">
	<h3>Location inventory</h3>
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
				<td>
					<input id="pick_up_amount_{Resource_ID}" type="number" value="0" size="4" style="text-align: right;" />
					<span class="action" onclick="pick_up_resource({actor_id}, {Resource_ID})">Pick up</span>
				</td>
			</tr>';

		$alternate = '';
		foreach ($location_inventory as $inventory) {
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
		?>
	</table>
</div>
