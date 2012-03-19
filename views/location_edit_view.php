<h2>Edit location <?php echo $location['X']." ".$location['Y'];?></h2>
</ul>
<div style="float: left;">
	<h3 class="list_title">Biome</h3>
	<div id="biome_list">
		<?php echo $biomes_view; ?>
	</div>
	<input type="text" id="new_biome" />
	<br/><span class="action" onclick="add_biome();">Add biome</span>
</div>

<div style="float: left;">
	<h3 class="list_title">Landscapes</h3>
	<div id="landscape_list">
		<?php echo $landscapes_view; ?>
	</div>
	<input type="text" id="new_landscape" />
	<br/><span class="action" onclick="add_landscape();">Add landscape</span>
</div>

<div style="float: left;">
	<h3 class="list_title">Resources</h3>
	<div id="resource_list">
		<?php echo $resources_view; ?>
	</div>
	<input type="text" id="new_resource" />
	<br/><span class="action" onclick="add_resource();">Add resource</span>
</div>
