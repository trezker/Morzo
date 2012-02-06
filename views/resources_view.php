<ul class="selectable" id="resources">
	<?php
	foreach ($resources as $resource) {
		$selected = '';
		if(isset($location_resources)) {
			foreach ($location_resources as $location_resource) {
				if($location_resource['ID'] == $resource['ID'])
					$selected = ' selected';
			}
		}
		$id = "resource_".$resource['ID'];
		echo '<li class="selectable'.$selected.'" id="'.$id.'" onclick="toggle_resource(\''.$id.'\')">'.$resource['Name'].'</li>';
	}
	?>
</ul>
