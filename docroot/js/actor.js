function set_location_changer(change_location_id) {
	$("#location_name_popup").dialog({
		width: 300,
		height: 200,
		modal: true,
		buttons: {
			"Change name": function() {
				change_location_name($("#location_name_popup").attr("data-actor_id"), $("#location_name_popup").attr("data-location_id"), change_location_id);
			},
			Cancel: function() {
				$( this ).dialog("close");
			}
		}
	});
}

function set_actor_changer(change_actor_id) {
	$("#actor_name_popup").dialog({
		width: 300,
		height: 200,
		modal: true,
		buttons: {
			"Change name": function() {
				change_actor_name($("#actor_name_popup").attr("data-actor_id"), change_actor_id);
			},
			Cancel: function() {
				$( this ).dialog("close");
			}
		}
	});
}

function reload_location_list(actor_id)
{
	callurl = '/location/Location_list';
	$.ajax({
		type: 'POST',
		url: callurl,
		data: {actor: actor_id},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success !== false) {
				$('#locations').html(data.data);
			}
		}
	});
}

function change_location_name(actor_id, Location_ID, change_location_id) {
	$('#change_location_name').html('Changing');
	callurl = '/location/Change_location_name';
	$.ajax({
		type: 'POST',
		url: callurl,
		data: {
			actor: actor_id,
			location: change_location_id,
			name: $('#location_input').val()
		},
		dataType: "json",
		success: function(data) {
			if(ajax_logged_out(data)) return;
			$('#change_location_name').html('Change');
			if(data.success == true) {
				window.location.reload();
			}
		}
	});
}

function change_actor_name(actor_id, change_actor_id) {
	callurl = '/actor/Change_actor_name';
	
	$.ajax({
		type: 'POST',
		url: callurl,
		data: {
			actor: actor_id,
			named_actor: change_actor_id,
			name: $('#actor_input').val()
		},
		dataType: "json",
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				window.location.reload();
			}
		}
	});
}

function travel(destination_id, actor_id, Location_ID)
{
	callurl = '/location/Travel';
	$.ajax(
	{
		type: 'POST',
		url: callurl,
		data: {
			actor: actor_id,
			destination: destination_id,
			origin: Location_ID
		},
		dataType: "json",
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data.success) {
				$('#locations_feedback').html("Travelling there.");
				window.location.reload();
			} else {
				$('#locations_feedback').html("Can't travel there.");
			}
		}
	});
}

function cancel_travel(actor_id) {
	$.ajax(
	{
		type: 'POST',
		url: '/location/Cancel_travel',
		data: {
			actor: actor_id,
		},
		dataType: "json",
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data.success) {
				$('#locations_feedback').html("Cancelling.");
				window.location.reload();
			} else {
				$('#locations_feedback').html("Can't cancel now.");
			}
		}
	});
}

function turn_around(actor_id) {
	$.ajax(
	{
		type: 'POST',
		url: '/location/Turn_around',
		data: {
			actor: actor_id,
		},
		dataType: "json",
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data.success) {
				$('#locations_feedback').html("Turning around.");
				window.location.reload();
			} else {
				$('#locations_feedback').html("Can't turn around now.");
			}
		}
	});
}

function load_tab(tab_name, actor_id) {
	$('#tab_content').html("Loading...");
	window.location = '/actor/show_actor/'+actor_id+'/'+tab_name;
}

function speak(actor_id) {
	var message = $('#actor_message').val();
	if(message == '')
		return;
	$('#event_feedback').html("Sending...");
	callurl = '/actor/Speak';
	$.ajax(
	{
		type: 'POST',
		url: callurl,
		data: {
			actor: actor_id,
			message: message
		},
		dataType: "json",
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				window.location.reload();
			} else {
				$('#event_feedback').html(data.reason);
			}
		}
	});
}

function get_natural_resource_dialog(actor_id, resource_id) {
	$.ajax(
	{
		type: 'POST',
		url: '/actor/Natural_resource_dialog',
		data: {
			actor_id: actor_id,
			resource: resource_id
		},
		dataType: "json",
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				$('#natural_resource_dialog').html(data.html);
			}
		}
	});
}

function show_project_start_form(actor_id, id) {
	$.ajax({
		type: 'POST',
		url: '/actor/Start_project_form',
		data: {
			actor_id: actor_id,
			recipe_id: id
		},
		dataType: "json",
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				$('#view_recipe').html(data.data);
				$('#recipes .selected').removeClass('selected');
				$('#recipe_'+id).addClass('selected');
			}
		}
	});
}

function start_project(actor_id, recipe_id) {
	var supply = $('#supply_resources_option').is(':checked');
	var cycles = $('#cycle_count').val();
	$.ajax({
		type: 'POST',
		url: '/actor/Start_project',
		data: {
			actor_id: actor_id,
			recipe_id: recipe_id,
			supply: supply,
			cycles: cycles
		},
		dataType: "json",
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				window.location = "/actor/show_actor/"+actor_id+"/projects";
			}
		}
	});
}

function join_project(actor_id, project_id) {
	$.ajax(
	{
		type: 'POST',
		url: '/actor/Join_project',
		data: {
			actor_id: actor_id,
			project_id: project_id
		},
		dataType: "json",
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				window.location.reload();
			}
		}
	});
}

function leave_project(actor_id) {
	$.ajax(
	{
		type: 'POST',
		url: '/actor/Leave_project',
		data: {
			actor_id: actor_id
		},
		dataType: "json",
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				window.location.reload();
			}
		}
	});
}

function point_at_actor(actor_id, pointee_id) {
	$.ajax(
	{
		type: 'POST',
		url: '/actor/Point_at_actor',
		data: {
			actor_id: actor_id,
			pointee_id: pointee_id
		},
		dataType: "json",
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				window.location = '/actor/show_actor/'+actor_id+'/events';
			}
		}
	});
}

function attack_actor(actor_id, attacked_actor_id) {
	$.ajax(
	{
		type: 'POST',
		url: '/actor/Attack_actor',
		data: {
			actor_id: actor_id,
			attacked_actor_id: attacked_actor_id
		},
		dataType: "json",
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				window.location = '/actor/show_actor/'+actor_id+'/events';
			}
		}
	});
}

whispree_id = -1;
function show_whisper(whispree) {
	$('#whisper_dialog').show();
	whispree_id = whispree;
}

function whisper(actor_id) {
	var message = $('#whisper_message').val();
	if(message == '')
		return;
	$('#whisper_dialog').hide();
	$('#event_feedback').html("Sending...");

	$.ajax(
	{
		type: 'POST',
		url: '/actor/Whisper',
		data: {
			actor_id: actor_id,
			whispree_id: whispree_id,
			message: message
		},
		dataType: "json",
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				window.location = '/actor/show_actor/'+actor_id+'/events';
			}
		}
	});
}

function toggle_recipe_menu() {
	$('#open_recipe_menu').toggle();
	$('#recipe_menu_content').toggle();
}

var project_details_at_id = -1;
function show_project(actor_id, project_id) {
	if(project_details_at_id == project_id) {
		project_details_at_id = -1;
		$('#project_details_row').hide();
		return;
	}
	$.ajax({
		type: 'POST',
		url: '/actor/Show_project_details',
		data: {
			actor_id: actor_id,
			project_id: project_id
		},
		dataType: "json",
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				project_details_at_id = project_id;
				$('#project_details_row').insertAfter($('#project_row_'+project_id)).show();
				$('#project_details_container').html(data.data);
			}
		}
	});
}

var hunt_details_at_id = -1;
function show_hunt(actor_id, hunt_id) {
	if(hunt_details_at_id == hunt_id) {
		hunt_details_at_id = -1;
		$('#hunt_details_row').hide();
		return;
	}
	$.ajax({
		type: 'POST',
		url: '/actor/Show_hunt_details',
		data: {
			actor_id: actor_id,
			hunt_id: hunt_id
		},
		dataType: "json",
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				hunt_details_at_id = hunt_id;
				$('#hunt_details_row').insertAfter($('#hunt_row_'+hunt_id)).show();
				$('#hunt_details_container').html(data.data);
			}
		}
	});
}

function supply_project(actor_id, project_id) {
	$.ajax({
		type: 'POST',
		url: '/actor/Supply_project',
		data: {
			actor_id: actor_id,
			project_id: project_id
		},
		dataType: "json",
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				/*
				project_details_at_id = project_id;
				$('#project_details_row').insertAfter($('#project_row_'+project_id)).show();
				$('#project_details_container').html(data.data);
				*/
				window.location = "/actor/show_actor/"+actor_id+"/projects";
			}
		}
	});
}

function cancel_project(actor_id, project_id) {
	$.ajax({
		type: 'POST',
		url: '/actor/Cancel_project',
		data: {
			actor_id: actor_id,
			project_id: project_id
		},
		dataType: "json",
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				window.location = "/actor/show_actor/"+actor_id+"/projects";
			} else {
				show_dialog({
						title: "Cancel", 
						body: "You can not cancel a project with resources allocated.",
						width: 300,
						height: 150
						});
			}
		}
	});
}

function start_hunt(actor_id) {
	var hours = $('#hunthours').val();
	var species = {};
	
	$(".huntspecies").each(function( index ) {
		var id = $(this).attr('data-species_id');
		species[id] = $(this).val();
	});
	
	$.ajax({
		type: 'POST',
		url: '/actor/Start_hunt',
		data: {
			actor_id: actor_id,
			hours: hours,
			species: species
		},
		dataType: "json",
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				window.location = "/actor/show_actor/"+actor_id+"/projects";
			}
		}
	});
}

function join_hunt(actor_id, hunt_id) {
	$.ajax({
		type: 'POST',
		url: '/actor/Join_hunt',
		data: {
			actor_id: actor_id,
			hunt_id: hunt_id
		},
		dataType: "json",
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				window.location = "/actor/show_actor/"+actor_id+"/projects";
			}
		}
	});
}

function leave_hunt(actor_id, hunt_id) {
	$.ajax({
		type: 'POST',
		url: '/actor/Leave_hunt',
		data: {
			actor_id: actor_id,
			hunt_id: hunt_id
		},
		dataType: "json",
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				window.location = "/actor/show_actor/"+actor_id+"/projects";
			}
		}
	});
}

function enter_object(actor_id, object_id) {
	$.ajax({
		type: 'POST',
		url: '/actor/Enter_object',
		data: {
			actor_id: actor_id,
			object_id: object_id
		},
		dataType: "json",
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				window.location = "/actor/show_actor/"+actor_id+"/locations";
			}
		}
	});
}

function leave_object(actor_id) {
	$.ajax({
		type: 'POST',
		url: '/actor/Leave_object',
		data: {
			actor_id: actor_id
		},
		dataType: "json",
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				window.location = "/actor/show_actor/"+actor_id+"/locations";
			}
		}
	});
}

function transfer_to_inventory(actor_id, target_inventory_id) {
	var products = new Array();
	$('.product_input').each(
		function(index, value) {
			var inventory_id = $(value).attr('data-inventory_id');
			var amount = $(value).val();
			if(! (inventory_id == target_inventory_id || Number(amount) <= 0)) {
				var product = {
						inventory_id: inventory_id,
						product_id: $(value).attr('data-product_id'),
						amount: amount
					};
				products.push(product);
			}
		}
	);
	var resources = new Array();
	$('.resource_input').each(
		function(index, value) {
			var inventory_id = $(value).attr('data-inventory_id');
			var amount = $(value).val();
			if(! (inventory_id == target_inventory_id || Number(amount) <= 0)) {
				var resource = {
						inventory_id: inventory_id,
						resource_id: $(value).attr('data-resource_id'),
						amount: amount
					};
				resources.push(resource);
			}
		}
	);

	$.ajax({
		type: 'POST',
		url: '/actor/Transfer_to_inventory',
		data: {
			actor_id: actor_id,
			inventory_id: target_inventory_id,
			resources: resources,
			products: products
		},
		dataType: "json",
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				window.location = "/actor/show_actor/"+actor_id+"/inventory";
			}
		}
	});
}

function expand_product(e, actor_id, inventory_id, product_id) {
	if($(e).html() == '+') {
		$(e).html('-');
		$.ajax({
			type: 'POST',
			url: '/actor/Expand_inventory_product',
			data: {
				actor_id: actor_id,
				inventory_id: inventory_id,
				product_id: product_id
			},
			dataType: "json",
			success: function(data) {
				if(ajax_logged_out(data)) return;
				if(data.success == true) {
					$('#product_'+inventory_id+'_'+product_id).after(data.html);
				}
			}
		});
	} else {
		$(e).html('+');
		$("[data-object_collection='"+inventory_id+'_'+product_id+"']").remove();
	}
	return false;
}

function open_container(actor_id, inventory_id) {
	$.ajax({
		type: 'POST',
		url: '/actor/Open_container',
		data: {
			actor_id: actor_id,
			inventory_id: inventory_id
		},
		dataType: "json",
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				$('#container_inventories').append(data.html);
			}
		}
	});
	return false;
}

var current_actor_id = -1;
var current_object_id = -1;
function show_object_label_dialog(actor_id, object_id, label) {
	current_actor_id = actor_id;
	current_object_id = object_id;
	$("#object_label_popup").dialog({
		width: 300,
		height: 200,
		modal: true,
		buttons: {
			"Change name": function() {
				var label = $('#label_input').val();
				var dialog_handle = this;
				$.ajax({
					type: 'POST',
					url: '/actor/Label_object',
					data: {
						actor_id: current_actor_id,
						object_id: current_object_id,
						label: label
					},
					dataType: "json",
					success: function(data) {
						if(ajax_logged_out(data)) return;
						if(data.success == true) {
							$('#name_object_' + current_object_id).html(label);
							$( dialog_handle ).dialog("close");
						}
					}
				});
			},
			Cancel: function() {
				$( this ).dialog("close");
			}
		}
	});
	$('#label_input').val(label);
}

var lock_to_attach = false;
var object_to_attach = false;
function attach_lock(actor_id, object_id, lockside) {
	if(lockside) {
		lock_to_attach = object_id;
	} else {
		object_to_attach = object_id;
	}
	if(lock_to_attach && object_to_attach) {
		$.ajax({
			type: 'POST',
			url: '/actor/Attach_lock',
			data: {
				actor_id: actor_id,
				lock_id: lock_to_attach,
				object_id: object_to_attach
			},
			dataType: "json",
			success: function(data) {
				if(ajax_logged_out(data)) return;
				if(data.success == true) {
					//$('#name_object_' + current_object_id).html(label);
					//close_dialog();
					window.location = "/actor/show_actor/"+actor_id+"/inventory";
				}
				lock_to_attach = false;
				object_to_attach = false;
			}
		});
	}
	return false;
}

function detach_lock(actor_id, object_id, lockside) {
	$.ajax({
		type: 'POST',
		url: '/actor/Detach_lock',
		data: {
			actor_id: actor_id,
			object_id: object_id,
			lockside: lockside
		},
		dataType: "json",
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				//$('#name_object_' + current_object_id).html(label);
				//close_dialog();
				window.location = "/actor/show_actor/"+actor_id+"/inventory";
			}
		}
	});
	return false;
}

function lock_object(actor_id, object_id, lockside) {
	$.ajax({
		type: 'POST',
		url: '/actor/Lock_object',
		data: {
			actor_id: actor_id,
			object_id: object_id,
			lockside: lockside
		},
		dataType: "json",
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				//$('#name_object_' + current_object_id).html(label);
				//close_dialog();
				window.location = "/actor/show_actor/"+actor_id+"/inventory";
			}
		}
	});
	return false;
}

function unlock_object(actor_id, object_id, lockside) {
	$.ajax({
		type: 'POST',
		url: '/actor/Unlock_object',
		data: {
			actor_id: actor_id,
			object_id: object_id,
			lockside: lockside
		},
		dataType: "json",
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				//$('#name_object_' + current_object_id).html(label);
				//close_dialog();
				window.location = "/actor/show_actor/"+actor_id+"/inventory";
			}
		}
	});
	return false;
}
