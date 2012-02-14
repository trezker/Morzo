<?php if($travel) {
	expand_template('Travelling from {OriginName} to {DestinationName}.', $travel);
}
else { ?>
			<div id="locations_feedback"></div>
			<div id="locations">
				<table class="location_list">
					<?php
					$row_template = '<tr><td>' . 
						'<span class="action" onclick="set_location_changer(\'{id}\');">{name}</span>' .
						'</td><td>' .
						'<span class="action" onclick="travel(\'{id}\', \'{actor_id}\', \'{current_location}\')">Travel {compass}</span>' .
						'</td></tr>';
					foreach ($locations as $location) {
						$vars = array(
							'id' => $location["ID"],
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
