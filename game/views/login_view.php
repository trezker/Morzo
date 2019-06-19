<div class="login popup_background" id="openid_div">
	<div class="popup_title">OpenID</div>
	<div class="login_content">
		<div class="openid_icons">
		<?php
		if(isset($openid_icons)) {
			foreach($openid_icons as $icon) {
				echo '<span class="action openid_icon" data-tooltip="'.$icon['name'].'"><img src="'.$icon['icon'].'" height="16" width="16" onclick="login(\''.$icon['URI'].'\');" /></span>';
			}
		}
		?>
		</div>
		<span><input type="text" name="openid" id="openid" /></span>
		<span class="login_action action" onclick="login();">Log in</span>
		<div id="openidfeedback"></div>
	</div>
</div>
