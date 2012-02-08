<html>
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript">
			var change_location_id = -1;
			function set_location_changer(location_id)
			{
				if(change_location_id == location_id || location_id == -1)
				{
					$('#edit_popup').html('');
					$('#changelink_'+change_location_id).html('Change name');
					change_location_id = -1;
					return;
				}
				else
				{
					$('#changelink_'+change_location_id).html('Change name');
					$('#changelink_'+location_id).html('See name changer');
				}
				change_location_id = location_id;
				$('#edit_popup').html($('#location_name_popup').html());
			}

			var change_actor_id = -1;
			function set_actor_changer(actor_id)
			{
				if(change_actor_id == actor_id || actor_id == -1)
				{
					$('#edit_popup').html('');
					$('#changeactorname_'+change_actor_id).html('Change name');
					change_actor_id = -1;
					return;
				}
				else
				{
					$('#changeactorname_'+change_actor_id).html('Change name');
					$('#changeactorname_'+actor_id).html('See name changer');
				}
				change_actor_id = actor_id;
				$('#edit_popup').html($('#actor_name_popup').html());
			}
			
			function reload_location_list()
			{
				callurl = '/location/Location_list';
				$.ajax({
					type: 'POST',
					url: callurl,
					data: {actor: <?=$actor_id?>},
					success: function(data) {
						if(data.success !== false) {
							$('#locations').html(data.data);
						}
					}
				});
			}

			function reload_actor_list() {
				callurl = '/actor/Actor_list';
				$.ajax({
					type: 'POST',
					url: callurl,
					data: {actor: <?=$actor_id?>},
					success: function(data) {
						if(data !== false) {
							$('#actors').html(data.data);
						}
					}
				});
			}
			
			function change_location_name()
			{
				$('#change_location_name').html('Changing');
				callurl = '/location/Change_location_name';
				$.ajax(
				{
					type: 'POST',
					url: callurl,
					data: {
						actor: <?=$actor_id?>,
						location: change_location_id,
						name: $('#location_input').val()
					},
					dataType: "json",
					success: function(data)
					{
						$('#change_location_name').html('Change');
						if(data.success == true)
						{
							if(change_location_id == <?=$actor['Location_ID']?>)
							{
								$('#location_name').html(data.data);
							}
							else
							{
								reload_location_list();
							}
						}
						set_location_changer(-1);
					}
				});
			}

			function change_actor_name()
			{
				$('#change_actor_name').html('Changing');
				callurl = '/actor/Change_actor_name';
				
				$.ajax(
				{
					type: 'POST',
					url: callurl,
					data: {
						actor: <?=$actor_id?>,
						named_actor: change_actor_id,
						name: $('#actor_input').val()
					},
					dataType: "json",
					success: function(data) {
						$('#change_actor_name').html('Change');
						if(data.success == true)
						{
							if(change_actor_id == <?=$actor_id?>)
							{
								$('#actor_name').html(data.data);
							}
							else
							{
								reload_actor_list();
							}
						}
						set_actor_changer(-1);
					}
				});
			}
			
			function travel(destination_id)
			{
				callurl = '/location/Travel';
				$.ajax(
				{
					type: 'POST',
					url: callurl,
					data: {
						actor: <?=$actor_id?>,
						destination: destination_id,
						origin: <?=$actor['Location_ID']?>
					},
					dataType: "json",
					success: function(data)
					{
						if(data.success) {
							$('#locations_feedback').html("Travelling there.");
							window.location.reload();
						} else {
							$('#locations_feedback').html("Can't travel there.");
						}
					}
				});
			}
		</script>
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

<?php if($travel) { ?>
		Travelling from <?=$travel['OriginName']?> to <?=$travel['DestinationName']?>.
<?php } else { ?>
		<h2>Locations you can go to</h2>
		<div id="locations_feedback"></div>
		<div id="locations">
			<?php include 'views/locations_view.php'; ?>
		</div>
<?php } ?>


		<h2>Actors you can see</h2>
		<div id="actors_feedback"></div>
		<div id="actors">
			<?php include 'views/location_actors_view.php'; ?>
		</div>

		<div id="location_name_popup" style="display: none">
			<h3>Change location name</h3>
			<input type="text" name="location_input" id="location_input" />
			<span class="action" id="change_location_name" onclick="change_location_name();">Change</span>
		</div>

		<div id="actor_name_popup" style="display: none">
			<h3>Change actor name</h3>
			<input type="text" name="actor_input" id="actor_input" />
			<span class="action" id="change_actor_name" onclick="change_actor_name();">Change</span>
		</div>

		<p id="Leave this actor"><a href='/user'>Leave this actor</a></p>
	</body>
</html>
