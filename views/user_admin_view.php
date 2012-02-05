<html>
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.php">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript">
			function login_as(id, name)
			{
				$.ajax(
				{
					type: 'POST',
					url: 'user/Login_as',
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
		</script>
	</head>
	<body>
		<h1>User administration</h1>
		<p><span class="action" onclick="window.location = 'front'">Back</span></p>
		<div id="users">
			<?php
			foreach ($users as $user) {
				echo '<li>'.$user['Username'].' <span class="action" onclick="login_as('.$user['ID'].', \''.$user['Username'].'\');">Login as</span></li>';
			}
			?>
		</div>
	</body>
</html>
