<!DOCTYPE html>
<html>
	<head>
		<title>World admin - Morzo</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="/js/project_admin.js"></script>
		<script type="text/javascript" src="/js/dialog.js"></script>
	</head>
	<body>
		<h1>World administration</h1>
		<p><span class="action" onclick="window.location = 'user'">Back</span></p>

		<div class="recipes">
			<h2>Recipes</h2>
			<div id="recipes">
				<?php
				foreach ($recipes as $recipe) {
					echo expand_template(
						'<li><span class="action" onclick="edit_recipe({ID});">{Name}</span></li>',
						$recipe);
				}
				?>
			</div>
		</div>

		<div id="edit_recipe" style="float: left;">
		</div>
	</body>
</html>
