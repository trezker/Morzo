<!DOCTYPE html>
<html>
	<head>
		<title>Morzo</title> 
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css">
		<link rel="stylesheet" type="text/css" media="screen" href="/css/blog.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>
		<script type="text/javascript" src="/js/dialog.js"></script>
	</head>
	<body>
		<div class="title">
			<h1>Morzo</h1>
		</div>
		
		<div class="user_menu">
			<span>Log in with OpenID</span>
			<span>
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
				<div id="openidfeedback">&nbsp;</div>
			</span>
		</div>
		<div style="clear: both;">
			<?php echo $blogposts_view; ?>
		</div>
	</body>
</html>
