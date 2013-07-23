<?php
foreach ($objects as $object) {
	$actions = '';
	if($object['Object_inventory_ID'] !== NULL) {
		$template = '<a href="javascript:void(0)" onclick="open_container({actor_id}, {Inventory_ID})">Open</a>';
		$actions = expand_template($template,
					array(
						'actor_id' => $actor_id,
						'Inventory_ID' => $object['Object_inventory_ID']
					));
	}
	
	$template = '
				<tr data-object_collection="{Inventory_ID}_{Product_ID}">
					<td><a href="javascript:void(0)" onclick="show_object_label_dialog({actor_id}, {Object_ID})">{Name}</a></td>
					<td>&nbsp;</td>
					<td>{!actions}</td>
				</tr>
				';
	echo expand_template($template,
		array(
			'actor_id' => $actor_id,
			'Name' => $object['Name'],
			'Object_ID' => $object['ID'],
			'Product_ID' => $product_id,
			'Inventory_ID' => $inventory_id,
			'actions' => $actions
		));
}
?>
