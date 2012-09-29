<h2>Edit category <?php echo htmlspecialchars($category['Name']); ?></h2>
<div id="category">
	<?php
	echo expand_template(
	'<table>
		<tr>
			<td class="label">Name:</td>
			<td><input type="text" id="category_name" value="{Name}" /></td>
		</tr>
	</table>',
	$category);
	?>
	<span class="action" style="float: right;" onclick="save_category()">Save</span>
</div>
</div>
