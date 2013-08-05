function ajax_logged_out(data) {
	if(data.success == false && data.reason == 'Not logged in') {
		show_login_dialog();
		return true;
	}
	return false;
}

function show_dialog(data) {
	$("#uidialog").attr("title", data.title);
	$("#uidialog").html(data.body);
	$("#uidialog").dialog({
		width: data.width,
		height: data.height,
		modal: true,
		buttons: data.buttons
	});
}

$(document).ready(function() {
	$('.openid_icon').mouseenter( function () {
		$('#openidfeedback').html($(this).attr('data-tooltip'));
	});
	$('.openid_icon').mouseleave( function () {
		$('#openidfeedback').html("&nbsp;");
	});
	$('#openid').keydown( function (event) {
		if (event.keyCode == 13) {
			login();
		}
	});
});

function login(openid){
	$('#openid_selection_container').hide();
	
	$('#openidfeedback').html('Processing...');
	if (openid == null){
		openid = document.getElementById('openid').value;
	}
	$.ajax({
		type: 'POST',
		url: '/user/Start_openid_login',
		data: {
			openid: openid
		},
		success: function(data){
			if(data.success == false) {
				$('#openid_selection_container').show();
				$('#openidfeedback').html('Process failed: ' + data.reason);
			} else {
				$('#openidfeedback').html('Redirecting');
				window.location = data.redirect_url;
			}
		}
	});
}

function add_openid(openid){
	$('#openidfeedback').html('Processing...');
	if (openid == null){
		openid = document.getElementById('openid').value;
	}	
	$.ajax(
	{
		type: 'POST',
		url: '/user/Start_add_openid',
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
