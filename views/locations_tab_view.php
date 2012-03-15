<?php if($travel) {
	echo expand_template('Travelling from {OriginName} to {DestinationName}.', $travel);
	if($travel['Has_moved'] == 0) {
		echo '
		<div class="action" onclick="cancel_travel('.$actor_id.')">
			Cancel travel
		</div>
		';
	} else {
		echo '
		<div class="action" onclick="turn_around('.$actor_id.')">
			Turn around
		</div>
		';
	}
} else { ?>
			<div id="locations_feedback"></div>
			<div id="locations">
				<table class="location_list">
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
					foreach ($locations as $location) {
						$alternate = ($alternate == 'alternate1')? 'alternate2': 'alternate1';
						$vars = array(
							'alternate' => $alternate,
							'id' => $location["ID"],
							'actor_id' => $actor_id,
							'name' => $location["Name"],
							'compass' => $location["Compass"],
							'x' => $location["x"],
							'y' => $location["y"],
							'current_location' => $actor['Location_ID']);
						echo expand_template($row_template, $vars);
					}
					?>
				</table>
			</div>
<?php } ?>
