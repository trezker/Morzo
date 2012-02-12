<h2>Edit location <?php echo $location['X']." ".$location['Y'];?></h2>
</ul>
<div style="float: left;">
	<h3 class="list_title">Biome</h3>
	<div id="biome_list">
		<?php include '../views/biomes_view.php'; ?>
	</div>
	<input type="text" id="new_biome" />
	<br/><span class="action" onclick="add_biome();">Add biome</span>
</div>

<div style="float: left;">
	<h3 class="list_title">Resources</h3>
	<div id="resource_list">
		<?php include '../views/resources_view.php'; ?>
	</div>
	<input type="text" id="new_resource" />
	<br/><span class="action" onclick="add_resource();">Add resource</span>
</div>
