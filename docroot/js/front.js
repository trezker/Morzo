$(function () {
	$("#username, #pass").on('keydown', function() {
		$("#login_message").html("&nbsp;");
	});
	
	$("#login").on('click', function() {
		var username = $("#username").val();
		var pass = $("#pass").val();
		$.ajax({
			type		: 'POST',
			dataType   	: 'json',
			contentType	: 'application/json; charset=UTF-8',
			url			: 'user/Login',
			data: {
				username: username,
				pass: pass
			}
		}).done(function(data) {
			if(data.success == true)
				window.location = '/';
			else
				$("#login_message").html("Failed. Please try again.");
		});
	});

	$("#create").on('click', function() {
		var username = $("#username").val();
		var pass = $("#pass").val();
		$.ajax({
			type		: 'POST',
			dataType   	: 'json',
			contentType	: 'application/json; charset=UTF-8',
			url			: 'user/Create_user_password',
			data: {
				username: username,
				pass: pass
			}
		}).done(function(data) {
			if(data.success == true)
				window.location = '/';
			else
				$("#login_message").html(data.reason);
		});
	});
});
