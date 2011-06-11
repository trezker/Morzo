<html>
	<head>
		<title>Morzo - front</title> 
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.php">
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>
		<script type="text/javascript">
			function login_toggle()
			{
				var e = document.getElementById('register_div');
				if(e.style.display == 'block')
					e.style.display = 'none';
				e = document.getElementById('login_div');
				if(e.style.display == 'block')
					e.style.display = 'none';
				else
					e.style.display = 'block';
			}
			function register_toggle()
			{
				var e = document.getElementById('login_div');
				if(e.style.display == 'block')
					e.style.display = 'none';
				e = document.getElementById('register_div');
				if(e.style.display == 'block')
					e.style.display = 'none';
				else
					e.style.display = 'block';
			}
		</script>
		<script type="text/javascript">
			$(document).ready(function()
			{
				$("#register").validate(
				{
					debug: false,
					rules:
					{
						email:
						{
							required: true,
							email: true
						}
					},
					messages:
					{
						email: "A valid email please.",
					},
					submitHandler: function(form)
					{
						$('#registerfeedback').html('Processing...');
						var email = document.getElementById('email').value;
						$.ajax(
						{
							type: 'POST',
							url: 'user/RegisterQueue/'+email,
							success: function(data)
							{
								$('#registerfeedback').html(data);
							}
						});
					}
				});
			});
		</script>
		<script type="text/javascript">
			$(document).ready(function()
			{
				$("#login").validate(
				{
					debug: false,
					rules:
					{
						username:
						{
							required: true,
						},
						password:
						{
							required: true,
						}
					},
					messages:
					{
						username: "Required",
						password: "Required",
					},
					submitHandler: function(form)
					{
						$('#loginfeedback').html('Processing...');
						var username = document.getElementById('username').value;
						var password = document.getElementById('password').value;
						$.ajax(
						{
							type: 'POST',
							url: 'user/Login/'+username+'/'+password,
							success: function(data)
							{
								if(data == 1)
								{
									$('#loginfeedback').html('Logged in');
									window.location = 'user';
								}
								else
								{
									$('#loginfeedback').html('Login failed');
								}
							}
						});
					}
				});
			});
		</script>
	</head>
	<body>
		<div class="title">
			<h1>Morzo</h1>
		</div>
		
		<div class="user_menu">
			<div class="user_options">
				<span class="user_option" id="login_link" onclick='login_toggle()'>Log in</span>
				<span id="login_separator"> or </span>
				<span class="user_option" id="register_link" onclick='register_toggle()'>Register</span>
			</div>
			<div style="clear:both;"></div>
			<div class="login" id="login_div" style="display:none">
				<form name="login" id="login" action="" method="POST">
					<div class="login_input">
						Username<br/>
						<input type="text" name="username" id="username" />
					</div>
					<div class="login_input">
						Password<br/>
						<input type="password" name="password" id="password" />
					</div>
					<div class="login_submit">
						<input type="submit" name="loginsubmit" value="Log in" />
					</div>
				</form>
				<div id="loginfeedback">
				</div>
			</div>
			<div class="login" id="register_div" style="display:none">
				<form name="register" id="register" action="" method="POST">  
					<div class="login_input">
						Email<br/>
						<input type="text" name="email" id="email" />
					</div>
					<div class="login_submit">
						<input type="submit" name="submit" value="Register" />
					</div>
				</form>
				<div id="registerfeedback"></div>
			</div>
		</div>
	</body>
</html>
