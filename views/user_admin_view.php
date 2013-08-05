<!DOCTYPE html>
<html>
	<head>
		<title>User admin - Morzo</title>
		<?php echo $common_head_view; ?>
		<script type="text/javascript" src="/js/user_admin.js"></script>
	</head>
	<body>
		<h1>User administration</h1>
		<p><a class="action" href="/user">Back</a></p>
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
					<td>Max actors {Max_actors}</td>
					<td>
						<input type="number" id="actor_limit{ID}" value="{Max_actors}" />
						<span class="action" onclick="set_user_actor_limit({ID});">Set actor limit</span>
					</td>
					<td>Last active {Last_active}</td>
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
