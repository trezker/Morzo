<!DOCTYPE html>
<html>
	<head>
		<title><?= htmlspecialchars($data['actor']['Name']); ?> - <?= $data['tab']; ?> - Morzo</title> 
		<?php echo $view_factory->Load_view('common_head_view'); ?>
		<script type="text/javascript" src="/js/actor.js">	</script>
	</head>
	<body>
		<div style="display: inline-block;">
			<table>
				<tr>
					<td>
						Name
					</td>
					<td id="actor_name" class="action" onclick="set_actor_changer(<?=$data['actor_id']?>);">
						<?= htmlspecialchars($data['actor']['Name']); ?>
					</td>
				</tr>
				<tr>
					<td>
						Vitality
					</td>
					<td>
						<?php echo intval(($data['actor']['Health'] / 128)*100); ?>% health
						<?php echo intval(($data['actor']['Hunger'] / 128)*100); ?>% hungry
					</td>
				</tr>
				<tr>
					<td>
						Location
					</td>
					<td>
						<span id="location_name" class="action" onclick="set_location_changer(<?=$data['actor']['Location_ID']?>);"><?= htmlspecialchars($data['actor']['Location']); ?></span> [<?= htmlspecialchars($data['actor']['Biome_name']); ?>]
						<?php 
							if($data['actor']['Inside_object_name'] !== NULL) {
								echo '(' . $data['actor']['Inside_object_name'] . ')';
							}
						?>
					</td>
				</tr>
				<tr>
					<td>
						Time
					</td>
					<td>
						<?php echo htmlspecialchars($data['time']['year'].':'.$data['time']['month'].':'.$data['time']['day'].':'.$data['time']['hour']); ?> (Y:M:D:H)
					</td>
				</tr>
				<tr>
					<td>
						Next update in
					</td>
					<td>
						<?php echo floor($data['minutes_to_next_update']/60) . ":" . $data['minutes_to_next_update']%60; ?>
					</td>
					
				</tr>
			</table>

			<p id="Leave this actor"><a href='/user'>Leave this actor</a></p>
			
			<ul id="actor_tabs">
				<li <?php if($data['tab']=="events") echo 'class="current"';?> onclick="load_tab('events', <?=$data['actor_id']?>)">
					Events
				</li>
				<li <?php if($data['tab']=="locations") echo 'class="current"';?> onclick="load_tab('locations', <?=$data['actor_id']?>)">
					Locations
				</li>
				<li <?php if($data['tab']=="people") echo 'class="current"';?> onclick="load_tab('people', <?=$data['actor_id']?>)">
					People
				</li>
				<li <?php if($data['tab']=="resources") echo 'class="current"';?> onclick="load_tab('resources', <?=$data['actor_id']?>)">
					Resources
				</li>
				<li <?php if($data['tab']=="projects") echo 'class="current"';?> onclick="load_tab('projects', <?=$data['actor_id']?>)">
					Projects
				</li>
				<li <?php if($data['tab']=="inventory") echo 'class="current"';?> onclick="load_tab('inventory', <?=$data['actor_id']?>)">
					Inventory
				</li>
			</ul>
			<div id="tab_content" style="clear: both;">
				<?php echo $view_factory->Load_view($data['tab_view']['view'], $data['tab_view']['data']); ?>
			</div>


		</div>

		<div id="uidialog">
		</div>
		<div id="actor_name_popup" style="display: none" data-actor_id="<?=$data['actor_id']?>" title="Change actor name">
			<input type="text" name="actor_input" id="actor_input" />
		</div>
		<div id="location_name_popup" style="display: none" title="Change location name" data-actor_id="<?=$data['actor_id']?>" data-location_id="<?=$data['actor']['Location_ID']?>">
			<input type="text" name="location_input" id="location_input" />
		</div>
	</div>
	</body>
</html>
