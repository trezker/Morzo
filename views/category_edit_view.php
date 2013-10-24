<h2>Edit category <?php echo htmlspecialchars($category['Name']); ?></h2>
<div id="category" style="margin-left: 10px;">
	<?php
	$category['container_section'] = expand_template('
	<span class="action" onclick="show_category_container_properties()">
		Container properties
	</span><br />
	<div id="container_properties_container">
		<input type="checkbox" id="is_container" {is_container_checked} /> Is container<br />
		Mass limit: <input type="number" id="container_mass_limit" value="{Mass_limit}" /><br />
		Volume limit: <input type="number" id="container_volume_limit" value="{Volume_limit}" />
	</div>
	', $container);
	
	echo expand_template('
	Name: <input type="text" id="category_name" value="{Name}" /><br />
	{!container_section}
	',
	$category);
	?>
	<a href="javascript:void(0)" class="action" style="float: right;" onclick="save_category()">Save</a>
</div>
</div>
