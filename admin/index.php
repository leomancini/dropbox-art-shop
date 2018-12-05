<?php
	include("../resources/functions.php");
	restricted();
?>
<!DOCTYPE HTML>
<html>

	<head>
		<title>Laura Mancini</title>
		<link rel="stylesheet/less" href="resources/css/admin.less">
		<link rel="stylesheet/less" href="../resources/css/style.less">
		<script src="../resources/js/less.js"></script>
		<script src="../resources/js/jquery.js"></script>
		<script src="resources/js/ui.js"></script>
		<script src="../resources/js/delay.js"></script>
		<script src="resources/js/form.js"></script>
		<script src="resources/js/admin.js"></script>
	</head>
	<body>
		
		<div id="dashboard">
			<a class="module" href="podcast">
				<div class="glyph">
					<img src="resources/images/emoji/podcast.png">
				</div>
				<h1>Podcast Manager</h1>
			</a>
			<a class="module" href="collections.php">
				<div class="glyph">
					<img src="resources/images/emoji/collections.png">
				</div>
				<h1>Art Collection Editor</h1>
			</a>
			<a class="module" href="orders.php">
				<div class="glyph">
					<img src="resources/images/emoji/orders.png">
				</div>
				<h1>Pending Art Orders</h1>
			</a>
		</div>
		
	</body>
</html>