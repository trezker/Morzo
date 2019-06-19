<?php
$properties = "&nbsp;";
if($data["Name"] == "Food")
{
	$properties = 'Nutrition <input type="text" data-property="nutrition" value="{Food_nutrition}" />';
}
elseif($data["Name"] == "Container")
{
	$properties = '	Mass limit <input type="text" data-property="mass_limit" value="{Container_mass_limit}" /><br />
					Volume limit <input type="text" data-property="volume_limit" value="{Container_volume_limit}" />';
}
elseif($data["Is_tool"] == 1)
{
	$properties = 'Efficiency <input type="text" data-property="efficiency" value="{Tool_efficiency}" />';
}

$data["properties"] = $properties;

$categorytemplate =	'
	<tr class="category" id="category_{ID}" data-category_id="{ID}">
		<td>{Name}</td>
		<td>{!properties}</td>
		<td>
			<a href="javascript:void(0)" class="action" onclick="remove_category({ID})">X</a>
		</td>
	</tr>
';

echo expand_template($categorytemplate, $data);
