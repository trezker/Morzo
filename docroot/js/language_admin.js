function load_translations()
{
	language_id = $('#language').val();
	$.ajax(
	{
		type: 'POST',
		url: 'language_admin/Load_translations',
		data: {
			language_id: language_id
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				$('#translations').html(data.html);
			}
		}
	});
}

function save_translation(input_id, handle) {
	$('#feedback_'+input_id).html("Saving...");
	var language_id = $('#language').val();
	var text = $('#input_'+input_id).val();
	$.ajax({
		type: 'POST',
		url: 'language_admin/Save_translation',
		data: {
			language_id: language_id,
			handle: handle,
			text: text			
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				$('#feedback_'+input_id).html("Saved");
			} else {
				alert(data.reason);
				$('#feedback_'+input_id).html(data.reason);
			}
		}
	});
}

function new_translation() {
	$('#new_feedback').html("Saving...");
	var handle = $('#new_handle').val();
	var text = $('#new_text').val();
	$.ajax({
		type: 'POST',
		url: 'language_admin/New_translation',
		data: {
			handle: handle,
			text: text			
		},
		success: function(data) {
			if(ajax_logged_out(data)) return;
			if(data.success == true) {
				$('#new_feedback').html("Saved");
			} else {
				$('#new_feedback').html(data.reason);
			}
		}
	});
}
