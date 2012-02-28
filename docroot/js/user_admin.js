function login_as(id, name)
{
	$.ajax(
	{
		type: 'POST',
		url: 'user_admin/Login_as',
		data: {
			id: id,
			username: name
		},
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			window.location = 'user';
		}
	});
}

function kick_user(id)
{
	$.ajax(
	{
		type: 'POST',
		url: 'user_admin/Kick_user',
		data: {
			id: id
		},
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			window.location = '/user_admin';
		}
	});
}

function ban_user(id)
{
	var ban_to_date = $('#ban_to_date'+id).val();
	$.ajax(
	{
		type: 'POST',
		url: 'user_admin/Ban_user',
		data: {
			id: id,
			to_date: ban_to_date
		},
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			window.location = '/user_admin';
		}
	});
}
