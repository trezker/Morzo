<html>
	<head>
		<title>User admin - Morzo</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="/js/user_admin.js"></script>
	</head>
	<body>
		<h1>User administration</h1>
		<p><span class="action" onclick="window.location = 'user'">Back</span></p>
		<div id="users">
			<?php
			foreach ($users as $user) {
				echo '
					<li>'
						.$user['Username'].
						' <span class="action" onclick="login_as('.$user['ID'].', \''.$user['Username'].'\');">Login as</span>
						<span class="action" onclick="kick_user('.$user['ID'].');">Kick</span>
					</li>';
			}
			?>
		</div>
	</body>
</html>
