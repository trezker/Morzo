<html>
	<head>
		<title>Morzo - front</title> 
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" media="screen" href="css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>
		<script type="text/javascript" src="/js/dialog.js"></script>
		<script type="text/javascript">
			function Popup_login() {
				callurl = '/front/Get_login_view';
				$.ajax({
					type: 'POST',
					url: callurl,
					success: function(data) {
						open_dialog(data);
					}
				});
			}
		</script>
	</head>
	<body>
		<div class="title">
			<h1>Morzo</h1>
		</div>
		
		<div class="user_menu">
			<div class="user_options">
				<span class="user_option" id="openid_link" onclick='Popup_login()'>Log in</span>
			</div>
		</div>
	</body>
</html>
