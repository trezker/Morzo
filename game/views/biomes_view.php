<ul class="selectable" id="biomes">
	<?php
	foreach ($data['biomes'] as $biome) {
		if(isset($data['location']) && $data['location']['Biome_ID'] == $biome['ID'])
			$selected = ' selected';
		else
			$selected = '';
		$id = "biome_".$biome['ID'];
		echo sprintf('<li class="selectable%1$s" id="%2$s" onclick="toggle_biome(\'%2$s\')">%3$s</li>',
			$selected, $id, htmlspecialchars($biome['Name']));
	}
	?>
</ul>
