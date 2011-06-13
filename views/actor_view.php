<html>
	<head>
		<style type="text/css">
			p {margin-left:20px; text-align:center;}
			.action {
				color: #00F;
				cursor: pointer;
			}
		</style>
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.2.min.js"></script>
		<script type="text/javascript">
		</script>
	</head>
	<body>
		<p>Viewing <?php echo $actor['Name']; ?></p>
		<p>Current location is <?php echo $actor['Location']; ?></p>
		<p id="Leave this actor"><a href='/user'>Leave this actor</a></p>
	</body>
</html>

