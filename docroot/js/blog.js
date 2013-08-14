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
	window.location = "/blog/Control_panel/" + name;
}

function submit_blog_post() {
	var blog_id = $('#blog_id').val();
	var post_id = $('#post_id').val();
	var title = $('#new_post_title').val();
	var content = $('#new_post_content').val();
	var hidden = $('#new_post_hidden').attr('checked');
	callurl = '/blog/Submit_blog_post';
	$.ajax({
		type: 'POST',
		url: callurl,
		data: {
			blog_id: blog_id,
			post_id: post_id,
			title: title,
			content: content,
			hidden: hidden
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success !== false) {
				window.location.reload();
			}
		}
	});
}

function delete_blogpost() {
	var post_id = $('#post_id').val();
	callurl = '/blog/Delete_blogpost';
	$.ajax({
		type: 'POST',
		url: callurl,
		data: {
			post_id: post_id
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success !== false) {
				window.location.reload();
			}
		}
	});
}

function hide_blogpost(post_id) {
	callurl = '/blog/Hide_blogpost';
	$.ajax({
		type: 'POST',
		url: callurl,
		data: {
			post_id: post_id,
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success !== false) {
				window.location.reload();
			}
		}
	});
}
