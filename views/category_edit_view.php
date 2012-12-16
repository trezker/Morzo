<h2>Edit category <?php echo htmlspecialchars($category['Name']); ?></h2>
<div id="category" style="margin-left: 10px;">
	<?php
	$category['food_section'] = expand_template('
	<span class="action" onclick="show_category_food_properties()">
		Food properties
	</span><br />
	<div id="food_properties_container">
		Nutrition: <input type="number" id="food_nutrition" value="{Nutrition}" />
	</div>
	', $food);
	
	echo expand_template('
	Name: <input type="text" id="category_name" value="{Name}" /><br />
	{!food_section}
	<span class="action" onclick="show_category_weapon_properties()">
		Show Weapon properties
	</span>
	<div id="weapon_properties_container">
	</div>
	',
	$category);
	?>
	<span class="action" style="float: right;" onclick="save_category()">Save</span>
</div>
</div>
