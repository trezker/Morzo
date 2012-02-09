<?php if($travel) { ?>
			Travelling from <?=$travel['OriginName']?> to <?=$travel['DestinationName']?>.
<?php } else { ?>
			<h2>Locations you can go to</h2>
			<div id="locations_feedback"></div>
			<div id="locations">
				<?php include 'views/locations_view.php'; ?>
			</div>
<?php } ?>
