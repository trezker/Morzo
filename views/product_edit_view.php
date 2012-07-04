<h2>Edit product <?php echo htmlspecialchars($product['Name']); ?></h2>
<div id="product">
	<?php
	echo expand_template(
	'<table>
		<tr>
			<td class="label">Name:</td>
			<td><input type="text" id="product_name" value="{Name}" /></td>
		</tr>
		<tr>
			<td class="label">Mass (gram):</td>
			<td><input type="number" id="mass" value="{Mass}" /></td>
		</tr>
		<tr>
			<td class="label">Volume (litre):</td>
			<td><input type="number" id="volume" value="{Volume}" /></td>
		</tr>
		<tr>
			<td class="label">Rot rate:</td>
			<td><input type="number" id="rot_rate" value="{Rot_rate}" /></td>
		</tr>
	</table>',
	$product);
	?>
	<span class="action" style="float: right;" onclick="save_product()">Save</span>
</div>
</div>
