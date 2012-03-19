var current_location = 0;
var current_landscape = 1;

function edit_location(id)
{
	$.ajax(
	{
		type: 'POST',
		url: 'world_admin/edit_location',
		data: {
			id: id
		},
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data !== false)
			{
				current_location = id;
				$('#edit_location').html(data.data);
			}
		}
	});
}
function add_biome()
{
	$.ajax(
	{
		type: 'POST',
		url: 'world_admin/add_biome',
		data: {
			name: $('#new_biome').val()
		},
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data !== false)
			{
				$('#biome_list').html(data.data);
			}
		}
	});
}
function add_resource()
{
	$.ajax(
	{
		type: 'POST',
		url: 'world_admin/add_resource',
		data: {
			name: $('#new_resource').val(),
			natural: true
		},
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data !== false)
			{
				$('#resource_list').html(data.data);
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
	$.ajax(
	{
		type: 'POST',
		url: 'world_admin/set_location_biome',
		data: {
			location: current_location,
			biome: biome
		},
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			$('#biomes .selected').removeClass('selected');
			e.addClass('selected');
		}
	});
}

function toggle_landscape(landscape) {
	$('#resource_list').html('Loading...');
	$.ajax(
	{
		type: 'POST',
		url: 'world_admin/get_landscape_resources',
		data: {
			location: current_location,
			landscape: landscape
		},
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				$('#landscape_'+current_landscape).removeClass('selected');
				current_landscape = landscape;
				$('#landscape_'+current_landscape).addClass('selected');
				$('#resource_list').html(data.data);
			}
		}
	});
}

function add_location_resource(resource, e) {
	$.ajax(
	{
		type: 'POST',
		url: 'world_admin/add_location_resource',
		data: {
			location: current_location,
			landscape: current_landscape,
			resource: resource
		},
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				e.addClass('selected');
				$('#landscape_'+current_landscape).addClass('contains_resources');
			}
		}
	});
}
function remove_location_resource(resource, e) {
	$.ajax(
	{
		type: 'POST',
		url: 'world_admin/remove_location_resource',
		data: {
			location: current_location,
			landscape: current_landscape,
			resource: resource
		},
		success: function(data)
		{
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
	$.ajax(
	{
		type: 'POST',
		url: 'world_admin/set_max_actors',
		data: {
			value: $('#max_actors_input').val()
		},
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data !== false)
			{
				$('#actor_control_feedback').html("Saved");
			} else {
				$('#actor_control_feedback').html("Failed to save");
			}
		}
	});
}
