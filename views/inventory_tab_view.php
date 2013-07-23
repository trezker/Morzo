<div style="float: left; margin-right: 20px;">
	<?php echo $actor_inventory_view; ?>
</div>

<div style="float: left;">
	<?php echo $location_inventory_view; ?>
</div>

<div id="container_inventories" class="clearboth">
</div>

<div id="object_label_popup" style="display: none">
	<div class="popup_background">
		<div class="popup_title">Change object label</div>
		<div class="popup_content">
			<input type="text" name="label_input" id="label_input" />
			<a href="javascript:void(0)" onclick="label_object();">Change</a>
		</div>
	</div>
</div>
