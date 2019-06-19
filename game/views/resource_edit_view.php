<h2>Edit resource <?php echo htmlspecialchars($data['resource']['Name']); ?></h2>
<div id="resource">
	<?php
	$categorieshtml = '';
	if($data['categories']) {
		foreach($data['categories'] as $category) {
			$categorieshtml .= $view_factory->Load_view('category_view', $category);
		}
	}
	$categorymenuhtml = '<select id="resource_category_select">';
	foreach($data['category_list'] as $category) {
		$categorymenuhtml .= expand_template(
			'<option value="{ID}">{Name}</option>',
			$category
		);
	}
	$categorymenuhtml .= '</select>
	<span class="action" onclick="add_resource_category()">Add</span>
	';
		
	$data['resource']['categorieshtml'] = $categorieshtml;
	$data['resource']['categorymenuhtml'] = $categorymenuhtml;
	
	if($data['resource']['Is_natural'] == 1)
		$data['resource']['Is_natural'] = 'checked=checked';
	else
		$data['resource']['Is_natural'] = '';

	$measure_template = '
		<option value="{ID}"{selected}>{Name}</option>
	';
	$data['resource']['measure_options'] = '';
	foreach ($data['measures'] as $measure) {
		if($resource['Measure'] == $measure['ID']) {
			$measure['selected'] = ' selected="true"';
		} else {
			$measure['selected'] = '';
		}
		$data['resource']['measure_options'] .= expand_template($measure_template, $measure);
	}

	echo expand_template(
	'<table>
		<tr>
			<td class="label">Name:</td>
			<td><input type="text" id="resource_name" value="{Name}" /></td>
		</tr>
		<tr>
			<td class="label">Is natural:</td>
			<td><input type="checkbox" id="is_natural" {Is_natural} /></td>
		</tr>
		<tr>
			<td class="label">Measured by:</td>
			<td>
				<select id="measure">
					{!measure_options}
				</select>
			</td>
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
	$data['resource']);
	?>
	<a href="javascript:void(0)" class="action" style="float: right;" onclick="save_resource()">Save</a>
</div>
</div>
