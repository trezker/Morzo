<?php

/**
 * Simple template expansion function.
 * All occurrences of any of the keys in $params, surrounded by curly
 * braces, are replaced by the corresponding value. Three forms exist
 * for the key:
 * {foobar} substitutes html-encoded value
 * {@foobar} substitutes url-encoded value
 * {!foobar} substitutes raw value (use with care)
 *
 * @param string $template
 * @param array $params Associative array; keys are variable names,
 *                      values are, well, values.
 * @param boolean $noencode If set, no HTML-encoding takes place.
 * @return string The expanded template.
 */
function expand_template($template, $params, $noencode = false) {
	$search = array();
	$replace = array();
	if ($noencode) {
		foreach ($params as $name => $value) {
			$search[] = "{".$name."}";
			$replace[] = $value;
			$search[] = "{!".$name."}";
			$replace[] = $value;
			$search[] = "{@".$name."}";
			$replace[] = $value;
		}
	}
	else {
		foreach ($params as $name => $value) {
			$search[] = "{".$name."}";
			$replace[] = htmlspecialchars($value);
			$search[] = "{!".$name."}";
			$replace[] = $value;
			$search[] = "{@".$name."}";
			$replace[] = rawurlencode($value);
		}
	}
	return str_replace($search, $replace, $template);
}
