<!DOCTYPE html>
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
		<p><a class="action" href="user">Back</a></p>
		<div id="users">
			<?php
			$template = '<li>' . 
				'{Username}' . 
				'<span class="action" onclick="login_as({ID}, \'{Username}\');">Login as </span>' .
				'<span class="action" onclick="kick_user({ID});">Kick</span>' .
				'</li>';
			foreach ($users as $user) {
				echo expand_template($template, $user);
			}
			?>
		</div>
	</body>
</html>
