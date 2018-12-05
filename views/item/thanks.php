<?php
	include("resources/functions.php");
	include("resources/constants.php");
	
	$itemID = $_GET['item'];
	$collectionID = $_GET['collection'];
	
	if(isset($_SESSION['orderIndex']) || isset($_COOKIE['gallerymode'])) {
		$order_query = mysql_query("SELECT * FROM orders WHERE `index` = '".$_SESSION['orderIndex']."' LIMIT 1") or die(mysql_error());
		$order = mysql_fetch_array($order_query);
		$order = array_map('htmlspecialchars', $order);
		unset($_SESSION['orderIndex']);
		
		// $estimatedDelivery = date("l, F j, Y", addDays(time(), $shippingDays));
		$estimatedDelivery = $shippingDays . " business days";
?>

	<div class="thanks wrapper">
		<div class="header">
			<h2>Thanks for your purchase, <?php echo $order['firstname']; ?>.</h2>
			<h3>We've sent an email confirmation to <?php echo $order['email']; ?>.</h3>
		</div>
		<div id="info">
			<div id="preview">
				<img src="<?php echo $artPath; ?>thumbs/<?php echo $itemID; ?>.jpg">
			</div><div id="shipping">
				<label>Shipping Address</label>
				<p>
					<?php echo $order['address1']; ?>
					<?php if($order['address2'] != "") { echo "<br>".$order['address2']; } ?>
					<br><?php echo $order['city']; ?>, <?php echo $order['state']; ?> <?php echo $order['zipcode']; ?>
				</p>
				<label>Estimated delivery</label>
				<p><?php echo $estimatedDelivery; ?></p>
			</div>
		</div>
		<h4>Contact <a href="mailto:<?php echo $adminEmail; ?>"><?php echo $adminEmail; ?></a> for questions or comments.</h4>
		<!--<h5>
			<a href="#" 
			  onclick="
			    window.open(
			      'https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(location.href), 
			      'facebook-share-dialog', 
			      'width=626,height=436'); 
			    return false;">
			  Share with your Facebook friends
			</a> or <a href="../../">continue browsing</a>.</h5>-->
	</div>

<?php

	// Send confirmation email to buyer
	
	$externalPrefix = "http://".$_SERVER['SERVER_NAME'];
	
	$item_query = mysql_query("SELECT * FROM work WHERE id = '".$itemID."' LIMIT 1") or die(mysql_error());
	$item = mysql_fetch_array($item_query);
	
	$itemTitle = itemTitle($item['title'], $order['itemID']);

	$copyrightYears = '2012-'.date("Y");
	
	setlocale(LC_MONETARY, 'en_US');
	$amountpaid = money_format('%n', $order['amountpaid']);
	
	$confirmationSubject = 'Thank you for your recent purchase from The Art of Laura Mancini';

	$confirmationHeaders = "From: $adminEmail\r\n";
	$confirmationHeaders .= "Reply-To: $adminEmail\r\n";
	$confirmationHeaders .= "MIME-Version: 1.0\r\n";
	$confirmationHeaders .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	
	if($order['address2'] != "") { $address2_valid = "<br>".$order['address2']; }
	
	$confirmationMessage = '
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
									">Thanks for your purchase, '.$order['firstname'].'.</div>
									<div class="h3" style="
										color: #676b6f;
										text-align: center;
										font-size: 13pt;
										line-height: 20pt;
										font-weight: normal;
										-webkit-font-smoothing: antialiased;
									">Please keep this email as your receipt.</div>
								</div>
								
								<div id="preview" style="
									text-align: center;
									margin-top: 40px;
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
								<div id="info" style="
									margin-top: 50px;
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
									
									<div class="label" style="
										font-size: 10pt;
										font-variant: small-caps;
										text-transform: uppercase;
										color: #a8adb4;
										padding-left: 1px;
										letter-spacing: 1px;
										-webkit-font-smoothing: antialiased;
									">Estimated delivery</div>
									<div class="p" style="
										margin: 10px 0 30px 0;
										font-size: 13pt;
										line-height: 20pt;
										-webkit-font-smoothing: antialiased;
									">
									'.$estimatedDelivery.'</div>
									
									<div class="label" style="
										font-size: 10pt;
										font-variant: small-caps;
										text-transform: uppercase;
										color: #a8adb4;
										padding-left: 1px;
										letter-spacing: 1px;
										-webkit-font-smoothing: antialiased;
									">Amount paid</div>
									<div class="p" style="
										margin: 10px 0 30px 0;
										font-size: 13pt;
										line-height: 20pt;
										-webkit-font-smoothing: antialiased;
									">
									'.$amountpaid.'</div>
								</div>
								<div class="h4" style="
										text-align: center;
										color: #a8adb4;
										font-size: 11pt;
										line-height: 18pt;
										font-weight: normal;
										margin: 40px 0 0 0;
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
	
	mail($order['email'], $confirmationSubject, $confirmationMessage, $confirmationHeaders);
	
	// Send email to notification Mom
	$adminNotificationSubject = 'New order from '.$order['firstname'].' '.$order['lastname'];

	$adminNotificationHeaders = "From: new-order@artoflauramancini.com\r\n";
	$adminNotificationHeaders .= "Reply-To: ".$order['email']."\r\n";
	$adminNotificationHeaders .= "MIME-Version: 1.0\r\n";
	$adminNotificationHeaders .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
	$adminNotificationMessage = '
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
									">'.$order['firstname'].' '.$order['lastname'].' just bought this painting.</div>
									<div class="h3" style="
										color: #676b6f;
										text-align: center;
										font-size: 13pt;
										line-height: 20pt;
										font-weight: normal;
										-webkit-font-smoothing: antialiased;
									">Their email address is <a href="mailto:'.$order['email'].'" style="color: #676b6f;">'.$order['email'].'</a>.</div>
								</div>
								
								<div id="preview" style="
									text-align: center;
									margin-top: 40px;
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
								<div id="info" style="
									margin-top: 50px;
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
									
									<div class="label" style="
										font-size: 10pt;
										font-variant: small-caps;
										text-transform: uppercase;
										color: #a8adb4;
										padding-left: 1px;
										letter-spacing: 1px;
										-webkit-font-smoothing: antialiased;
									">Estimated delivery</div>
									<div class="p" style="
										margin: 10px 0 30px 0;
										font-size: 13pt;
										line-height: 20pt;
										-webkit-font-smoothing: antialiased;
									">
									'.$estimatedDelivery.'</div>
									
									<div class="label" style="
										font-size: 10pt;
										font-variant: small-caps;
										text-transform: uppercase;
										color: #a8adb4;
										padding-left: 1px;
										letter-spacing: 1px;
										-webkit-font-smoothing: antialiased;
									">Amount paid</div>
									<div class="p" style="
										margin: 10px 0 30px 0;
										font-size: 13pt;
										line-height: 20pt;
										-webkit-font-smoothing: antialiased;
									">
									'.$amountpaid.'</div>
								</div>
								<div class="h4" style="
										text-align: center;
										color: #a8adb4;
										font-size: 11pt;
										line-height: 18pt;
										font-weight: normal;
										margin: 40px 0 0 0;
										-webkit-font-smoothing: antialiased;
								">
									<a href="'.$externalPrefix.'/admin/orders.php" style="color: #676b6f;">View all pending orders</a>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</body>
		</html>
	';
	
	mail($adminEmail, $adminNotificationSubject, $adminNotificationMessage, $adminNotificationHeaders);
	
	} else {
?>
	<script type="text/javascript"> window.location = "." </script>
<?php
	}
?>