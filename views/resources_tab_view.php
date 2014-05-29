<h3>Resources</h3>
<table>
	<?php
	$resource_template = '
				<td class="action" onclick="get_natural_resource_dialog({actor_id}, {ID})">
					{Name}
				</td>
			';
	$resourcecellmap = array();
	foreach ($data['resources'] as $resource) {
		$resource['actor_id'] = $data['actor_id'];
		$landscape = $resource['Landscape_name'];
		if(!isset($resourcecellmap[$landscape]))
			$resourcecellmap[$landscape] = "";
		$resourcecellmap[$landscape] .= expand_template($resource_template, $resource);
	}

	$landscaperowtemplate = '
			<tr>
				<td>
					{landscape}
				</td>
				{!landscaperesoures}
			</tr>
			';
	foreach ($resourcecellmap as  $landscape => $landscaperesoures) {
		$args = array('landscape' => $landscape, 'landscaperesoures' => $landscaperesoures);
		echo expand_template($landscaperowtemplate, $args);
	}
	?>
</table>
<div id="natural_resource_dialog">
</div>

<div>
	<h3>Hunt wild Animals</h3>
	<table>
		<tr>
			<th>
				Species
			</th>
			<th>
				Max nr
			</th>
		</tr>
<?php
	$speciestemplate = '<tr>
							<td>{Name}</td>
							<td><input type="number" class="huntspecies" data-species_id="{ID}" style="width: 30px;" /></td>
						</tr>
						';
	foreach($data['species'] as $specie) {
		echo expand_template($speciestemplate, $specie);
	}
?>
	</table>
	Hunt for <input type="number" style="width: 30px;" id="hunthours" /> hours.<br />
	<span class="action" onclick="start_hunt(<?php echo $data['actor_id']; ?>);">Start hunt</span>
</div>
