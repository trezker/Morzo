<!DOCTYPE html>
<html>
	<head>
		<title>Morzo - blog</title>
		<?php echo $view_factory->Load_view('common_head_view'); ?>
		<link rel="stylesheet" type="text/css" media="screen" href="/css/blog.css">
		<script type="text/javascript" src="/js/blog.js"></script>
	</head>
	<body>
		<?php echo $view_factory->Load_view($data['blogposts_view']['view'], $data['blogposts_view']['data']); ?>
	</body>
</html>
