<html>
	<head>
		<style type="text/css">
			p {margin-left:20px; text-align:center;}
			.action {
				color: #00F;
				cursor: pointer;
			}
		</style>
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.2.min.js"></script>
		<script type="text/javascript">
			var change_location_id = <?=$actor['Location_ID']?>;
			function location_changer(location_id)
			{
				change_location_id = location_id;
				//Todo: Toggle location changer visibility
			}
			function change_location_name(location_id)
			{
				alert("Changing: " + change_location_id);
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
							alert("Changed: " + data);
						}
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
			<span class="action" onclick="location_changer(<?=$actor['Location_ID']?>);">Change</span>
		</p>
		<div id="location_name_changer">
			<input type="text" name="location_input" id="location_input" />
			<span class="action" id="change_location_name" onclick="change_location_name(-1);">Change</span>
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
							<span class='action' onclick='location_changer(\"$id\");'>Change</span>
						</li>
						";
				}
				?>
			</ul>
		</div>
		<p id="Leave this actor"><a href='/user'>Leave this actor</a></p>
	</body>
</html>
