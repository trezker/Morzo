<h2>Edit category <?php echo htmlspecialchars($category['Name']); ?></h2>
<div id="category" style="margin-left: 10px;">
	<?php
	echo expand_template('
	Name: <input type="text" id="category_name" value="{Name}" /><br />
	',
	$category);
	?>
	<a href="javascript:void(0)" class="action" style="float: right;" onclick="save_category()">Save</a>
</div>
</div>
