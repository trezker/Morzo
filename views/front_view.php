<html>
	<head>
		<title>Morzo - front</title> 
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.php">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>
		<script type="text/javascript">
			function openid_toggle()
			{
				e = document.getElementById('openid_div');
				if(e.style.display == 'block')
					e.style.display = 'none';
				else
					e.style.display = 'block';
			}
			$(document).ready(function()
			{
				$("#openid_form").validate(
				{
					debug: false,
					rules:
					{
						openid:
						{
							required: true
						}
					},
					messages:
					{
						openid: "A valid openid please.",
					},
					submitHandler: function(form)
					{
						$('#openidfeedback').html('Processing...');
						var openid = document.getElementById('openid').value;
						$.ajax(
						{
							type: 'POST',
							url: 'user/Start_openid_login',
							data: { openid: openid },
							success: function(data)
							{
								if(!data) {
									$('#openidfeedback').html('Process failed');
								} else {
									$('#openidfeedback').html('Redirecting');
									window.location = data;
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
				<span class="user_option" id="openid_link" onclick='openid_toggle()'>Log in</span>
			</div>
			<div style="clear:both;"></div>

			<div class="login" id="openid_div" style="display:none">
				<form name="openid" id="openid_form" action="" method="POST">  
					<div class="login_input">
						OpenID<br/>
						<input type="text" name="openid" id="openid" />
					</div>
					<div class="login_submit">
						<input type="submit" name="submit" value="Log in" />
					</div>
				</form>
				<div id="openidfeedback"></div>
			</div>
		</div>
	</body>
</html>
