<ul class="selectable" id="landscapes">
	<?php
	foreach ($landscapes as $landscape) {
		$contains_resources = '';
		if(isset($location_resources)) {
			foreach ($location_resources as $location_resource) {
				if($location_resource['Landscape_ID'] == $landscape['ID'])
					$contains_resources = ' contains_resources';
			}
		}
		echo expand_template('<li class="selectable{contains_resources}" id="{element_id}" onclick="toggle_landscape(\'{id}\')">{name}</li>',
			array(
				'contains_resources' => $contains_resources,
				'element_id' => "landscape_" . $landscape['ID'],
				'id' => $landscape['ID'],
				'name' => $landscape['Name']));
	}
	?>
</ul>
