<?php
	include("../../../resources/functions.php");
	include("../../../resources/constants.php");
	restricted();
	
	$orderID = $_GET['orderID'];
	$trackingNumber = $_GET['trackingNumber'];
	$trackingCarrier = $_GET['trackingCarrier'];
	$status = $_GET['status'];
	
	// status codes
	// 0 = payment confirmed, pending action
	// 1 = shipped
	// 2 = order completed
	
	// Update order record with new status
	mysql_query("UPDATE orders SET `status` = '".$status."' WHERE `index` = '".$orderID."'");
	
	if($status == 1) {
		// Save tracking number, tracking carrier, and ship date to order record
		mysql_query("
			UPDATE orders
			SET
				`trackingnumber` = '".$trackingNumber."',
				`trackingcarrier` = '".$trackingCarrier."',
				`shipdate` = NOW()
			WHERE
				`index` = '".$orderID."'
		");

		// Get order record information
		$order_query = mysql_query("SELECT * FROM orders WHERE `index` = '".$orderID."' LIMIT 1") or die(mysql_error());
		$order = mysql_fetch_array($order_query);
		
		$itemID = str_pad($order['itemID'], 5, "0", STR_PAD_LEFT);
		
		$trackingCarrierURL = getTrackingURLfromCarrier($trackingCarrier, $trackingNumber);
		
		// Send shipping notification email to buyer
		
		$externalPrefix = "http://".$_SERVER['SERVER_NAME'];

		$item_query = mysql_query("SELECT * FROM work WHERE id = '".$order['itemID']."' LIMIT 1") or die(mysql_error());
		$item = mysql_fetch_array($item_query);
		
		$itemTitle = itemTitle($item['title'], $order['itemID']);

		$copyrightYears = '2012-'.date("Y");

		$shippingNotificationSubject = 'Your order has shipped from The Art of Laura Mancini';

		$shippingNotificationHeaders = "From: $adminEmail\r\n";
		$shippingNotificationHeaders .= "Reply-To: $adminEmail\r\n";
		$shippingNotificationHeaders .= "MIME-Version: 1.0\r\n";
		$shippingNotificationHeaders .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

		if($order['address2'] != "") { $address2_valid = "<br>".$order['address2']; }

		if($order['trackingnumber'] != "") {
			$trackingInfo = '
				<div class="label" style="
					margin-top: 30px;
					text-align: center;
					font-size: 10pt;
					font-variant: small-caps;
					text-transform: uppercase;
					color: #a8adb4;
					padding-left: 1px;
					letter-spacing: 1px;
					-webkit-font-smoothing: antialiased;
				">
					Tracking number
				</div>
				<div class="p" style="
					text-align: center;
					margin: 10px 0 30px 0;
					font-size: 13pt;
					line-height: 20pt;
					-webkit-font-smoothing: antialiased;
				">
					'.$trackingCarrier.': <a style="color: #3983ad;" href="'.$trackingCarrierURL.'">'.$trackingNumber.'</a>
				</div>
			';
		}
		$shippingNotificationMessage = '
			<html>
				<body style="margin: 0;">
					<table width="100%" height="100%" style="background: #f2f4f5; margin: 0;">
						<tr>
							<td style="text-align: center;">
								<img style="padding-top: 50px;" src="'.$externalPrefix.'/resources/images/email-header.png">
								<div class="thanks wrapper" style="margin: 50px auto; background: white; padding: 50px; width: 500px; border: 1px solid #e5e5e5; font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; text-align: left;">
									<div class="header">
										<div class="h2" style="
											text-align: center;
											font-size: 15pt;
											line-height: 20pt;
											color: #2e2e2e;
											margin-top: 0;
											margin-bottom: 5px;
											-webkit-font-smoothing: antialiased;
										">'.$order['firstname'].', your painting is on its way.</div>
										<div class="h3" style="
											color: #676b6f;
											text-align: center;
											font-size: 13pt;
											line-height: 20pt;
											font-weight: normal;
											-webkit-font-smoothing: antialiased;
										">Your recent order has shipped.</div>
									</div>

									'.$trackingInfo.'
		
									<div id="info" style="
										text-align: center;
									">
										<div class="label" style="
											font-size: 10pt;
											font-variant: small-caps;
											text-transform: uppercase;
											color: #a8adb4;
											padding-left: 1px;
											letter-spacing: 1px;
											-webkit-font-smoothing: antialiased;
										">Shipping Address</div>
										<div class="p" style="
											margin: 10px 0 30px 0;
											font-size: 13pt;
											line-height: 20pt;
											-webkit-font-smoothing: antialiased;
										">
											'.$order['firstname'].' '.$order['lastname'].'
											<br>'.$order['address1'].'
											'.$address2_valid.'
											<br>'.$order['city'].', '.$order['state'].' '.$order['zipcode'].'
										</div>
									</div>
									<div id="preview" style="
										text-align: center;
										margin-top: 50px;
									">
										<img src="'.$externalPrefix.$artPath.'thumbs/'.$itemID.'.jpg">
										<div class="p" style="
											margin-top: 25px;
											font-size: 17pt;
											line-height: 20pt;
											color: #2e2e2e;
											font-family: Georgia, serif;
											-webkit-font-smoothing: antialiased;
										">
										'.$itemTitle.'</div>
										<div class="p" style="
											margin-top: 5px;
											font-size: 15pt;
											line-height: 20pt;
											color: #676b6f;
											font-family: Georgia, serif;
											-webkit-font-smoothing: antialiased;
										">
										'.$item['description'].'</div>
									</div>
									<div class="h4" style="
											text-align: center;
											color: #a8adb4;
											font-size: 11pt;
											line-height: 18pt;
											font-weight: normal;
											margin: 50px 0 0 0;
											-webkit-font-smoothing: antialiased;
									">
										Contact <a href="mailto:'.$adminEmail.'" style="color: #676b6f;">'.$adminEmail.'</a> for<br>questions or comments.
									</div>
								</div>
								<div class="footer" style="
									font-size: 10pt;
									font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;
									color: #676b6f;
									margin-top: 50px;
									margin-bottom: 50px;
									text-align: center;
									-webkit-font-smoothing: antialiased;
								">
									All work &copy; '.$copyrightYears.' Laura Mancini
								</div>
							</td>
						</tr>
					</table>
				</body>
			</html>
		';

		mail($order['email'], $shippingNotificationSubject, $shippingNotificationMessage, $shippingNotificationHeaders);
	}
?>