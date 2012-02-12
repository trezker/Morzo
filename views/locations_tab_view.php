<?php if($travel) { ?>
			Travelling from <?=$travel['OriginName']?> to <?=$travel['DestinationName']?>.
<?php } else { ?>
			<div id="locations_feedback"></div>
			<div id="locations">
				<?php include 'views/locations_view.php'; ?>
			</div>
<?php } ?>
