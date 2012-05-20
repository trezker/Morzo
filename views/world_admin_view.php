<!DOCTYPE html>
<html>
	<head>
		<title>World admin - Morzo</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="/js/world_admin.js"></script>
		<script type="text/javascript" src="/js/dialog.js"></script>
	</head>
	<body>
		<h1>World administration</h1>
		<p><span class="action" onclick="window.location = 'user'">Back</span></p>

		<div class="locations">
			<h2>Locations (Deficient=red)</h2>
			<div id="locations">
				<?php
				foreach ($locations as $location) {
					$location['deficient_class'] = '';
					if($location['Resource_count'] == 0 || $location['Biome_ID'] == NULL) {
						$location['deficient_class'] = ' deficient_location';
					}
					echo expand_template(
						'<li><span class="action{deficient_class}" onclick="edit_location({ID});">{X} {Y}</span></li>',
						$location);
				}
				?>
			</div>
		</div>

		<div id="edit_location" style="float: left;">
		</div>
		<div id="actor_control">
			Maximum number of actors in the world
			<input name="max_actors_input" id="max_actors_input" type="text" value="<?php echo $max_actors;?>" />
			<span class="action" onclick="set_max_actors();">Update</span>

			<div></div>

			Default max actors for new accounts
			<input name="max_actors_account_input" id="max_actors_account_input" type="text" value="<?php echo $max_actors_account;?>" />
			<span class="action" onclick="set_max_actors_account();">Update</span>

			<div id="actor_control_feedback"></div>
		</div>
	</body>
</html>
