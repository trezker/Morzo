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
		<a class="action" href="/user">Back</a>
		Select a blog
		<?php
		echo '<select id="select_blog" onchange="load_blog_control_panel();">';
		$option_template = '
			  <option value="{Name}">{Name}</option>
			  ';
		echo expand_template($option_template, array('Name' => '-Select blog-'));
		foreach($blogs as $blog) {
			echo expand_template($option_template, $blog);
		}
		echo '</select>';
		?>
		<input id="new_blog_name" />
		<span class="action" onclick="create_blog();">Create blog</span>
		
		<div style="width: 500px;" id="blog_control_panel_container">
			<?php
			echo $blog_control_panel_view;
			?>
		</div>
	</body>
</html>
