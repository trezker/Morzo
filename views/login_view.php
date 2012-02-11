<script type="text/javascript">
	function login()
	{
		$('#openidfeedback').html('Processing...');
		var openid = document.getElementById('openid').value;
		$.ajax(
		{
			type: 'POST',
			url: '/user/Start_openid_login',
			data: { openid: openid },
			success: function(data)
			{
				if(data.success == false) {
					$('#openidfeedback').html('Process failed: ' + data.reason);
				} else {
					$('#openidfeedback').html('Redirecting');
					window.location = data.redirect_url;
				}
			}
		});
	}
</script>
<div class="login" id="openid_div" style="background: #AAA; width:100%; height: 100%;">
	<div class="login_input">
		OpenID<br/>
		<input type="text" name="openid" id="openid" />
	</div>
	<div class="action" onclick="login();">
		Log in
	</div>
	</form>
	<div id="openidfeedback"></div>
</div>
