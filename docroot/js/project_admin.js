var current_recipe = -1;
var current_resource = -1;

function edit_recipe(id)
{
	$.ajax(
	{
		type: 'POST',
		url: 'project_admin/edit_recipe',
		data: {
			id: id
		},
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data !== false)
			{
				current_recipe = id;
				$('#edit_recipe').html(data.data);
			}
		}
	});
}

function save_recipe() {
	var recipe_name = $('#recipe_name').val();
	var cycle_time = $('#cycle_time').val();
	var allow_fraction_output = $('#allow_fraction_output').attr('checked');
	var require_full_cycle = $('#require_full_cycle').attr('checked');

	var outputs = new Array();
	$('#recipe_outputs .output').each(
		function(index, value){
			var output = {
					id: value.id,
					amount: $(value).children('.amount').val(),
					resource: $(value).children('.resource').attr('data-id')
				};
			outputs.push(output);
		}
	);

	var inputs = new Array();
	$('#recipe_inputs .input').each(
		function(index, value){
			var input = {
					id: value.id,
					amount: $(value).children('.amount').val(),
					resource: $(value).children('.resource').attr('data-id'),
					from_nature: $(value).children('.from_nature').attr('checked')
				};
			inputs.push(input);
		}
	);

	$.ajax({
		type: 'POST',
		url: 'project_admin/save_recipe',
		data: {
			id: current_recipe,
			name: recipe_name,
			cycle_time: cycle_time,
			allow_fraction_output: allow_fraction_output,
			require_full_cycle: require_full_cycle,
			outputs: outputs,
			inputs: inputs
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data !== false) {
				current_recipe = data.id;
				window.location.reload();
			}
		}
	});
}

/*
 * Recipe output
*/
function select_output_resource(id) {
	$('.output#'+id).prepend($('#resource_select').html())
	$('.output#'+id+' select').change(id, selected_output_resource);
	var resource_id = $('.output#'+id+' .resource').attr('data-id');
	$('.output#'+id+' select').val(resource_id);
	$('.output#'+id+' .resource').css('display', 'none');
}

function selected_output_resource(e) {
	var value = $('.output#'+e.data+' select').val();
	var name = $('.output#'+e.data+' select [value="'+value+'"]').html();

	$('.output#'+e.data+' .resource').html(name);
	$('.output#'+e.data+' .resource').attr('data-id', value);
}

function add_output() {
	$('#recipe_outputs').append($('#new_output').html());
}

function remove_output(id) {
	if(id == -1) {
		$('.output#'+id).remove();
	}
	$.ajax(
	{
		type: 'POST',
		url: 'project_admin/remove_recipe_output',
		data: {
			recipe_id: current_recipe,
			id: id
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data !== false) {
				$('.output#'+id).remove();
			}
		}
	});
}

/*
 * Recipe input
*/
function select_input_resource(id) {
	$('.input#'+id).prepend($('#resource_select').html())
	$('.input#'+id+' select').change(id, selected_input_resource);
	var resource_id = $('.input#'+id+' .resource').attr('data-id');
	$('.input#'+id+' select').val(resource_id);
	$('.input#'+id+' .resource').css('display', 'none');
}

function selected_input_resource(e) {
	var value = $('.input#'+e.data+' select').val();
	var name = $('.input#'+e.data+' select [value="'+value+'"]').html();

	$('.input#'+e.data+' .resource').html(name);
	$('.input#'+e.data+' .resource').attr('data-id', value);
}

function add_input() {
	$('#recipe_inputs').append($('#new_input').html());
}

function remove_input(id) {
	if(id == -1) {
		$('.input#'+id).remove();
	}
	$.ajax(
	{
		type: 'POST',
		url: 'project_admin/remove_recipe_input',
		data: {
			recipe_id: current_recipe,
			id: id
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data !== false) {
				$('.input#'+id).remove();
			}
		}
	});
}

function edit_resource(id) {
	$.ajax({
		type: 'POST',
		url: 'project_admin/edit_resource',
		data: {
			id: id
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data !== false) {
				current_resource = id;
				$('#edit_resource').html(data.data);
			}
		}
	});
}

function save_resource() {
	var id = current_resource;
	var name = $('#resource_name').val();
	var is_natural = $('#is_natural').attr('checked');
	var measure = $('#measure').val();
	var mass = $('#mass').val();
	var volume = $('#volume').val();

	$.ajax({
		type: 'POST',
		url: 'project_admin/save_resource',
		data: {
			id: id,
			name: name,
			is_natural: is_natural,
			measure: measure,
			mass: mass,
			volume: volume
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data !== false) {
				current_resource = id;
				window.location.reload();
			}
			edit_resource(id);
		}
	});
}
