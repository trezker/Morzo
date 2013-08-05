<!DOCTYPE html>
<html>
	<head>
		<title>Morzo</title> 
		<?php echo $common_head_view; ?>
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
						if(isset($openid_icons)) {
							foreach($openid_icons as $icon) {
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
			<?php echo $blogposts_view; ?>
		</div>
	</body>
</html>
