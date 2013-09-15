<?php
foreach ($objects as $object) {
	$actions = '';
	if($object['Object_inventory_ID'] !== NULL) {
		$template =  '<a href="javascript:void(0)" onclick="open_container({actor_id}, {Inventory_ID})">Open</a>';
		if($object['Has_lock'] === NULL) {
			$template .= ' <a href="javascript:void(0)" onclick="attach_lock({actor_id}, {Object_ID}, false)">Attach lock</a>';
		} else {
			$template .= ' <a href="javascript:void(0)" onclick="detach_lock({actor_id}, {Object_ID}, false)">Detach lock</a>';
			if($object['Is_locked'] == false) {
				$template .= ' <a href="javascript:void(0)" onclick="lock_object({actor_id}, {Object_ID}, false)">Lock</a>';
			} else {
				$template .= ' <a href="javascript:void(0)" onclick="unlock_object({actor_id}, {Object_ID}, false)">Unlock</a>';
			}
		}
				
		$actions = expand_template($template,
					array(
						'actor_id' => $actor_id,
						'Inventory_ID' => $object['Object_inventory_ID'],
						'Object_ID' => $object['ID']
					));
	}
	if($object['Object_lock_ID'] !== NULL) {
		if($object['Is_attached'] === NULL) {
			$template .= ' <a href="javascript:void(0)" onclick="attach_lock({actor_id}, {Object_ID}, true)">Attach lock</a>';
		} else {
			$template .= ' <a href="javascript:void(0)" onclick="detach_lock({actor_id}, {Object_ID}, true)">Detach lock</a>';
		}
				
		$actions = expand_template($template,
					array(
						'actor_id' => $actor_id,
						'Inventory_ID' => $object['Object_inventory_ID'],
						'Object_ID' => $object['ID']
					));
	}
	$object_name = $object['Name'];
	if($object['Label'] !== NULL && $object['Label'] != '') {
		$object_name = $object['Label'];
	} else {
		$object['Label'] = '';
	}
	
	$template = '
				<tr data-object_collection="{Inventory_ID}_{Product_ID}">
					<td><a id="name_object_{Object_ID}" href="javascript:void(0)" onclick="show_object_label_dialog({actor_id}, {Object_ID}, \'{Label}\')">{Name}</a></td>
					<td>&nbsp;</td>
					<td>{!actions}</td>
				</tr>
				';
	echo expand_template($template,
		array(
			'actor_id' => $actor_id,
			'Name' => $object_name,
			'Object_ID' => $object['ID'],
			'Label' => $object['Label'],
			'Product_ID' => $product_id,
			'Inventory_ID' => $inventory_id,
			'actions' => $actions
		));
}
?>
