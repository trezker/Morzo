<h2>Edit product <?php echo htmlspecialchars($data['product']['Name']); ?></h2>
<div id="product">
	<?php
	foreach($data['categories'] as $n => $category) {
		$data['categories'][$n]["properties"] = $view_factory->Load_view('category_properties_view', $category);
	}
	$categorytemplate =	'
		<tr class="category" id="category_{ID}" data-category_id="{ID}">
			<td>{Name}</td>
			<td>{!properties}</td>
			<td>
				<a href="javascript:void(0)" class="action" onclick="remove_category({ID})">X</a>
			</td>
		</tr>
	';
	$categorieshtml = '';
	if($data['categories']) {
		foreach($data['categories'] as $category) {
			$categorieshtml .= expand_template($categorytemplate, $category);
		}
	}
	$categorymenuhtml = '<select id="product_category_select">';
	foreach($data['category_list'] as $category) {
		$categorymenuhtml .= expand_template(
			'<option value="{ID}">{Name}</option>',
			$category
		);
	}
	$categorymenuhtml .= '</select>
	<span class="action" onclick="add_product_category()">Add</span>
	';
		
	$data['product']['categorieshtml'] = $categorieshtml;
	$data['product']['categorymenuhtml'] = $categorymenuhtml;
	echo expand_template(
	'<table>
		<tr>
			<td class="label">Name:</td>
			<td><input type="text" id="product_name" value="{Name}" /></td>
		</tr>
		<tr>
			<td class="label">Mass (gram):</td>
			<td><input type="number" id="mass" value="{Mass}" /></td>
		</tr>
		<tr>
			<td class="label">Volume (litre):</td>
			<td><input type="number" id="volume" value="{Volume}" /></td>
		</tr>
		<tr>
			<td class="label">Rot rate:</td>
			<td><input type="number" id="rot_rate" value="{Rot_rate}" /></td>
		</tr>
		<tr>
			<td class="label">Categories:</td>
			<td>{!categorymenuhtml}</td>
		</tr>
		<tr>
			<td colspan="2">
				<table id="categorycontainer">
					{!categorieshtml}
				</table>
			</td>
		</tr>
	</table>',
	$data['product']);
	?>
	<a href="javascript:void(0)" class="action" style="float: right;" onclick="save_product()">Save</a>
</div>
</div>
