<!DOCTYPE html>
<html>
	<head>
		<title>Morzo</title> 
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>
		<script type="text/javascript" src="/js/dialog.js"></script>
		<script type="text/javascript">
			$(document).ready(function()
			{
				$("#signup_form").validate(
				{
					debug: false,
					rules:
					{
						username:
						{
							required: true
						}
					},
					messages:
					{
						username: "Required, max 32 characters",
					},
					submitHandler: function(form)
					{
						$('#signupfeedback').html('Processing...');
						var username = document.getElementById('username').value;
						$.ajax(
						{
							type: 'POST',
							url: '/user/Create_user',
							dataType: "json",
							data: { username: username },
							success: function(data)
							{
								if(!data['success']) {
									$('#usernamefeedback').html(data.reason);
								} else {
									$('#usernamefeedback').html('Redirecting');
									window.location = '/user';
								}
							}
						});
					}
				});
			});
		</script>
	</head>
	<body>
		Your openID was not found to have an account.
		Please select a username.
		<div class="login" id="signup_div">
			<form name="signup" id="signup_form" action="" method="POST">  
				<div class="login_input">
					Username<br/>
					<input type="text" name="username" id="username" />
				</div>
				<div class="login_submit">
					<input type="submit" name="submit" value="Sign up" />
				</div>
			</form>
			<div id="signupfeedback"></div>
		</div>
		<p><a href="/front">Go to front page</a></p>
	</body>
</html>
