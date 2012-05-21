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
		<h3>Current OpenID identifiers</h3>
		<?php
		echo '<table>
			  ';
		foreach($openids as $openid) {
			echo '
				  <tr>
					  <td style="font-size: small;">'.$openid['OpenID'].'</td><td class="action">Delete</td>
				  </tr>
				  ';
		}
		echo '
			  </table>';
		?>
		<h3>Add an OpenID identifier</h3>
	</body>
</html>
