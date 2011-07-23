<html>
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.php">
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.2.min.js"></script>
		<script type="text/javascript">
			function logout()
			{
				$('#logout').html('Logging out...');
				$.ajax(
				{
					type: 'GET',
					url: 'user/Logout',
					success: function(data)
					{
						window.location = 'front';
					}
				});
			}
			
			var new_actor_processing = false;
			function new_actor()
			{
				if(new_actor_processing == true)
				{
					$('#new_actor').html('Requesting, please wait...');
					return;
				}
				new_actor_processing = true;
				$('#new_actor').html('Requesting...');
				$.ajax(
				{
					type: 'GET',
					url: '/user/Request_actor',
					success: function(data)
					{
						if(data == true)
						{
							$('#new_actor').html('Request granted');
						}
						else
						{
							$('#new_actor').html('Request denied');
						}
						Refresh_actors();
						new_actor_processing = false;
					}
				});
			}
			
			function Refresh_actors()
			{
				$.ajax(
				{
					type: 'GET',
					url: '/actor/Actors',
					success: function(data)
					{
						$('#actors').html(data);
					}
				});
			}
		</script>
	</head>
	<body>
		<p>Hello <?php echo $_SESSION['username']; ?>!</p>
		<p>
			<span class="action" id="new_actor" onclick='new_actor()'>New actor</span>
		</p>
		<div id="actors">
			<?php include 'views/actors_view.php'; ?>
		</div>
		<p id="logout"><span class="action" onclick='logout()'>Log out</span></p>
		
		<?php if($_SESSION['admin']===true) { ?>
			<p>You're an Admin!</p>
		<?php } ?>
	</body>
</html>
