<!DOCTYPE html>
<html>
	<head>
		<title>Language admin - Morzo</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="/js/language_admin.js"></script>
		<script type="text/javascript" src="/js/dialog.js">	</script>
	</head>
	<body>
		<h1>Language administration</h1>
		<p><a href="/user">Back</a></p>
		<div id="languages">
			<select id="language">
			<?php
			$template = '
				<option value="{ID}">{Name}</option>
				';
			foreach ($languages as $language) {
				echo expand_template($template, $language);
			}
			?>
			</select>
			<a href="javascript:load_translations()">Get translations</a>
		</div>
		<div id="add_translation">
			<table class="translations_list">
				<?php
				$template = '
				<tr class="{alternate}">
					<td class="tdhandle">
						New handle<br />
						<input id="new_handle" name="new_handle" class="translation_input" style="width: 99%;" type="text" value="" />
					</td>
					<td>
						Enter the english text<br />
						<input id="new_text" name="new_text" class="translation_input" style="width: 99%;" type="text" value="" />
					</td>
					<td class="tdsave">
						<a href="javascript:new_translation()">Save</a><br />
						<span id="new_feedback"></span>
					</td>
				</tr>';
				$translation = array();
				$translation['alternate'] = 'alternate2';
				$translation['C'] = 0;
				echo expand_template($template, $translation);
				?>
			</table>
		</div>
		<div id="translations">
		</div>
	</body>
</html>
