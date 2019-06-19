<div style="float: left; margin-right: 20px;">
	<?php echo $view_factory->Load_view($data['actor_inventory_view']['view'], $data['actor_inventory_view']['data']); ?>
</div>

<div style="float: left;">
	<?php echo $view_factory->Load_view($data['location_inventory_view']['view'], $data['location_inventory_view']['data']); ?>
</div>

<div id="container_inventories" class="clearboth">
</div>

<div id="object_label_popup" style="display: none" title="Change object label">
	<input type="text" name="label_input" id="label_input" />
</div>
