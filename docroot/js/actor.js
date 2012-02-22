var change_location_id = -1;
function set_location_changer(location_id)
{
	if(change_location_id == location_id || location_id == -1)
	{
		$('#edit_popup').html('');
		$('#changelink_'+change_location_id).html('Change name');
		change_location_id = -1;
		return;
	}
	else
	{
		$('#changelink_'+change_location_id).html('Change name');
		$('#changelink_'+location_id).html('See name changer');
	}
	change_location_id = location_id;
	open_dialog($('#location_name_popup').html());
}

var change_actor_id = -1;
function set_actor_changer(actor_id)
{
	if(change_actor_id == actor_id || actor_id == -1)
	{
		$('#edit_popup').html('');
		$('#changeactorname_'+change_actor_id).html('Change name');
		change_actor_id = -1;
		return;
	}
	else
	{
		$('#changeactorname_'+change_actor_id).html('Change name');
		$('#changeactorname_'+actor_id).html('See name changer');
	}
	change_actor_id = actor_id;
	open_dialog($('#actor_name_popup').html());
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

function change_location_name(actor_id, Location_ID)
{
	$('#change_location_name').html('Changing');
	callurl = '/location/Change_location_name';
	$.ajax(
	{
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
			set_location_changer(-1);
		}
	});
}

function change_actor_name(actor_id)
{
	$('#change_actor_name').html('Changing');
	callurl = '/actor/Change_actor_name';
	
	$.ajax(
	{
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
			$('#change_actor_name').html('Change');
			if(data.success == true) {
				window.location.reload();
			}
			set_actor_changer(-1);
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
			actor: actor_id,
			resource: resource_id
		},
		dataType: "json",
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				$('#natural_resource_dialog').html(data.data);
			}
		}
	});
}
