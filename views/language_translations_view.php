	<table class="translations_list">
	<?php
	$template = '
		<tr class="{alternate}">
			<td>{Handle}</td>
			<td>
				{English}<br />
				<input id="input_{C}" name="input_{C}" class="translation_input" style="width: 99%;" type="text" value="{Text}" />
			</td>
			<td>
				<span class="action" onclick="save_translation({C}, \'{Handle}\');">Save</span><br />
				<span id="feedback_{C}"></span>
			</td>
		</tr>';
	$alternate = '';
	$C = 1;
	foreach ($translations as $translation) {
		$alternate = ($alternate == 'alternate1')? 'alternate2': 'alternate1';
		$translation['alternate'] = $alternate;
		$translation['C'] = $C;
		echo expand_template($template, $translation);
		$C = $C + 1;
	}
	?>
	</table>
