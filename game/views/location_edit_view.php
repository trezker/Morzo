<h2>Edit location <?php echo $data['location']['X']." ".$data['location']['Y'];?></h2>

<div class="edit_panel" style="float: left;">
	<div class="panel_header">Biome</div>
	<div id="biome_list">
		<?php echo $view_factory->Load_view($data['biomes_view']['view'], $data['biomes_view']['data']); ?>
	</div>
	<input type="text" id="new_biome" />
	<br/><span class="action" onclick="add_biome();">Add biome</span>
</div>

<div class="edit_panel" style="float: left;">
	<div class="panel_header">Landscapes</div>
	<div id="landscape_list">
		<?php echo $view_factory->Load_view($data['landscapes_view']['view'], $data['landscapes_view']['data']); ?>
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
		<?php echo $view_factory->Load_view($data['species_view']['view'], $data['species_view']['data']); ?>
	</div>
	<span class="action" onclick="add_species();">Add species</span>
</div>
	<?php
	$corpse_select = '<select id="species_corpse">';
	$corpseoption_template = '
		<option value="{ID}">{Name}</option>
	';
	foreach ($data['corpse_products'] as $corpse) {
		$corpse_select .= expand_template($corpseoption_template, $corpse);
	}
	$corpse_select .= '</select>';
	?>
<div class="edit_panel" id="edit_species" style="display: none; float: left; clear: both;">
	<div class="panel_header">Edit species</div>
	Name: <input type="text" id="species_name" /><br />
	Corpse: <?php echo $corpse_select; ?><br />
	Max population: <input type="number" id="species_max_population" /><br />
	On location: <input type="checkbox" id="species_on_location" /><br />
	Local population: <input type="number" id="species_population" /><br />
	Actor spawn: <input type="number" id="species_actor_spawn" /><br />
	<span class="action" onclick="save_species()">Save species</span>
	<br />
	<p>
	Note: Max population changes for all locations. <br />
	It means how many there can be on each location the species exist.
	</p>
	<p>
	On location sets whether it exists on this location.
	</p>
	<p>
	Corpse is a product, this needs to be created on project admin page and must have the Corpse category.
	</p>
	<p>
	Actor spawn means that actors of this species will be spawned in this location if the population is less than the given number.
	</p>
</div>
