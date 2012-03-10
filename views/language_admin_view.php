<!DOCTYPE html>
<html>
	<head>
		<title>Language admin - Morzo</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="/js/language_admin.js"></script>
		<script type="text/javascript" src="/js/dialog.js">	</script>
	</head>
	<body>
		<h1>Language administration</h1>
		<p><a class="action" href="user">Back</a></p>
		<div id="languages">
			<select id="language">
			<?php
			$template = '
				<option value="{ID}">{Name}</option>
				';
			foreach ($languages as $language) {
				echo expand_template($template, $language);
			}
			?>
			</select>
			<span class="action" onclick="load_translations();">Get translations</span>
		</div>
		<div id="translations"></div>
	</body>
</html>
