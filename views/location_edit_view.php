<?php
echo "<h3>Edit location ".$location['X']." ".$location['Y']."</h3>";
echo "Biome: ".$location['Biome_name'];

echo "<p>Resources</p>
<ul>";
foreach ($location_resources as $resource) {
	echo "<li>".$resource['Name']."</li>";
}
echo "</ul>";
?>
