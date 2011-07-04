<html>
	<head>
		<style type="text/css">
			.action {
				color: #00F;
				cursor: pointer;
			}
			.namechange {
				font-size: 80%;
			}
		</style>
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.2.min.js"></script>
		<script type="text/javascript">
			var change_location_id = -1;
			function location_changer(location_id)
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
			function change_location_name(location_id)
			{
				$('#change_location_name').html('Changing');
				callurl = '/user/Change_location_name/' + <?=$actor_id?> + '/' + change_location_id + '/' + $('#location_input').val();
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
							//TODO: Reload locations view
						}
						location_changer(-1);
					}
				});
			}
			function change_actor_name(actor_id)
			{
				$('#change_actor_name').html('Changing');
				callurl = '/user/Change_actor_name/' + <?=$actor_id?> + '/' + actor_id + '/' + $('#name_input').val();
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
			<span id='changelink_<?=$actor['Location_ID']?>' class="action namechange" onclick="location_changer(<?=$actor['Location_ID']?>);">Change name</span>
		</p>
		<div id="edit_popup">
		</div>
		<div id="locations">
			<h2>Locations you can go to</h2>
			<ul class="location_list">
				<?php
				foreach ($locations as $location) {
					$id = $location["ID"];
					$name = $location["Name"];
					echo "
						<li>
							<a href='/user/travel/$id'>$name</a>
							<span id='changelink_$id' class='action namechange' onclick='location_changer(\"$id\");'>Change name</span>
						</li>
						";
				}
				?>
			</ul>
		</div>
		<p id="Leave this actor"><a href='/user'>Leave this actor</a></p>

		<div id="location_name_popup" style="display: none">
			<h3>Change location name</h3>
			<input type="text" name="location_input" id="location_input" />
			<span class="action" id="change_location_name" onclick="change_location_name(-1);">Change</span>
		</div>
	</body>
</html>
