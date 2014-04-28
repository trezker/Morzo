<!DOCTYPE html>
<html>
	<head>
		<title><?= htmlspecialchars($actor['Name']); ?> - <?= $tab; ?> - Morzo</title> 
		<?php echo Load_view('common_head_view'); ?>
		<script type="text/javascript" src="/js/actor.js">	</script>
	</head>
	<body>
		<div style="display: inline-block;">
			<table>
				<tr>
					<td>
						Name
					</td>
					<td id="actor_name" class="action" onclick="set_actor_changer(<?=$actor_id?>);">
						<?= htmlspecialchars($actor['Name']); ?>
					</td>
				</tr>
				<tr>
					<td>
						Vitality
					</td>
					<td>
						<?php echo intval(($actor['Health'] / 128)*100); ?>% health
						<?php echo intval(($actor['Hunger'] / 128)*100); ?>% hungry
					</td>
				</tr>
				<tr>
					<td>
						Location
					</td>
					<td>
						<span id="location_name" class="action" onclick="set_location_changer(<?=$actor['Location_ID']?>);"><?= htmlspecialchars($actor['Location']); ?></span> [<?= htmlspecialchars($actor['Biome_name']); ?>]
						<?php 
							if($actor['Inside_object_name'] !== NULL) {
								echo '(' . $actor['Inside_object_name'] . ')';
							}
						?>
					</td>
				</tr>
				<tr>
					<td>
						Time
					</td>
					<td>
						<?php echo htmlspecialchars($time['year'].':'.$time['month'].':'.$time['day'].':'.$time['hour']); ?> (Y:M:D:H)
					</td>
				</tr>
				<tr>
					<td>
						Next update in
					</td>
					<td>
						<?php echo floor($minutes_to_next_update/60) . ":" . $minutes_to_next_update%60; ?>
					</td>
					
				</tr>
			</table>

			<p id="Leave this actor"><a href='/user'>Leave this actor</a></p>
			
			<ul id="actor_tabs">
				<li <?php if($tab=="events") echo 'class="current"';?> onclick="load_tab('events', <?=$actor_id?>)">
					Events
				</li>
				<li <?php if($tab=="locations") echo 'class="current"';?> onclick="load_tab('locations', <?=$actor_id?>)">
					Locations
				</li>
				<li <?php if($tab=="people") echo 'class="current"';?> onclick="load_tab('people', <?=$actor_id?>)">
					People
				</li>
				<li <?php if($tab=="resources") echo 'class="current"';?> onclick="load_tab('resources', <?=$actor_id?>)">
					Resources
				</li>
				<li <?php if($tab=="projects") echo 'class="current"';?> onclick="load_tab('projects', <?=$actor_id?>)">
					Projects
				</li>
				<li <?php if($tab=="inventory") echo 'class="current"';?> onclick="load_tab('inventory', <?=$actor_id?>)">
					Inventory
				</li>
			</ul>
			<div id="tab_content" style="clear: both;">
				<?php echo Load_view($data['tab_view']['view'], $data['tab_view']['data']); ?>
			</div>


		</div>

		<div id="uidialog">
		</div>
		<div id="actor_name_popup" style="display: none" data-actor_id="<?=$actor_id?>" title="Change actor name">
			<input type="text" name="actor_input" id="actor_input" />
		</div>
		<div id="location_name_popup" style="display: none" title="Change location name" data-actor_id="<?=$actor_id?>" data-location_id="<?=$actor['Location_ID']?>">
			<input type="text" name="location_input" id="location_input" />
		</div>
	</div>
	</body>
</html>
