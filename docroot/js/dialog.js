function ajax_logged_out(data) {
	if(data.success == false && data.reason == 'Not logged in') {
		show_login_dialog();
		return true;
	}
	return false;
}

function show_login_dialog() {
	callurl = '/front/Get_login_view';
	$.ajax({
		type: 'POST',
		url: callurl,
		success: function(data) {
			open_dialog(data, 250, 120);
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
		}
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

function close_dialog() {
	$('body #popup').remove();
	$('body #grayout').remove();
}

function open_dialog(html, width, height) {
	if (width == null){
		width = 200;
	}	
	if (height == null){
		height = 100;
	}	

	if($('#popup').length == 0) {
		var grayout_div = '<div id="grayout" onclick="close_dialog()"></div>';
		var popup_div = '<div id="popup"></div>';
		$('body').prepend(popup_div);
		$('body').prepend(grayout_div);
	}
	var popup = $('#popup');
	popup.html(html);
	popup.css({
		width: width+'px',
		height: height+'px',
		position: 'absolute',
		top: '50%',
		left: '50%',
		margin: '-'+height+'px 0 0 -'+width+'px'
	});
	var grayout = $('#grayout');
	grayout.css({
		position: 'absolute',
		width: '100%',
		height:'100%',
		top: '0px',
		left: '0px',
		'background-color': 'rgba(0,0,0,0.4)'
	});
}

function login(openid)
{
	$('#openidfeedback').html('Processing...');
	if (openid == null){
		openid = document.getElementById('openid').value;
	}	
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

function add_openid(openid)
{
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
