<?php
$template = '
	<div class="product_input" id="product_input_{ID}" data-id="{ID}">
		<input class="amount" type="number" value="{Amount}" />
		<span class="product" data-id="{Product_ID}">{Product_Name}</span>
		<span class="action" style="float: right;" onclick="remove_product_input({ID})">Remove</span>
	</div>
';

echo expand_template($template, $data);
