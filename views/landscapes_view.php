<ul class="selectable" id="landscapes">
	<?php
	foreach ($landscapes as $landscape) {
		$selected = '';
		if(isset($location_resources)) {
			foreach ($location_resources as $location_resource) {
				if($location_resource['Landscape_ID'] == $landscape['ID'])
					$selected = ' selected';
			}
		}
		echo expand_template('<li class="selectable{selected}" id="{element_id}" onclick="toggle_landscape(\'{id}\')">{name}</li>',
			array(
				'selected' => $selected,
				'element_id' => "landscape_" . $landscape['ID'],
				'id' => $landscape['ID'],
				'name' => $landscape['Name']));
	}
	?>
</ul>
