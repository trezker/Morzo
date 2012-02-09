<html>
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="/js/actor.js">	</script>
	</head>
	<body>
		<p>
			Viewing <span id="actor_name"><?= $actor['Name']; ?></span>
			<span id='changeactorname_<?=$actor_id?>' class="action namechange" onclick="set_actor_changer(<?=$actor_id?>);">Change name</span>
		</p>
		<p>
			Current location is <span id="location_name"><?= $actor['Location']; ?></span>
			<span id='changelink_<?=$actor['Location_ID']?>' class="action namechange" onclick="set_location_changer(<?=$actor['Location_ID']?>);">Change name</span>
		</p>
		<div id="edit_popup">
		</div>
		
		
		<ul id="actor_tabs">
			<li class="current" onclick="load_tab('locations', <?=$actor_id?>)">Locations</li>
			<li onclick="load_tab('people', <?=$actor_id?>)">People</li>
		</ul>
		<div id="tab_content" style="clear: both;"></div>

		<div id="location_name_popup" style="display: none">
			<h3>Change location name</h3>
			<input type="text" name="location_input" id="location_input" />
			<span class="action" id="change_location_name" onclick="change_location_name(<?=$actor_id?>,<?=$actor['Location_ID']?>);">Change</span>
		</div>

		<div id="actor_name_popup" style="display: none">
			<h3>Change actor name</h3>
			<input type="text" name="actor_input" id="actor_input" />
			<span class="action" id="change_actor_name" onclick="change_actor_name(<?=$actor_id?>);">Change</span>
		</div>

		<p id="Leave this actor"><a href='/user'>Leave this actor</a></p>
	</body>
</html>
