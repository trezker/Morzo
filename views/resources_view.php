<ul class="selectable" id="resources">
	<?php
	foreach ($resources as $resource) {
		$id = "resource_".$resource['ID'];
		echo '<li class="selectable" id="'.$id.'" onclick="toggle_resource(\''.$id.'\')">'.$resource['Name'].'</li>';
	}
	?>
</ul>
