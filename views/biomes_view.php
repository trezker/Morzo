<ul class="selectable" id="biomes">
	<?php
	foreach ($biomes as $biome) {
		if(isset($location) && $location['Biome_ID'] == $biome['ID'])
			$selected = ' selected';
		else
			$selected = '';
		$id = "biome_".$biome['ID'];
		sprintf('<li class="selectable%1$s" id="%2$d" onclick="toggle_biome(\'%2$d\')">%3$s</li>',
			$selected, $id, htmlspecialchars($biome['Name']));
	}
	?>
</ul>
