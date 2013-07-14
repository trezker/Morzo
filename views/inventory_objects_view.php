<?php
foreach ($objects as $object) {
	$template = '
				<tr>
					<td>{Name}</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				';
	echo expand_template($template,
		array(
			'Name' => $object['Name']
		));
}
?>
