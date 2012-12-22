<h2>Edit location <?php echo $location['X']." ".$location['Y'];?></h2>
</ul>
<div style="float: left;">
	<div class="edit_panel">
		<div class="panel_header">Biome</div>
		<div id="biome_list">
			<?php echo $biomes_view; ?>
		</div>
		<input type="text" id="new_biome" />
		<br/><span class="action" onclick="add_biome();">Add biome</span>
	</div>
</div>

<div style="float: left;">
	<div class="edit_panel">
		<div class="panel_header">Landscapes</div>
		<div id="landscape_list">
			<?php echo $landscapes_view; ?>
		</div>
		<input type="text" id="new_landscape" />
		<br/><span class="action" onclick="add_landscape();">Add landscape</span>
	</div>

	<div class="edit_panel">
		<div class="panel_header">Resources</div>
		<div id="resource_list">
		</div>
	</div>
</div>

