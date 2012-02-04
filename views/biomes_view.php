<select>
	<?php
	foreach ($biomes as $biome) {
		echo "<option value='".$biome['ID']."'>".$biome['Name']."</option>";
	}
	?>
</select>
