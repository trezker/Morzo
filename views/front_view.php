<!DOCTYPE html>
<html>
	<head>
		<title>Morzo</title> 
		<?php echo Load_view('common_head_view'); ?>
		<link rel="stylesheet" type="text/css" media="screen" href="/css/blog.css">
		<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>
	</head>
	<body>
		<div class="title">
			<h1>Morzo</h1>
		</div>
		
		<div class="user_menu">
			<span>Log in with OpenID</span>
			<span id="openid_selection_container">
				<div>
					<span class="openid_icons">
						<?php
						if(isset($data["openid_icons"])) {
							foreach($data["openid_icons"] as $icon) {
								echo '<span class="action openid_icon" data-tooltip="'.$icon['name'].'"><img src="'.$icon['icon'].'" height="16" width="16" onclick="login(\''.$icon['URI'].'\');" /></span>';
							}
						}
						?>
					</span>
				</div>
				<span><input type="text" name="openid" id="openid" /></span>
				<span class="login_action action" onclick="login();">Log in</span>
			</span>
			<div id="openidfeedback">&nbsp;</div>
		</div>
		<div style="clear: both;">
			<p><a href="/library">Documentation</a></p>
			<?php
				echo Load_view('blogposts_view', array(
					'posts' => $data["posts"],
					'blogs' => $data["blogs"],
					'show_owner_controls' => false
				)); 
			?>
		</div>
	</body>
</html>
