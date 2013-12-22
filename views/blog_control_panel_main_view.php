<!DOCTYPE html>
<html>
	<head>
		<title>Morzo</title>
		<?php echo $common_head_view; ?>
		<link rel="stylesheet" type="text/css" media="screen" href="/css/blog.css">
		<script type="text/javascript" src="/js/blog.js"></script>
	</head>
	<body>
		<a class="action" href="/user">Back</a>
		Select a blog
		<?php
		echo '<select id="select_blog" onchange="load_blog_control_panel(-1);">';
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
		
		<div id="blog_control_panel_container">
			<?php
			echo $blog_control_panel_view;
			?>
		</div>
	</body>
</html>
