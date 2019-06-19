<!DOCTYPE html>
<html>
	<head>
		<title>Morzo</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<?php echo $view_factory->Load_view('common_head_view'); ?>
		<script type="text/javascript" src="/js/user.js"></script>
	</head>
	<body>
		<p>Hello <?php echo htmlspecialchars($data['username']); ?>!</p>
		<p>
			<?php
			if($data['actor_limit']['Max_actors_reached'] == 0) {
				echo '<span class="action" id="new_actor" onclick="new_actor()">New actor</span>';
			} else {
				echo 'You can not request any more actors.';
			}
			echo '<br />You have '.$data['actor_limit']["Num_actors"].'/'.$data['actor_limit']["Max_actors"].' actors.';
			?>
		</p>
		<div id="actors">
			<ul class="actor_list">
				<?php
				if($data['actors']) {
					foreach ($data['actors'] as $actor) {
						echo expand_template("<li><a href='actor/show_actor/{ID}'>{Name}</a></li>", $actor);
					}
				}
				?>
			</ul>
		</div>

		<p><a href="/library">Documentation</a></p>
		<p><a href="/user/settings">Settings</a></p>
		<p><a href="/blog/Control_panel">Blog</a></p>
		<p id="logout"><a href="javascript:logout()">Log out</a></p>
		
		<?php if($data['admin']===true) { ?>
			<p>You're an Admin!</p>
			<p><a href="/user_admin">Users</a></p>
			<p><a href="/world_admin">World</a></p>
			<p><a href="/project_admin">Projects</a></p>
			<p><a href="/language_admin">Translation</a></p>
		<?php } ?>
	</body>
</html>
