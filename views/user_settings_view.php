<!DOCTYPE html>
<html>
	<head>
		<title>Morzo</title>
		<?php echo $common_head_view; ?>
		<script type="text/javascript" src="/js/user.js"></script>
	</head>
	<body>
		<p><a class="action" href="/user">Back</a></p>
		<h3>Current OpenID identifiers</h3>
		<?php
		echo '<table>
			  ';
		foreach($openids as $openid) {
			echo '
				  <tr>
					  <td style="font-size: small;">'.$openid['OpenID'].'</td>';

			if(count($openids) > 1)
				echo '<td class="action" onclick="delete_openid('.$openid['ID'].');">Delete</td>';

			echo '</tr>
				  ';
		}
		echo '
			  </table>';
		?>
		<h3>Add an OpenID identifier</h3>
		<div>
			<div class="openid_icons">
			<?php
			if(isset($openid_icons)) {
				foreach($openid_icons as $icon) {
					echo '<span class="action openid_icon" data-tooltip="'.$icon['name'].'"><img src="'.$icon['icon'].'" height="16" width="16" onclick="add_openid(\''.$icon['URI'].'\');" /></span>';
				}
			}
			?>
			</div>
			<span><input type="text" name="openid" id="openid" /></span>
			<span class="login_action action" onclick="add_openid();">Log in</span>
			<div id="openidfeedback"></div>
		</div>
	</body>
</html>
