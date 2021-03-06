<?php if($data['travel']) {
	echo expand_template('Travelling from {OriginName} to {DestinationName}.', $data['travel']);
	if($data['travel']['Has_moved'] == 0) {
		echo '
		<div class="action" onclick="cancel_travel('.$data['actor_id'].')">
			Cancel travel
		</div>
		';
	} else {
		echo '
		<div class="action" onclick="turn_around('.$data['actor_id'].')">
			Turn around
		</div>
		';
	}
} else { ?>
			<div id="locations_feedback"></div>
			
			<?php 
				if($data['actor']['Inside_object_name'] !== NULL) {
					$template = '
						<div>
							<a href="javascript:leave_object({actor_id})">Leave {Inside_object_name}</a>
						</div>
						';
					$vars = array(
						'actor_id' => $data['actor_id'],
						'Inside_object_name' => $data['actor']['Inside_object_name']
						);
					echo expand_template($template, $vars);
				}
			?>
			
			<div id="locations" class="floatleft">
				<table class="location_list list">
					<?php
					$row_template = '
						<tr class="{alternate}">
							<td>
								<span class="action" onclick="set_location_changer(\'{id}\');">{name}</span>
							</td>
							<td>
								<span class="action" onclick="travel(\'{id}\', \'{actor_id}\', \'{current_location}\')">Travel {compass}</span>
							</td>
						</tr>';
					$alternate = '';
					if($data['locations']) {
						foreach ($data['locations'] as $location) {
							$alternate = ($alternate == 'alternate1')? 'alternate2': 'alternate1';
							$vars = array(
								'alternate' => $alternate,
								'id' => $location["ID"],
								'actor_id' => $data['actor_id'],
								'name' => $location["Name"],
								'compass' => $location["Compass"],
								'x' => $location["x"],
								'y' => $location["y"],
								'current_location' => $data['actor']['Location_ID']);
							echo expand_template($row_template, $vars);
						}
					}
					?>
				</table>
			</div>
			<div id='containers' class="floatleft">
				<table class="container_list list">
					<?php
					$row_template = '
						<tr class="{alternate}">
							<td>
								{name}
							</td>
							<td>
								<a href="javascript:enter_object(\'{actor_id}\', \'{id}\')">Enter</a>
							</td>
						</tr>';
					$alternate = '';
					if($data['containers']) {
						foreach ($data['containers'] as $container) {
							$alternate = ($alternate == 'alternate1')? 'alternate2': 'alternate1';
							$vars = array(
									'alternate' => $alternate,
									'id' => $container["ID"],
									'actor_id' => $data['actor_id'],
									'name' => $container["Name"]
								);
							echo expand_template($row_template, $vars);
						}
					}
					?>
				</table>
			</div>
<?php } ?>
