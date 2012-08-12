var current_recipe = -1;
var current_resource = -1;
var current_product = -1;

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
					measure: $(value).children('.measuredesc').attr('data-id'),
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
					measure: $(value).children('.measuredesc').attr('data-id'),
					resource: $(value).children('.resource').attr('data-id'),
					from_nature: $(value).children('.from_nature').attr('checked')
				};
			inputs.push(input);
		}
	);

	var product_outputs = new Array();
	$('#recipe_product_outputs .product_output').each(
		function(index, value){
			var output = {
					id: $(value).attr('data-id'),
					amount: $(value).children('.amount').val(),
					product: $(value).children('.product').attr('data-id')
				};
			product_outputs.push(output);
		}
	);

	var product_inputs = new Array();
	$('#recipe_product_inputs .product_input').each(
		function(index, value){
			var input = {
					id: $(value).attr('data-id'),
					amount: $(value).children('.amount').val(),
					product: $(value).children('.product').attr('data-id'),
				};
			product_inputs.push(input);
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
			inputs: inputs,
			product_outputs: product_outputs,
			product_inputs: product_inputs
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data !== false) {
				current_recipe = data.id;
//				window.location.reload();
			}
		}
	});
}

/*
 * Recipe output
*/
function selected_output_resource(e) {
	var value = $('.output#'+e.data+' select').val();
	var name = $('.output#'+e.data+' select [value="'+value+'"]').html();

	$('.output#'+e.data+' .resource').html(name);
	$('.output#'+e.data+' .resource').attr('data-id', value);
}

function add_output() {
	var resource_id = $('#new_output_form select').val();
	var resource_name = $('#new_output_form select option[value="'+resource_id+'"]').html();
	var measure_id = $('#new_output_form select option[value="'+resource_id+'"]').attr('data-measure');
	var measure_desc = $('#measuredesc_'+measure_id).html();
	
	$.ajax(
	{
		type: 'POST',
		url: 'project_admin/add_recipe_output',
		data: {
			recipe_id: current_recipe,
			resource_id: resource_id,
			measure_id: measure_id
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success !== false) {
				$('#new_output .output').attr("id", data.id);
				$('#new_output .resource').attr("data-id", resource_id);
				$('#new_output .resource').html(resource_name);
				$('#new_output .measuredesc').replaceWith(measure_desc);
				$('#recipe_outputs').append($('#new_output').html());
			}
		}
	});
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
function selected_input_resource(e) {
	var value = $('.input#'+e.data+' select').val();
	var name = $('.input#'+e.data+' select [value="'+value+'"]').html();

	$('.input#'+e.data+' .resource').html(name);
	$('.input#'+e.data+' .resource').attr('data-id', value);
}

function add_input() {
	var resource_id = $('#new_input_form select').val();
	var resource_name = $('#new_input_form select option[value="'+resource_id+'"]').html();
	var measure_id = $('#new_input_form select option[value="'+resource_id+'"]').attr('data-measure');
	var measure_desc = $('#measuredesc_'+measure_id).html();
	
	$.ajax(
	{
		type: 'POST',
		url: 'project_admin/add_recipe_input',
		data: {
			recipe_id: current_recipe,
			resource_id: resource_id,
			measure_id: measure_id
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success !== false) {
				$('#new_input .input').attr("id", data.id);
				$('#new_input .resource').attr("data-id", resource_id);
				$('#new_input .resource').html(resource_name);
				$('#new_input .measuredesc').replaceWith(measure_desc);
				$('#recipe_inputs').append($('#new_input').html());
			}
		}
	});
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

function edit_product(id) {
	$.ajax({
		type: 'POST',
		url: 'project_admin/edit_product',
		data: {
			id: id
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data !== false) {
				current_product = id;
				$('#edit_product').html(data.data);
			}
		}
	});
}

function save_product() {
	var id = current_product;
	var name = $('#product_name').val();
	var mass = $('#mass').val();
	var volume = $('#volume').val();
	var rot_rate = $('#rot_rate').val();

	$.ajax({
		type: 'POST',
		url: 'project_admin/save_product',
		data: {
			id: id,
			name: name,
			mass: mass,
			volume: volume,
			rot_rate: rot_rate
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data !== false) {
				current_product = id;
				window.location.reload();
			}
			edit_product(id);
		}
	});
}

function add_product_output() {
	var product_id = $('#new_product_output_form select').val();
	var product_name = $('#new_product_output_form select option[value="'+product_id+'"]').html();
	$('#new_product_output .product').attr("data-id", product_id);
	$('#new_product_output .product').html(product_name);
	$('#recipe_product_outputs').append($('#new_product_output').html());
}

function remove_product_output(id) {
	if(id == -1) {
		$('#product_output_'+id).remove();
	} else {
		$.ajax({
			type: 'POST',
			url: 'project_admin/remove_recipe_product_output',
			data: {
				recipe_id: current_recipe,
				id: id
			},
			success: function(data) {
				if(ajax_logged_out(data)) return;
				if(data !== false) {
					$('#product_output_'+id).remove();
				}
			}
		});
	}
}

function add_product_input() {
	var product_id = $('#new_product_input_form select').val();
	var product_name = $('#new_product_input_form select option[value="'+product_id+'"]').html();
	$('#new_product_input .product').attr("data-id", product_id);
	$('#new_product_input .product').html(product_name);
	$('#recipe_product_inputs').append($('#new_product_input').html());
}

function remove_product_input(id) {
	if(id == -1) {
		$('#product_input_'+id).remove();
	} else {
		$.ajax({
			type: 'POST',
			url: 'project_admin/remove_recipe_product_input',
			data: {
				recipe_id: current_recipe,
				id: id
			},
			success: function(data) {
				if(ajax_logged_out(data)) return;
				if(data !== false) {
					$('#product_input_'+id).remove();
				}
			}
		});
	}
}
