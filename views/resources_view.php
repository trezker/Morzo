<ul class="selectable" id="resources">
	<?php
	foreach ($resources as $resource) {
		$selected = '';
		if(isset($location_resources)) {
			foreach ($location_resources as $location_resource) {
				if($location_resource['ID'] == $resource['ID'])
					$selected = ' selected';
			}
		}
		echo expand_template('<li class="selectable{selected}" id="{id}" onclick="toggle_resource(\'{id}\')">{name}</li>',
			array(
				'selected' => $selected,
				'id' => "resource_" . $resource['ID'],
				'name' => $resource['Name']));
	}
	?>
</ul>
