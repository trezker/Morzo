<table>
	<?php
	foreach ($resources as $resource) {
		echo '
			<tr>
				<td>
					'.$resource['Name'].'
				</td>
			</tr>';
	}
	?>
</table>
