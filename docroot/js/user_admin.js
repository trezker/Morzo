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
			//window.location = 'user_admin';
		}
	});
}
