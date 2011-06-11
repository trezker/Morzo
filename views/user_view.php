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
					$('#new_actor_status').html('Requesting, please wait...');
					return;
				}
				new_actor_processing = true;
				$('#new_actor_status').html('Requesting...');
				$.ajax(
				{
					type: 'GET',
					url: 'user/New_actor',
					success: function(data)
					{
						$('#new_actor_status').html('');
						new_actor_processing = false;
					}
				});
			}
		</script>
	</head>
	<body>
		<p>Hello <?php echo $_SESSION['username']; ?>!</p>
		<p>
			<span class="action" id="new_actor" onclick='new_actor()'>New actor</span>
			<span id="new_actor_status"></span>
		</p>
		<?php
			foreach ($actors as $actor) {
    			echo "<p><a href='user/actor/$actor'>$actor</a></p>";
			}
		?>
		<p id="logout"><a href='' onclick='logout()'>Log out</a></p>
		
		<?php if($_SESSION['admin']===true) { ?>
			<p>You're an Admin!</p>
		<?php } ?>
	</body>
</html>

