<ul class="selectable" id="species">
	<?php
	foreach ($data['species'] as $specie) {
		$selected = '';
		if(isset($data['location_species'])) {
			foreach ($data['location_species'] as $location_specie) {
				if($location_specie['ID'] == $specie['ID'])
					$selected = ' selected';
			}
		}
		echo expand_template('<li class="selectable{selected}" id="specie_{id}" onclick="edit_species(\'{id}\')">{name}</li>',
			array(
				'selected' => $selected,
				'id' => $specie['ID'],
				'name' => $specie['Name']));
	}
	?>
</ul>
