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
			function change_location_name(location_id)
			{
				$('#change_location_name').html('Changing');
				callurl = '/user/Change_location_name/' + <?=$actor_id?> + '/' + location_id + '/' + $('#location_input').val();
				$.ajax(
				{
					type: 'GET',
					url: callurl,
					success: function(data)
					{
						$('#change_location_name').html('Change');
						if(data !== false)
						{
							$('#location_name').html(data);
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
			<input type="text" name="location_input" id="location_input" />
			<span class="action" id="change_location_name" onclick="change_location_name(<?=$actor['Location_ID']?>);">Change</span>
		</p>
		<p id="Leave this actor"><a href='/user'>Leave this actor</a></p>
	</body>
</html>
