var current_recipe = -1;
var current_resource = -1;
var current_product = -1;
var current_category = -1;

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
	var allow_fraction_output = $('#allow_fraction_output').is(':checked');
	var require_full_cycle = $('#require_full_cycle').is(':checked');

	var outputs = new Array();
	$('#recipe_outputs .output').each(
		function(index, value){
			var output = {
					id: $(value).attr('data-id'),
					amount: $(value).children('.amount').val(),
					measure: $(value).children('.measuredesc').attr('data-id'),
					resource: $(value).children('.resource').attr('data-id'),
					remove: $(value).attr('data-remove')
				};
			outputs.push(output);
		}
	);

	var inputs = new Array();
	$('#recipe_inputs .input').each(
		function(index, value){
			var input = {
					id: $(value).attr('data-id'),
					amount: $(value).children('.amount').val(),
					measure: $(value).children('.measuredesc').attr('data-id'),
					resource: $(value).children('.resource').attr('data-id'),
					from_nature: $(value).children('.from_nature').is(':checked'),
					remove: $(value).attr('data-remove')
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
					product: $(value).children('.product').attr('data-id'),
					remove: $(value).attr('data-remove')
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
					remove: $(value).attr('data-remove')
				};
			product_inputs.push(input);
		}
	);

	var tools = new Array();
	$('#recipe_tools .tool').each(
		function(index, value){
			var input = {
					id: $(value).attr('data-id'),
					product: $(value).children('.product').attr('data-id'),
					remove: $(value).attr('data-remove')
				};
			tools.push(input);
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
			product_inputs: product_inputs,
			tools: tools
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data !== false) {
				window.location.reload();
			}
		}
	});
	return false;
}

/*
 * Recipe output
*/
var new_output_id = -1;
function add_output() {
	var resource_id = $('#new_output_form select').val();
	
	$.ajax({
		type: 'POST',
		url: 'project_admin/add_recipe_output',
		data: {
			resource_id: resource_id,
			new_output_id: new_output_id
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success !== false) {
				new_output_id -= 1;
				$("#recipe_outputs").append(data.html);
			}
		}
	});
}

function remove_output(id) {
	if(id < 0) {
		$("#resource_output_"+id).remove();
	} else {
		$("#resource_output_"+id).attr('data-remove', true).hide();
	}
}

/*
 * Recipe input
*/
var new_input_id = -1;
function add_input() {
	var resource_id = $('#new_input_form select').val();
	
	$.ajax({
		type: 'POST',
		url: 'project_admin/add_recipe_input',
		data: {
			resource_id: resource_id,
			new_input_id: new_input_id
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success !== false) {
				new_input_id -= 1;
				$("#recipe_inputs").append(data.html);
			}
		}
	});
}

function remove_input(id) {
	if(id < 0) {
		$("#resource_input_"+id).remove();
	} else {
		$("#resource_input_"+id).attr('data-remove', true).hide();
	}
}

var new_product_output_id = -1;
function add_product_output() {
	var product_id = $('#new_product_output_form select').val();
	
	$.ajax({
		type: 'POST',
		url: 'project_admin/add_recipe_product_output',
		data: {
			product_id: product_id,
			new_product_output_id: new_product_output_id
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success !== false) {
				new_product_output_id -= 1;
				$("#recipe_product_outputs").append(data.html);
			}
		}
	});
}

function remove_product_output(id) {
	if(id < 0) {
		$("#product_output_"+id).remove();
	} else {
		$("#product_output_"+id).attr('data-remove', true).hide();
	}
}

var new_product_input_id = -1;
function add_product_input() {
	var product_id = $('#new_product_input_form select').val();
	var product_name = $('#new_product_input_form select option[value="'+product_id+'"]').html();
	
	$.ajax({
		type: 'POST',
		url: 'project_admin/add_recipe_product_input',
		data: {
			product_id: product_id,
			new_product_input_id: new_product_input_id
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success !== false) {
				new_product_input_id -= 1;
				$("#recipe_product_inputs").append(data.html);
			}
		}
	});
}

function remove_product_input(id) {
	if(id < 0) {
		$("#product_input_"+id).remove();
	} else {
		$("#product_input_"+id).attr('data-remove', true).hide();
	}
}

var new_tool_id = -1;
function add_tool() {
	var product_id = $('#new_tool_form select').val();
	var product_name = $('#new_tool_form select option[value="'+product_id+'"]').html();
	
	$.ajax({
		type: 'POST',
		url: 'project_admin/add_recipe_tool',
		data: {
			product_id: product_id,
			new_tool_id: new_tool_id
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success !== false) {
				new_tool_id -= 1;
				$("#recipe_tools").append(data.html);
			}
		}
	});
}

function remove_tool(id) {
	if(id < 0) {
		$("#tool_"+id).remove();
	} else {
		$("#tool_"+id).attr('data-remove', true).hide();
	}
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
	var is_natural = $('#is_natural').is(':checked');
	var measure = $('#measure').val();
	var mass = $('#mass').val();
	var volume = $('#volume').val();

	var categories = new Array();
	$(".category").each(
		function(index, value){
			var category_id = $(value).attr("data-category_id");
			var category_state = $(value).attr("data-state");
			var category_nutrition = $(value).find("[data-property='nutrition']").val();
			var category = {
					id: category_id,
					state: category_state,
					nutrition: category_nutrition
				};
			categories.push(category);
		}
	);

	$.ajax({
		type: 'POST',
		url: 'project_admin/save_resource',
		data: {
			id: id,
			name: name,
			is_natural: is_natural,
			measure: measure,
			mass: mass,
			volume: volume,
			categories: categories
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data !== false) {
				window.location.reload();
			}
		}
	});
	return false;
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
	
	var categories = new Array();
	$(".category").each(
		function(index, value){
			var category_id = $(value).attr("data-category_id");
			var category_state = $(value).attr("data-state");
			var category_nutrition = $(value).find("[data-property='nutrition']").val();
			var category_mass_limit = $(value).find("[data-property='mass_limit']").val();
			var category_volume_limit = $(value).find("[data-property='volume_limit']").val();
			var category = {
					id: category_id,
					state: category_state,
					nutrition: category_nutrition,
					mass_limit: category_mass_limit,
					volume_limit: category_volume_limit
				};
			categories.push(category);
		}
	);

	$.ajax({
		type: 'POST',
		url: 'project_admin/save_product',
		data: {
			id: id,
			name: name,
			mass: mass,
			volume: volume,
			rot_rate: rot_rate,
			categories: categories
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data !== false) {
				window.location.reload();
			}
		}
	});
	return false;
}

function edit_category(id) {
	$.ajax({
		type: 'POST',
		url: 'project_admin/edit_category',
		data: {
			id: id
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data !== false) {
				current_category = id;
				$('#edit_category').html(data.data);
			}
		}
	});
}

function save_category() {
	var id = current_category;
	var name = $('#category_name').val();

	$.ajax({
		type: 'POST',
		url: 'project_admin/save_category',
		data: {
			id: id,
			name: name
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data !== false) {
				current_category = id;
				window.location.reload();
			}
		}
	});
}

function show_category_container_properties() {
	$("#container_properties_container").toggle();
}

function add_product_category() {
	var category_id = $('#product_category_select').val();

	var existing = $("#category_"+category_id);
	if(existing.length) {
		existing.show().attr("data-state", "");
		return;
	}
	
	$.ajax({
		type: 'POST',
		url: 'project_admin/add_category',
		data: {
			product_id: current_product,
			category_id: category_id,
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data !== false) {
				$("#categorycontainer").append(data.html);
			}
		}
	});
}

function add_resource_category() {
	var category_id = $('#resource_category_select').val();
	
	var existing = $("#category_"+category_id);
	if(existing.length) {
		existing.show().attr("data-state", "");
		return;
	}

	$.ajax({
		type: 'POST',
		url: 'project_admin/add_category',
		data: {
			resource_id: current_resource,
			category_id: category_id,
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data !== false) {
				$("#categorycontainer").append(data.html);
			}
		}
	});
}

function remove_category(category_id) {
	$("#category_"+category_id).hide().attr("data-state", "remove");
}
