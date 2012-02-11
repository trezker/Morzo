<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="/js/dialog.js"></script>
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
			function new_actor() {
				if(new_actor_processing == true) {
					$('#new_actor').html('Requesting, please wait...');
					return;
				}
				new_actor_processing = true;
				$('#new_actor').html('Requesting...');
				$.ajax({
					type: 'GET',
					url: '/actor/Request_actor',
					success: function(data) {
						if(ajax_logged_out(data)) return;
						if(data.success == true) {
							$('#new_actor').html('Request granted');
							window.location.reload();
						}
						else {
							$('#new_actor').html('Request denied');
						}
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
			<ul class="actor_list">
				<?php
				foreach ($actors as $actor) {
					echo "<li><a href='actor/show_actor/".$actor["ID"]."'>".$actor["Name"]."</a></li>";
				}
				?>
			</ul>
		</div>
		<p id="logout"><span class="action" onclick='logout()'>Log out</span></p>
		
		<?php if($_SESSION['admin']===true) { ?>
			<p>You're an Admin!</p>
			<p><span class="action" onclick='window.location="user_admin"'>Users</span></p>
			<p><span class="action" onclick='window.location="world_admin"'>World</span></p>
		<?php } ?>
	</body>
</html>
