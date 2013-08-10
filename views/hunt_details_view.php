<?php
	if($hunt == false) {
		echo "Something is broken";
		return;
	}
?>
<div id="hunt_details">
	<?php
	echo expand_template(
	'
	Hours left: {Hours_left}<br />
	{Stage_name} {Prey_name}<br />
	',
	$hunt['info']);
	?>
</div>
