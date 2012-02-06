<ul class="selectable" id="biomes">
	<?php
	foreach ($biomes as $biome) {
		$id = "biome_".$biome['ID'];
		echo '<li class="selectable" id="'.$id.'" onclick="toggle_biome(\''.$id.'\')">'.$biome['Name'].'</li>';
	}
	?>
</ul>
