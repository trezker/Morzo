<html>
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.php">
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.2.min.js"></script>
		<script type="text/javascript">
			function edit_location(id)
			{
				$.ajax(
				{
					type: 'POST',
					url: 'world_admin/edit_location',
					data: {
						id: id
					},
					success: function(data)
					{
						if(data !== false)
						{
							$('#edit_location').html(data.data);
						}
					}
				});
			}
			function add_biome()
			{
				$.ajax(
				{
					type: 'POST',
					url: 'world_admin/add_biome',
					data: {
						name: $('#new_biome').val()
					},
					success: function(data)
					{
						if(data !== false)
						{
							$('#biome_list').html(data.data);
						}
					}
				});
			}
		</script>
	</head>
	<body>
		<h1>World administration</h1>
		<p><span class="action" onclick="window.location = 'front'">Back</span></p>
		<div id="edit_location">
		</div>

		<h3>Biomes</h3>
		<div id="biome_list">
			<?php include 'views/biomes_view.php'; ?>
		</div>
		<input type="text" id="new_biome" /><span class="action" onclick="add_biome();">Add biome</span>
		
		<h2>Deficient locations</h2>
		<div id="locations">
			<?php
			foreach ($locations as $location) {
				echo '<li><span class="action" onclick="edit_location('.$location['ID'].');">'.$location['X'].' '.$location['Y'].'</span></li>';
			}
			?>
		</div>
	</body>
</html>
