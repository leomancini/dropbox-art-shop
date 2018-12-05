<?php
	include("../resources/functions.php");
	restricted();
	
	if(isset($_GET['view']) && $_GET['view'] != "") {
		$view = $_GET['view'];
	} else {
		$view = "pending";
	}
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

		<header>
			<a href="." class="back">&laquo; back</a>
			<h1><?php echo ucwords($view);?> Orders</h1>
				<?php if($view == "shipped") { ?>
					<a href="?view=pending" id="pending">View pending orders</a>
				<?php } else { ?>
					<a href="?view=shipped" id="shipped">View shipped orders</a>
				<?php } ?>
			<div class="right">
			</div>
		</header>
	
		<div class="content" id="orders">
			<?php
				$view_code = Array(
					"pending" => "0",
					"shipped" => "1"
				);
				
				$collection_metadata_query = mysql_query("SELECT * FROM collections ORDER BY displayOrder ASC") or die(mysql_error());
				while($collection_metadata = mysql_fetch_array($collection_metadata_query)) {
					$collections[$collection_metadata['id']] = Array(
						"title" => $collection_metadata["title"],
						"id" => $collection_metadata["id"]
					);
				}
				
				$order_query = mysql_query("SELECT * FROM orders WHERE `status` = '$view_code[$view]' ORDER BY timestamp DESC");
				while($order = mysql_fetch_array($order_query)) {
					$order = array_map('htmlspecialchars', $order);
					
					$item_query = mysql_query("SELECT * FROM work WHERE id = '".$order['itemID']."' LIMIT 1") or die(mysql_error());
					$item = mysql_fetch_array($item_query);
			?>
				<div class="order <?php echo $view; ?>" id="<?php echo $order['index']; ?>">
					<div class="preview">
						<img src="../..<?php echo $artPath; ?><?php echo $collections[$item['collectionID']]['title']; ?>/thumb_<?php echo getFilename($item['title']); ?>">
					</div>
					<div class="info">
						<h1><?php echo itemTitle($item['title'], $item['id']); ?></h1>
						<h2><?php setlocale(LC_MONETARY, 'en_US'); echo money_format('%n', $order['amountpaid']) . "\n"; ?></h2>
						<p>
							<?php echo $order['firstname']; ?> <?php echo $order['lastname']; ?><br>
							<?php echo $order['address1']; ?><br>
							<?php if($order['address2'] != "") { echo $order['address2']."<br>"; } ?>
							<?php echo $order['city']; ?>, <?php echo $order['state']; ?> <?php echo $order['zipcode']; ?><br>
							<a href="mailto:<?php echo $order['address1']; ?>"><?php echo $order['email']; ?></a>
						</p>
						<p>
							Order placed on <?php echo date("l, F j, Y \a\\t g:ia", strtotime($order['timestamp'])); ?><br>
							<?php
								if($order['status'] == 1) {
							?>
								Shipped on <?php echo date("l, F j, Y \a\\t g:ia", strtotime($order['shipdate'])); ?><br>
							<?php } ?>
							<?php
								if($order['trackingnumber'] != "") {
							?>
								Tracking Number: 
								<?php if($order['trackingcarrier'] != "") { ?>
									<a href="<?php echo getTrackingURLfromCarrier($order['trackingcarrier'], $order['trackingnumber']); ?>">
								<?php } ?><?php echo $order['trackingnumber']; ?><?php if($order['trackingcarrier'] != "") { ?></a> via <?php echo $order['trackingcarrier']; ?><?php } ?>
							<?php } ?>
						</p>
					</div>	
					<div class="right">
						<?php if($order['status'] == "0") { ?>
							<select id="trackingCarrier">
								<option value="FedEx"<?php if($order['trackingcarrier'] == "FedEx") { echo " selected"; } ?>>FedEx</option>
								<option value="UPS"<?php if($order['trackingcarrier'] == "UPS") { echo " selected"; } ?>>UPS</option>
								<option value="USPS"<?php if($order['trackingcarrier'] == "USPS") { echo " selected"; } ?>>USPS</option>
							</select>
							<input type="text" placeholder="Tracking Number" id="trackingNumber" value="<?php echo $order['trackingnumber']; ?>">
							<button class="ship">Ship</button>
						<?php } else { ?>
							<button class="unship">Unship</button>
						<?php } ?>
					</div>
				</div>
			<?php
				}
			?>
		</div>
	
	</body>
	
</html>