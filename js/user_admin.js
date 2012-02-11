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
			//window.location = 'user_admin';
		}
	});
}
