<html>
	<head>
		<title><?= $actor['Name']; ?> - <?= $tab; ?> - Morzo</title> 
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="/js/actor.js">	</script>
		<script type="text/javascript" src="/js/dialog.js">	</script>
	</head>
	<body>
		<table>
			<tr>
				<td>
					Name
				</td>
				<td id="actor_name" class="action" onclick="set_actor_changer(<?=$actor_id?>);">
					<?= $actor['Name']; ?>
				</td>
			</tr>
			<tr>
				<td>
					Location
				</td>
				<td>
					<span id="location_name" class="action" onclick="set_location_changer(<?=$actor['Location_ID']?>);"><?= $actor['Location']; ?></span> [<?= $actor['Biome_name']; ?>]
				</td>
			</tr>
			<tr>
				<td>
					Time
				</td>
				<td>
					<?php echo $time['year'].':'.$time['month'].':'.$time['day'].':'.$time['hour'] ?> (Y:M:D:H)
				</td>
			</tr>
		</table>

		<p id="Leave this actor"><a href='/user'>Leave this actor</a></p>
		
		<ul id="actor_tabs">
			<li <?php if($tab=="events") echo 'class="current"';?> onclick="load_tab('events', <?=$actor_id?>)">
				Events
			</li>
			<li <?php if($tab=="locations") echo 'class="current"';?> onclick="load_tab('locations', <?=$actor_id?>)">
				Locations
			</li>
			<li <?php if($tab=="people") echo 'class="current"';?> onclick="load_tab('people', <?=$actor_id?>)">
				People
			</li>
			<li <?php if($tab=="resources") echo 'class="current"';?> onclick="load_tab('resources', <?=$actor_id?>)">
				Resources
			</li>
		</ul>
		<div id="tab_content" style="clear: both;">
			<?=$tab_view?>
		</div>

		<div id="location_name_popup" style="display: none">
			<div class="popup_background">
				<div class="popup_title">Change location name</div>
				<div class="login_content">
					<input type="text" name="location_input" id="location_input" />
					<span class="action" id="change_location_name" onclick="change_location_name(<?=$actor_id?>,<?=$actor['Location_ID']?>);">Change</span>
				</div>
			</div>
		</div>

		<div id="actor_name_popup" style="display: none">
			<div class="popup_background">
				<div class="popup_title">Change actor name</div>
				<div class="login_content">
					<input type="text" name="actor_input" id="actor_input" />
					<span class="action" id="change_actor_name" onclick="change_actor_name(<?=$actor_id?>);">Change</span>
				</div>
			</div>
		</div>
	</body>
</html>
