function ajax_logged_out(data) {
	if(data.success == false && data.reason == 'Not logged in') {
		callurl = '/front/Get_login_view';
		$.ajax({
			type: 'POST',
			url: callurl,
			success: function(data) {
				open_dialog(data);
			}
		});
		return true;
	}
	return false;
}

function close_dialog() {
	$('body #popup').remove();
	$('body #grayout').remove();
}

function open_dialog(html) {
	if($('#popup').length == 0) {
		var grayout_div = '<div id="grayout" onclick="close_dialog()"></div>';
		var popup_div = '<div id="popup"></div>';
		$('body').append(grayout_div);
		$('body').append(popup_div);
	}
	var popup = $('#popup');
	popup.html(html);
	popup.css({
		width: '200px',
		height:'100px',
		position: 'absolute',
		top: '50%',
		left: '50%',
		margin: '-50px 0 0 -100px'
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
