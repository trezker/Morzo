<!DOCTYPE html>
<html>
	<head>
		<title>Morzo</title> 
		<?php echo $view_factory->Load_view('common_head_view'); ?>
		<link rel="stylesheet" type="text/css" media="screen" href="/css/blog.css">
		<script type="text/javascript" src="/js/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="/js/front.js"></script>
	</head>
	<body>
		<div class="title">
			<h1>Morzo</h1>
		</div>

		<div class="login_content">
			<div class="floatright">
				<label for="username">Username</label>
				<input type="text" id="username" />
			</div>
			<div class="clearboth floatright">
				<label for="pass">Pass</label>
				<input type="password" id="pass" />
			</div>
			<div class="clearboth floatright">
				<span id="login" class="action">Log in</span>
				<span id="create" class="action">Create account</span>
			</div>
			<div class="clearboth floatright">
				<div id="login_message"></div>
			</div>
		</div>
		
		<div style="clear: both;">
			<p><a href="/library">Documentation</a></p>
			<?php
				echo $view_factory->Load_view('blogposts_view', array(
					'posts' => $data["posts"],
					'blogs' => $data["blogs"],
					'show_owner_controls' => false
				)); 
			?>
		</div>
	</body>
</html>
