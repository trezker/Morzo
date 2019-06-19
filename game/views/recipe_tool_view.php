<?php
$template = '
	<div class="tool" id="tool_{ID}" data-id="{ID}">
		<span class="category" data-id="{Category_ID}">{Category_Name}</span>
		<span class="action" style="float: right;" onclick="remove_tool({ID})">Remove</span>
	</div>
';

echo expand_template($template, $data);
