<h2>Edit category <?php echo htmlspecialchars($data['category']['Name']); ?></h2>
<div id="category" style="margin-left: 10px;">
	<?php
	if($data['category']['Is_tool'] == 1)
		$data['category']['Is_tool'] = 'checked=checked';
	else
		$data['category']['Is_tool'] = '';

	echo expand_template('
	Name: <input type="text" id="category_name" value="{Name}" /><br />
	Is_tool: <input type="checkbox" id="category_is_tool" {Is_tool} /><br />
	',
	$data['category']);
	?>
	<a href="javascript:void(0)" class="action" style="float: right;" onclick="save_category()">Save</a>
</div>
</div>
