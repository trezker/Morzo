<ul class="selectable" id="landscapes">
	<?php
	foreach ($data['landscapes'] as $landscape) {
		$contains_resources = '';
		if(isset($data['location_resources'])) {
			foreach ($data['location_resources'] as $location_resource) {
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
