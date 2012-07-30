<!DOCTYPE html>
<html>
	<head>
		<title>Morzo</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="/js/dialog.js"></script>
		<script type="text/javascript" src="/js/blog.js"></script>
	</head>
	<body>
		<?php
		$post_template = '
				<div>
					<h2>{Title}</h2>
					<div>{Content}</div>
				</div>
			  ';
		foreach($titles as $post) {
			echo expand_template($post_template, $post);
		}
		?>
	</body>
</html>
