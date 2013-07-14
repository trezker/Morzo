<?php
foreach ($objects as $object) {
	$template = '
				<tr data-object_collection="{Inventory_ID}_{Product_ID}">
					<td>{Name}</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				';
	echo expand_template($template,
		array(
			'Name' => $object['Name'],
			'Product_ID' => $product_id,
			'Inventory_ID' => $inventory_id
		));
}
?>
