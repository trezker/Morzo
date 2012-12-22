<ul class="selectable" id="species">
	<?php
	foreach ($species as $specie) {
		$selected = '';
		if(isset($location_species)) {
			foreach ($location_species as $location_specie) {
				if($location_specie['ID'] == $specie['ID'])
					$selected = ' selected';
			}
		}
		echo expand_template('<li class="selectable{selected}" id="{id}" onclick="edit_specie(\'{id}\')">{name}</li>',
			array(
				'selected' => $selected,
				'id' => "specie_" . $specie['ID'],
				'name' => $specie['Name']));
	}
	?>
</ul>
