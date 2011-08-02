<html>
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.php">
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.2.min.js"></script>
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
			
			function reload_location_list()
			{
				callurl = '/location/Location_list';
				$.ajax(
				{
					type: 'POST',
					url: callurl,
					data: {actor: <?=$actor_id?>},
					success: function(data)
					{
						if(data !== false)
						{
							$('#locations').html(data);
						}
					}
				});
			}
			
			function change_location_name()
			{
				$('#change_location_name').html('Changing');
				callurl = '/location/Change_location_name/' + <?=$actor_id?> + '/' + change_location_id + '/' + $('#location_input').val();
				$.ajax(
				{
					type: 'GET',
					url: callurl,
					success: function(data)
					{
						$('#change_location_name').html('Change');
						if(change_location_id == <?=$actor['Location_ID']?>)
						{
							if(data !== false)
							{
								$('#location_name').html(data);
							}
						}
						else
						{
							reload_location_list();
						}
						set_location_changer(-1);
					}
				});
			}
			
			function travel(destination_id)
			{
//				$('#change_actor_name').html('Changing');
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
							$('#locations').html("Can go there.");
						} else {
							$('#locations').html("Can't go there.");
						}
					}
				});
			}
			
			function change_actor_name(actor_id)
			{
				$('#change_actor_name').html('Changing');
				callurl = '/actor/Change_actor_name/' + <?=$actor_id?> + '/' + actor_id + '/' + $('#name_input').val();
				$.ajax(
				{
					type: 'GET',
					url: callurl,
					success: function(data)
					{
						$('#change_actor_name').html('Change');
						if(data !== false)
						{
							$('#actor_name').html(data);
						}
					}
				});
			}
		</script>
	</head>
	<body>
		<p>
			Viewing <span id="actor_name"><?= $actor['Name']; ?></span>
			<input type="text" name="name_input" id="name_input" />
			<span class="action" id="change_actor_name" onclick="change_actor_name(<?=$actor_id?>);">Change</span>
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
		<div id="locations">
			<?php include 'views/locations_view.php'; ?>
		</div>
<?php } ?>
		<div id="location_name_popup" style="display: none">
			<h3>Change location name</h3>
			<input type="text" name="location_input" id="location_input" />
			<span class="action" id="change_location_name" onclick="change_location_name();">Change</span>
		</div>

		<p id="Leave this actor"><a href='/user'>Leave this actor</a></p>
	</body>
</html>
