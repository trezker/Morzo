<!DOCTYPE html>
<html>
	<head>
		<title>User admin - Morzo</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="/js/user_admin.js"></script>
		<script type="text/javascript" src="/js/dialog.js">	</script>
	</head>
	<body>
		<h1>User administration</h1>
		<p><a class="action" href="user">Back</a></p>
		<div id="users">
			<table class="user_admin_list">
			<?php
			$template = '
				<tr class="{alternate}">
					<td>{Username}</td>
					<td><span class="action" onclick="login_as({ID}, \'{Username}\');">Login as </span></td>
					<td><span class="action" onclick="kick_user({ID});">Kick</span></td>
					<td>{Banned_text}</td>
					<td><input type="text" id="ban_to_date{ID}" value="" /><span class="action" onclick="ban_user({ID});">Set ban</span></td>
				</tr>';
			$alternate = '';
			foreach ($users as $user) {
				$alternate = ($alternate == 'alternate1')? 'alternate2': 'alternate1';
				$user['Banned_text'] = '';
				if($user['Banned_from'] != NULL) {
					$user['Banned_text'] = "Banned from ".$user['Banned_from'];
					if($user['Banned_to'] != NULL) {
						$user['Banned_text'] .= " to ".$user['Banned_to'].".";
					} else {
						$user['Banned_text'] .= " indefinitely.";
					}
				}
				$user['alternate'] = $alternate;
				echo expand_template($template, $user);
			}
			?>
			</table>
		</div>
	</body>
</html>
