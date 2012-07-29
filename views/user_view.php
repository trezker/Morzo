<!DOCTYPE html>
<html>
	<head>
		<title>Morzo</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="/js/dialog.js"></script>
		<script type="text/javascript" src="/js/user.js"></script>
	</head>
	<body>
		<p>Hello <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
		<p>
			<?php
			if($actor_limit['Max_actors_reached'] == 0) {
				echo '<span class="action" id="new_actor" onclick="new_actor()">New actor</span>';
			} else {
				echo 'You can not request any more actors.';
			}
			echo '<br />You have '.$actor_limit["Num_actors"].'/'.$actor_limit["Max_actors"].' actors.';
			?>
		</p>
		<div id="actors">
			<ul class="actor_list">
				<?php
				foreach ($actors as $actor) {
					echo expand_template("<li><a href='actor/show_actor/{ID}'>{Name}</a></li>", $actor);
				}
				?>
			</ul>
		</div>

		<p><span class="action" onclick='window.location="user/settings"'>Settings</span></p>
		<p><span class="action" onclick='window.location="blog/Control_panel"'>Blog</span></p>
		<p id="logout"><span class="action" onclick='logout()'>Log out</span></p>
		
		<?php if($_SESSION['admin']===true) { ?>
			<p>You're an Admin!</p>
			<p><span class="action" onclick='window.location="user_admin"'>Users</span></p>
			<p><span class="action" onclick='window.location="world_admin"'>World</span></p>
			<p><span class="action" onclick='window.location="project_admin"'>Projects</span></p>
			<p><span class="action" onclick='window.location="language_admin"'>Translation</span></p>
		<?php } ?>
	</body>
</html>
