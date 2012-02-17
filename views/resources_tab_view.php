<table>
	<?php
	$current_landscape = '';
	foreach ($resources as $resource) {
		if($resource['Landscape_name'] != $current_landscape) {
			if($current_landscape != '')
				echo '</tr>';
			$current_landscape = $resource['Landscape_name'];
			echo '
				<tr>
					<td>
						'.htmlspecialchars($current_landscape).'
					</td>
				';
		}
		echo '
				<td>
					'.htmlspecialchars($resource['Name']).'
				</td>
			';
	}
	echo '</tr>';
	?>
</table>
