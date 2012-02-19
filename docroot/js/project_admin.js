var current_recipe = -1;

function edit_recipe(id)
{
	$.ajax(
	{
		type: 'POST',
		url: 'project_admin/edit_recipe',
		data: {
			id: id
		},
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data !== false)
			{
				current_recipe = id;
				$('#edit_recipe').html(data.data);
			}
		}
	});
}

function save_recipe() {
	var recipe_name = $('#recipe_name').val();
	var cycle_time = $('#cycle_time').val();
	var allow_fraction_output = $('#allow_fraction_output').attr('checked');
	var require_full_cycle = $('#require_full_cycle').attr('checked');

	$.ajax(
	{
		type: 'POST',
		url: 'project_admin/save_recipe',
		data: {
			id: current_recipe,
			name: recipe_name,
			cycle_time: cycle_time,
			allow_fraction_output: allow_fraction_output,
			require_full_cycle: require_full_cycle
		},
		success: function(data)
		{
			if(ajax_logged_out(data)) return;
			if(data !== false)
			{
				current_recipe = data.id;
				$('#edit_recipe').html(data.data);
			}
		}
	});
}

function select_output_resource(id) {
	$('#output_'+id).prepend($('#resource_select').html())
	$('#output_'+id+' select').change(id, selected_output_resource);
	$('#output_'+id+' .resource').css('display', 'none');
}

function selected_output_resource(e) {
	console.log(e);
	var value = $('#output_'+e.data+' select').val();
	var name = $('#output_'+e.data+' select [value="'+value+'"]').html();

	$('#output_'+e.data+' .resource').html(name);
	$('#output_'+e.data+' .resource').attr('data-id', value);

	console.log($('#output_'+e.data+' select').val());
}
