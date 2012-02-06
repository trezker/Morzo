
<h3>Edit location <?php echo $location['X']." ".$location['Y'];?></h3>
Biome: <?php echo $location['Biome_name'];?>

<p>Resources</p>
<ul>
<?php
foreach ($location_resources as $resource) {
	echo "<li>".$resource['Name']."</li>";
}
?>
</ul>
<div style="float: left;">
	<h4 style="margin: 0px;">Biomes</h4>
	<div id="biome_list">
		<?php include 'views/biomes_view.php'; ?>
	</div>
	<input type="text" id="new_biome" />
	<br/><span class="action" onclick="add_biome();">Add biome</span>
</div>

<div style="float: left;">
	<h4 style="margin: 0px;">Resources</h4>
	<div id="resource_list">
		<?php include 'views/resources_view.php'; ?>
	</div>
	<input type="text" id="new_resource" />
	<br/><span class="action" onclick="add_resource();">Add resource</span>
</div>
