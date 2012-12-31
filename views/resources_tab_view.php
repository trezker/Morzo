<table>
	<?php
	$resource_template = '
				<td class="action" onclick="get_natural_resource_dialog({actor_id}, {ID})">
					{Name}
				</td>
			';
	$resourcecellmap = array();
	foreach ($resources as $resource) {
		$resource['actor_id'] = $actor_id;
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
	<div>Hunt wild Animals</div>
	<table>
		<tr>
			<th>
				Include
			</th>
			<th>
				Amount
			</th>
		</tr>
<?php
	$speciestemplate = '<tr>
							<td><input type="checkbox" id="huntcheck_{ID}" />{Name}</td>
							<td><input type="text" id="huntamount_{ID}" style="width: 30px;" /></td>
						</tr>
						';
	foreach($species as $specie) {
		echo expand_template($speciestemplate, $specie);
	}
?>
	</table>
	<span class="action">Start hunt</span>
</div>
