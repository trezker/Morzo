<!DOCTYPE html>
<html>
	<head>
		<title>Project admin - Morzo</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="/js/project_admin.js"></script>
		<script type="text/javascript" src="/js/dialog.js"></script>
	</head>
	<body>
		<h1>Project administration</h1>
		<p><span class="action" onclick="window.location = 'user'">Back</span></p>

		<div class="recipes" style="float: left;">
			<h2>Recipes</h2>
			<div id="recipes">
				<ul>
					<li><span class="action" onclick="edit_recipe(-1);">Create a new recipe</span></li>
					<?php
					foreach ($recipes as $recipe) {
						echo expand_template(
							'<li><span class="action" onclick="edit_recipe({ID});">{Name}</span></li>',
							$recipe);
					}
					?>
				</ul>
			</div>
		</div>
		<div id="edit_recipe" style="float: left;">
		</div>

		<div class="resources" style="float: left;">
			<h2>Resources</h2>
			<div id="resources">
				<ul>
					<li><span class="action" onclick="edit_resource(-1);">Create a new resource</span></li>
					<?php
					foreach ($resources as $resource) {
						echo expand_template(
							'<li><span class="action" onclick="edit_resource({ID});">{Name}</span></li>',
							$resource);
					}
					?>
				</ul>
			</div>
		</div>

		<div id="edit_resource" style="float: left;">
		</div>
	</body>
</html>
