<select>
	<?php
	foreach ($resources as $resource) {
		echo "<option value='".$resource['ID']."'>".$resource['Name']."</option>";
	}
	?>
</select>
