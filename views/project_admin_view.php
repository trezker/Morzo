<!DOCTYPE html>
<html>
	<head>
		<title>Project admin - Morzo</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<?php echo $common_head_view; ?>
		<script type="text/javascript" src="/js/project_admin.js"></script>
	</head>
	<body>
		<h1>Project administration</h1>
		<p><span class="action" onclick="window.location = 'user'">Back</span></p>

		<div class="accordion">
			<h3>Help</h3>
			<div>
				<h2>Recipes</h2>
				<p>A recipe descripes how to tranform resources and objects into other resources and objects.
				You select inputs and outputs, add them and enter amounts.
				</p>
				<p>The from nature option on resource inputs makes it require that resource exist on the location where you try to start a project.
				If not checked, you need to add the resource from inventory.
				</p>
				<p>I don't remember why I added the Allow fraction output and require full cycle options, don't know how I figured to apply them.</p>
				<h2>Resources</h2>
				<p>If a resource is natural it can be available in the world as a natural resource. Otherwise it is something you have to create in a project.
				You need to specify the unit used for measurement of the resource and specify the weight and volume per unit.
				</p>
				<p>You can also set which categories the resource belongs to, see Categories below.</p>
				<h2>Products</h2>
				<p>Products is used for things that need to be handled as separate entities for each unit produced.
				These also require specified Mass and Volume.
				You can also set a rot rate, but its effect is not yet implemented.
				</p>
				<p>You can also set which categories the product belongs to, see Categories below.</p>
				<h2>Categories</h2>
				<p>
					Categories are used to handle special attributes of resources and products.
				</p>
				<p>
					Under food you can enter Nutrition value, this makes the resource or product edible.
					Hunger increases by 1 per ingame hour and there is 16 hours in a day. So 16 nutrition would fill a days need.
				</p>
				<p>
					Under container you can make a product that can store things, this is not applicable to resources.
					If you only check Is container, it will have unlimited storage capacity.
					You can also set Mass or Volume limit or both.
				</p>
			</div>
		</div>

		<div class="recipes" style="float: left;">
			<h2>Recipes</h2>
			<div id="recipes">
				<ul>
					<li><span class="action" onclick="edit_recipe(-1);">Create a new recipe</span></li>
					<?php
					foreach ($recipes as $recipe) {
						echo expand_template(
							'<li><span class="action" onclick="edit_recipe({ID});">{Name}</span></li>',
							$recipe);
					}
					?>
				</ul>
			</div>
		</div>
		<div id="edit_recipe" style="float: left;">
		</div>

		<div class="resources" style="clear: both; float: left;">
			<h2>Resources</h2>
			<div id="resources">
				<ul>
					<li><span class="action" onclick="edit_resource(-1);">Create a new resource</span></li>
					<?php
					foreach ($resources as $resource) {
						echo expand_template(
							'<li><span class="action" onclick="edit_resource({ID});">{Name}</span></li>',
							$resource);
					}
					?>
				</ul>
			</div>
		</div>

		<div id="edit_resource" style="float: left;">
		</div>

		<div class="products" style="clear: both; float: left;">
			<h2>Products</h2>
			<div id="products">
				<ul>
					<li><span class="action" onclick="edit_product(-1);">Create a new product</span></li>
					<?php
					foreach ($products as $product) {
						echo expand_template(
							'<li><span class="action" onclick="edit_product({ID});">{Name}</span></li>',
							$product);
					}
					?>
				</ul>
			</div>
		</div>

		<div id="edit_product" style="float: left;">
		</div>

		<div class="categories" style="clear: both; float: left;">
			<h2>Categories</h2>
			<div id="categories">
				<ul>
					<li><span class="action" onclick="edit_category(-1);">Create a new category</span></li>
					<?php
					foreach ($categories as $category) {
						echo expand_template(
							'<li><span class="action" onclick="edit_category({ID});">{Name}</span></li>',
							$category);
					}
					?>
				</ul>
			</div>
		</div>

		<div id="edit_category" style="float: left;">
		</div>
	</body>
</html>
