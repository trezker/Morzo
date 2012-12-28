<h2>Edit location <?php echo $location['X']." ".$location['Y'];?></h2>

<div class="edit_panel" style="float: left;">
	<div class="panel_header">Biome</div>
	<div id="biome_list">
		<?php echo $biomes_view; ?>
	</div>
	<input type="text" id="new_biome" />
	<br/><span class="action" onclick="add_biome();">Add biome</span>
</div>

<div class="edit_panel" style="float: left;">
	<div class="panel_header">Landscapes</div>
	<div id="landscape_list">
		<?php echo $landscapes_view; ?>
	</div>
	<input type="text" id="new_landscape" />
	<br/><span class="action" onclick="add_landscape();">Add landscape</span>
</div>

<div class="edit_panel" style="float: left;">
	<div class="panel_header">Resources</div>
	<div id="resource_list">
	</div>
</div>

<div class="edit_panel" style="float: left; clear: both;">
	<div class="panel_header">Animal species</div>
	<div id="species_list">
		<?php echo $species_view; ?>
	</div>
	<span class="action" onclick="add_species();">Add species</span>
</div>
<div class="edit_panel" id="edit_species" style="display: none; float: left; clear: both;">
	<div class="panel_header">Edit species</div>
	Name: <input type="text" id="species_name" /><br />
	Max population: <input type="number" id="species_max_population" /><br />
	On location: <input type="checkbox" id="species_on_location" /><br />
	Local population: <input type="number" id="species_population" /><br />
	<span class="action" onclick="save_species()">Save species</span>
	<br>
	<p>
	Note: Max population changes for all locations. <br />
	It means how many there can be on each location the species exist.
	</p>
	On location sets whether it exists on this location.
</div>
