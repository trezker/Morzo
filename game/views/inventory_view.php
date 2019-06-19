<h3><?php echo $data['inventory_title']; ?></h3>
<?php
echo expand_template('<a href="javascript:transfer_to_inventory({actor_id}, {inventory_id})">Transfer here</a>', 
array(
	'actor_id' => $data['actor_id'],
	'inventory_id' => $data['inventory_id']
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

	foreach ($data['inventory']['resources'] as $resource) {
		$alternate = ($alternate == 'alternate1')? 'alternate2': 'alternate1';
		
		$resource['Measure_desc'] = '';
		if($resource['Measure_name'] == 'Mass') {
			$resource['Amount'] *= $resource['Mass'];
			$resource['Measure_desc'] = ' g';
		}
		if($resource['Measure_name'] == 'Volume') {
			$resource['Amount'] *= $resource['Volume'];
			$resource['Measure_desc'] = ' l';
		}

		echo expand_template($row_template, array(
				'alternate' => $alternate,
				'actor_id' => $data['actor_id'],
				'Name' => $resource['Name'],
				'Inventory_ID' => $data['inventory_id'],
				'Amount' => $resource['Amount'],
				'Resource_ID' => $resource['Resource_ID'],
				'Measure_desc' => $resource['Measure_desc']
			));
	}

	$row_template = '
		<tr class="{alternate}" id="product_{Inventory_ID}_{Product_ID}">
			<td>
				{!expand}{Name}
			</td>
			<td>
				{Amount}
			</td>
			<td>
				<input class="product_input" id="drop_product_{Inventory_ID}_{Product_ID}" data-inventory_id="{Inventory_ID}" data-product_id="{Product_ID}" type="number" value="0" size="4" style="text-align: right;" />
			</td>
		</tr>';

	foreach ($data['inventory']['products'] as $product) {
		$expand = '';
		$expand = '<div class="expand"><a  href="javascript:void(0)" onclick="expand_product(this, {actor_id}, {Inventory_ID}, {Product_ID})">+</a></div>';
		$expand = expand_template($expand, array(
			'Inventory_ID' => $data['inventory_id'],
			'actor_id' => $data['actor_id'],
			'Product_ID' => $product['ID']
		));

		$alternate = ($alternate == 'alternate1')? 'alternate2': 'alternate1';
		
		echo expand_template($row_template, array(
				'alternate' => $alternate,
				'actor_id' => $data['actor_id'],
				'Name' => $product['Name'],
				'Inventory_ID' => $data['inventory_id'],
				'Amount' => $product['Amount'],
				'Product_ID' => $product['ID'],
				'expand' => $expand
			));
	}
	?>
</table>
