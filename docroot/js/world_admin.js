var current_location = 0;
var current_landscape = 1;
var current_species = -1;

function move_map() {
	var x = $('#map_center_x').val();
	var y = $('#map_center_y').val();
	window.location = '/world_admin/map/'+x+'/'+y;
}

function edit_location(id) {
	$.ajax({
		type: 'POST',
		url: 'world_admin/edit_location',
		data: {
			id: id
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data !== false) {
				current_location = id;
				$('#edit_location').html(data.html);
			}
		}
	});
}

function add_biome() {
	$.ajax({
		type: 'POST',
		url: 'world_admin/add_biome',
		data: {
			name: $('#new_biome').val()
		},
		success: function(data){
			if(ajax_logged_out(data)) return;
			if(data !== false){
				$('#biome_list').html(data.html);
			}
		}
	});
}
function add_resource(){
	$.ajax({
		type: 'POST',
		url: 'world_admin/add_resource',
		data: {
			name: $('#new_resource').val(),
			natural: true,
			location_id: current_location,
			landscape_id: current_landscape
		},
		success: function(data){
			if(ajax_logged_out(data)) return;
			if(data !== false){
				$('#resource_list').html(data.data);
			}
		}
	});
}

function add_landscape() {
	$.ajax({
		type: 'POST',
		url: 'world_admin/add_landscape',
		data: {
			name: $('#new_landscape').val(),
			location_id: current_location
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data !== false) {
				$('#landscape_list').html(data.html);
			}
		}
	});
}

function toggle_resource(id) {
	var e = $('#'+id);
	var resource_id = e.attr('id').substr(9);
	if(e.hasClass('selected')) {
		remove_location_resource(resource_id, e);
	} else {
		add_location_resource(resource_id, e);
	}
}

function toggle_biome(id) {
	var e = $('#'+id);
	if(e.hasClass('selected') == false) {
		var biome_id = e.attr('id').substr(6);
		set_location_biome(biome_id, e);
	}
}

function set_location_biome(biome, e) {
	$.ajax({
		type: 'POST',
		url: 'world_admin/set_location_biome',
		data: {
			location: current_location,
			biome: biome
		},
		success: function(data){
			if(ajax_logged_out(data)) return;
			$('#biomes .selected').removeClass('selected');
			e.addClass('selected');
		}
	});
}

function toggle_landscape(landscape) {
	$('#resource_list').html('Loading...');
	$.ajax({
		type: 'POST',
		url: 'world_admin/get_landscape_resources',
		data: {
			location: current_location,
			landscape: landscape
		},
		success: function(data){
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				$('#landscape_'+current_landscape).removeClass('selected');
				current_landscape = landscape;
				$('#landscape_'+current_landscape).addClass('selected');
				$('#resource_list').html(data.html);
			}
		}
	});
}

function add_location_resource(resource, e) {
	$.ajax({
		type: 'POST',
		url: 'world_admin/add_location_resource',
		data: {
			location: current_location,
			landscape: current_landscape,
			resource: resource
		},
		success: function(data){
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				e.addClass('selected');
				$('#landscape_'+current_landscape).addClass('contains_resources');
			}
		}
	});
}
function remove_location_resource(resource, e) {
	$.ajax({
		type: 'POST',
		url: 'world_admin/remove_location_resource',
		data: {
			location: current_location,
			landscape: current_landscape,
			resource: resource
		},
		success: function(data){
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				e.removeClass('selected');
				if(data.landscape_resource_count == 0) {
					$('#landscape_'+current_landscape).removeClass('contains_resources');
				}
			}
		}
	});
}

function set_max_actors() {
	$('#actor_control_feedback').html("Saving...");
	$.ajax({
		type: 'POST',
		url: 'world_admin/set_max_actors',
		data: {
			value: $('#max_actors_input').val()
		},
		success: function(data){
			if(ajax_logged_out(data)) return;
			if(data !== false){
				$('#actor_control_feedback').html("Saved");
			} else {
				$('#actor_control_feedback').html("Failed to save");
			}
		}
	});
}

function set_max_actors_account() {
	$('#actor_control_feedback').html("Saving...");
	$.ajax({
		type: 'POST',
		url: 'world_admin/set_max_actors_account',
		data: {
			value: $('#max_actors_account_input').val()
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data !== false) {
				$('#actor_control_feedback').html("Saved");
			} else {
				$('#actor_control_feedback').html("Failed to save");
			}
		}
	});
}

function add_species() {
	current_species = -1;
	$('#edit_species').show();
	$('#edit_species .panel_header').html("New species");
	$("#species_name").val("");
	$("#species_max_population").val(100);
	$("#species_population").val("");
	$("#species_on_location").removeAttr('checked');
	$("#species_actor_spawn").val("");
}

function edit_species(id) {
	$.ajax({
		type: 'POST',
		url: 'world_admin/get_specie',
		data: {
			id: id,
			location_id: current_location
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success !== false) {
				current_species = id;
				$('#edit_species').show();
				$('#edit_species .panel_header').html("Edit species");
				$("#species_name").val(data.data.Name);
				$("#species_corpse").val(data.data.Corpse_product_ID);
				$("#species_max_population").val(data.data.Max_population);
				if(data.data.Population) {
					$("#species_population").val(data.data.Population);
					$("#species_on_location").attr('checked', 'true');
					$("#species_actor_spawn").val(data.data.Actor_spawn);
				} else {
					$("#species_population").val("");
					$("#species_on_location").removeAttr('checked');
					$("#species_actor_spawn").val("");
				}
			}
		}
	});
}

function save_species() {
	var name = $("#species_name").val();
	var max_population = $("#species_max_population").val();
	var on_location = $("#species_on_location").is(':checked');
	var population = $("#species_population").val();
	var actor_spawn = $("#species_actor_spawn").val();
	var id = current_species;
	$.ajax({
		type: 'POST',
		url: 'world_admin/save_species',
		data: {
			id: id,
			name: name,
			max_population: max_population,
			location_id: current_location,
			on_location: on_location,
			population: population,
			actor_spawn: actor_spawn
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success !== false) {
				$('#species_list').html(data.html);
				$('#edit_species').hide();
			}
		}
	});
}
