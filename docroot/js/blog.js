function create_blog() {
	var name = $('#new_blog_name').val();
	callurl = '/blog/Create_blog';
	$.ajax({
		type: 'POST',
		url: callurl,
		data: {name: name},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success !== false) {
				window.location.reload();
			}
		}
	});
}

function load_blog_control_panel() {
	var name = $('#select_blog').val();
	name = name.replace(" ", "_");
	alert(name);
	window.location = "/blog/Control_panel/" + name;
}

function new_blog_post({ID}) {
	var blog_id = $('#blog_id').val();
	var title = $('#new_post_title').val();
	var content = $('#new_post_content').val();
	callurl = '/blog/Create_blog_post';
	$.ajax({
		type: 'POST',
		url: callurl,
		data: {
			blog_id: blog_id,
			title: title,
			content: content
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success !== false) {
				alert("Created it");
			}
		}
	});
}
