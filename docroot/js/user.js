function logout()
{
	$('#logout').html('Logging out...');
	$.ajax(
	{
		type: 'GET',
		url: 'user/Logout',
		success: function(data)
		{
			window.location = 'front';
		}
	});
}

var new_actor_processing = false;
function new_actor() {
	if(new_actor_processing == true) {
		$('#new_actor').html('Requesting, please wait...');
		return;
	}
	new_actor_processing = true;
	$('#new_actor').html('Requesting...');
	$.ajax({
		type: 'GET',
		url: '/actor/Request_actor',
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				$('#new_actor').html('Request granted');
				window.location.reload();
			}
			else {
				$('#new_actor').html(data.reason);
				window.location.reload();
			}
		}
	});
}

function Refresh_actors()
{
	$.ajax(
	{
		type: 'GET',
		url: '/actor/Actors',
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			$('#actors').html(data);
		}
	});
}
