<?php
$template = '
	<div class="input" id="resource_input_{ID}" data-id="{ID}">
		<input class="amount" type="number" value="{Amount}" />
		<span class="measuredesc" data-id="{Measure_ID}">{Measuredesc}</span>
		<span class="resource" data-id="{Resource_ID}">{Resource_Name}</span>
		(from nature: <input type="checkbox" class="from_nature" {From_nature_checked} />)
		<span class="action" style="float: right;" onclick="remove_input({ID})">Remove</span>
	</div>
';

echo expand_template($template, $data);
