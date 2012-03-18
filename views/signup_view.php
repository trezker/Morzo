<!DOCTYPE html>
<html>
	<head>
		<title>Morzo</title> 
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="/js/dialog.js"></script>
		<script type="text/javascript" src="/js/user.js"></script>
	</head>
	<body>
		<h1>Create an account</h1>
		<p>
		Your openID was not found to have an account.
		Please provide a username to create one.
		</p>
		<label for="username">Username</label>
		<input type="text" name="username" id="username" />
		<span class="action" onclick="sign_up()">Sign up</span>
		<div id="signupfeedback"></div>
		<p><a href="/front">Go to front page</a></p>
	</body>
</html>
