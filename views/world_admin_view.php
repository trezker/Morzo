<html>
	<head>
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.php">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="js/world_admin.js"></script>
	</head>
	<body>
		<h1>World administration</h1>
		<p><span class="action" onclick="window.location = 'user'">Back</span></p>
		<div id="edit_location">
		</div>

		<div style="float: left;">
			<h4 style="margin: 0px;">Biomes</h4>
			<div id="biome_list">
				<?php include 'views/biomes_view.php'; ?>
			</div>
			<input type="text" id="new_biome" />
			<br/><span class="action" onclick="add_biome();">Add biome</span>
		</div>

		<div style="float: left;">
			<h4 style="margin: 0px;">Resources</h4>
			<div id="resource_list">
				<?php include 'views/resources_view.php'; ?>
			</div>
			<input type="text" id="new_resource" />
			<br/><span class="action" onclick="add_resource();">Add resource</span>
		</div>
		
		<div style="clear: both;"></div>
		
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
