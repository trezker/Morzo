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
				<label for="map_center_x">X:</label>
				<input id="map_center_x" type="number" value="<?php echo $center_x; ?>" style="width: 50px; text-align: right;"></input>
				<label for="map_center_y">Y:</label>
				<input id="map_center_y" type="number" value="<?php echo $center_y; ?>" style="width: 50px; text-align: right;"></input>
				<span class="action" onclick="move_map();">Fetch map</span>
				<?php
				$i = 0;
				echo '<table>';
				echo '<tr>';
				for($x=-5; $x <= 5; $x++) {
					echo '<th>'.($center_x+$x).'</th>';
				}
				for($y=-5; $y <= 5; $y++) {
					echo '<tr>';
					for($x=-5; $x <= 5; $x++) {
						if($i < count($locations) && $locations[$i]['X'] == $center_x+$x && $locations[$i]['Y'] == $center_y+$y) {
							$location = $locations[$i];
							$location['deficient_class'] = '';
							if($location['Resource_count'] == 0 || $location['Biome_ID'] == NULL) {
								$location['deficient_class'] = ' deficient_location';
							}
							$location['Biome_single_letter'] = substr($location['Name'], 0, 1);
							if($location['Biome_single_letter'] == "") {
								$location['Biome_single_letter'] = "X";
							}
							echo expand_template(
								'<td><span class="action{deficient_class}" onclick="edit_location({ID});">{Biome_single_letter}</span></td>',
								$location);
							$i++;
						} else {
							echo '<td> </td>';
						}
					}
					echo '<th>'.($center_y+$y).'</th>';
					echo '</tr>';
				}
				echo '</table>';
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
