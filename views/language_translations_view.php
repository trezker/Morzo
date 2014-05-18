	<table class="translations_list">
	<?php
	$template = '
		<tr class="{alternate}">
			<td class="tdhandle">{Handle}</td>
			<td>
				{English}<br />
				<input id="input_{C}" name="input_{C}" class="translation_input width_100" type="text" value="{Text}" />
			</td>
			<td class="tdsave">
				<a href="javascript:void(0)" onclick="javascript:save_translation({C}, \'{Handle}\')">Save</a><br />
				<span id="feedback_{C}"></span>
			</td>
		</tr>';
	$alternate = '';
	$C = 1;
	foreach ($data['translations'] as $translation) {
		$alternate = ($alternate == 'alternate1')? 'alternate2': 'alternate1';
		$translation['alternate'] = $alternate;
		$translation['C'] = $C;
		echo expand_template($template, $translation);
		$C = $C + 1;
	}
	?>
	</table>
