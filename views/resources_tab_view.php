<table>
	<?php
	foreach ($resources as $resource) {
		echo '
			<tr>
				<td>
					'.htmlspecialchars($resource['Name']).'
				</td>
			</tr>';
	}
	?>
</table>
