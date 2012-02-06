<ul class="selectable" id="biomes">
	<?php
	foreach ($biomes as $biome) {
		if(isset($location) && $location['Biome_ID'] == $biome['ID'])
			$selected = ' selected';
		else
			$selected = '';
		$id = "biome_".$biome['ID'];
		echo '<li class="selectable'.$selected.'" id="'.$id.'" onclick="toggle_biome(\''.$id.'\')">'.$biome['Name'].'</li>';
	}
	?>
</ul>
