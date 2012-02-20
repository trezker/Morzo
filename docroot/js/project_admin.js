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

	var outputs = new Array();
	$('.output').each(
		function(index, value){
			var output = {
					id: value.id,
					amount: $(value).children('.amount').val(),
					resource: $(value).children('.resource').attr('data-id')
				};
			outputs.push(output);
		}
	);

	$.ajax(
	{
		type: 'POST',
		url: 'project_admin/save_recipe',
		data: {
			id: current_recipe,
			name: recipe_name,
			cycle_time: cycle_time,
			allow_fraction_output: allow_fraction_output,
			require_full_cycle: require_full_cycle,
			outputs: outputs
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
	$('.output#'+id).prepend($('#resource_select').html())
	$('.output#'+id+' select').change(id, selected_output_resource);
	$('.output#'+id+' .resource').css('display', 'none');
}

function selected_output_resource(e) {
	var value = $('.output#'+e.data+' select').val();
	var name = $('.output#'+e.data+' select [value="'+value+'"]').html();

	$('.output#'+e.data+' .resource').html(name);
	$('.output#'+e.data+' .resource').attr('data-id', value);
}
