<!DOCTYPE html>
<html>
	<head>
		<title>World admin - Morzo</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="/js/world_admin.js"></script>
		<script type="text/javascript" src="/js/dialog.js"></script>
	</head>
	<body>
		<h1>World administration</h1>
		<p><span class="action" onclick="window.location = 'user'">Back</span></p>

		<div class="deficient_locations">
			<h2>Deficient locations</h2>
			<div id="locations">
				<?php
				foreach ($locations as $location) {
					echo '<li><span class="action" onclick="edit_location('.$location['ID'].');">'.$location['X'].' '.$location['Y'].'</span></li>';
				}
				?>
			</div>
		</div>

		<div id="edit_location" style="float: left;">
		</div>
	</body>
</html>
