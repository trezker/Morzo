<?php
$template = '
	<div class="output" id="resource_output_{ID}" data-id="{ID}">
		<input class="amount" type="number" value="{Amount}" />
		<span class="measuredesc" data-id="{Measure_ID}">{Measuredesc}</span>
		<span class="resource" data-id="{Resource_ID}">{Resource_Name}</span>
		<span class="action" style="float: right;" onclick="remove_output({ID})">Remove</span>
	</div>
';

echo expand_template($template, $data);
