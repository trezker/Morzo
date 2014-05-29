<ul class="selectable" id="resources">
	<?php
	foreach ($data['resources'] as $resource) {
		$selected = '';
		if(isset($data['location_resources'])) {
			foreach ($data['location_resources'] as $location_resource) {
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
