<?php
	// Prefill form for debugging
	$form_debug = 0;
	
	include("resources/functions.php");
	include("resources/constants.php");
	
	$itemID = $_GET['item'];
	$collectionID = $_GET['collection'];
	
	$item_query = mysql_query("SELECT * FROM work WHERE id = '".$itemID."' LIMIT 1") or die(mysql_error());
	$item = mysql_fetch_array($item_query);
	
	if($item['availability'] == 0 || $item['visibility'] == 0 || isset($_COOKIE['gallerymode'])) {
?>
		<script type="text/javascript"> window.location = "../../" </script>
<?php
		die();
	}
?>
<div class="buy wrapper">
	<!--<h2>Good choice.<br>Let's get this piece into your hands.</h2>-->
	<form method="POST" action="/process-purchase">
		<input type="hidden" name="itemID" value="<?php echo $itemID; ?>">		
		<input type="hidden" name="totalAmount" value="<?php echo $item['price'] + $shippingAmount ?>">
		<div id="loading"></div>
		<div class="section">
			<div class="section-header">Basic Info</div>
			<div class="error" id="basicinfo"></div>
			
			<div class="input-wrapper" style="width: 163px;">
				<input type="text" id="firstname" name="firstname" value="<?php if($form_debug == 1) { echo "Bob"; } ?>">
				<div class="placeholder" data-for-input="firstname"><?php if($form_debug == 0) { echo "First Name"; } ?></div>
			</div>
			
			<div class="input-wrapper" style="width: 162px;">
				<input type="text" id="lastname" name="lastname" value="<?php if($form_debug == 1) { echo "Smith"; } ?>">
				<div class="placeholder" data-for-input="lastname"><?php if($form_debug == 0) { echo "Last Name"; } ?></div>
			</div>
			
			<div class="input-wrapper">
				<input type="text" id="email" name="email" value="<?php if($form_debug == 1) { echo "test@leomancinidesign.com"; } ?>">
				<div class="placeholder" data-for-input="email"><?php if($form_debug == 0) { echo "Email Address"; } ?></div>
			</div>
		</div>
			
		<div class="section">
			<div class="section-header">Shipping Address</div>
			<div class="error" id="address"></div>
			
			<div class="input-wrapper">
				<input type="text" id="address1" name="address1" value="<?php if($form_debug == 1) { echo "123 Main Street"; } ?>">
				<div class="placeholder" data-for-input="address1"><?php if($form_debug == 0) { echo "Address &nbsp; Line 1"; } ?></div>
			</div>
			
			<div class="input-wrapper">
				<input type="text" id="address2" name="address2">
				<div class="placeholder" data-for-input="address2">Address &nbsp; Line 2</div>
			</div>
			
			<div class="input-wrapper">
				<input type="text" id="zipcode" name="zipcode" maxlength="5" class="numeric" value="<?php if($form_debug == 1) { echo "33012"; } ?>">
				<div class="placeholder" data-for-input="zipcode"><?php if($form_debug == 0) { echo "ZIP Code"; } ?></div>
			</div>
			
			<div class="input-wrapper">
				<input type="text" id="city" name="city" value="<?php if($form_debug == 1) { echo "Cleveland"; } ?>">
				<div class="placeholder" data-for-input="city"><?php if($form_debug == 0) { echo "City"; } ?></div>
			</div>	

			<div class="input-wrapper">
				<input type="text" id="state" name="state" value="<?php if($form_debug == 1) { echo "Ohio"; } ?>">
				<div class="placeholder" data-for-input="state"><?php if($form_debug == 0) { echo "State"; } ?></div>
			</div>	
		</div>
		
		<div class="section">
			<div class="section-header">Payment Details</div>
			<div class="error" id="payment"></div>
			<div class="input-wrapper">
				<input type="text" id="cardname" name="cardname" data-stripe="name" value="<?php if($form_debug == 1) { echo "Bob Smith"; } ?>">
				<div class="placeholder" data-for-input="cardname"><?php if($form_debug == 0) { echo "Name on Card"; } ?></div>
			</div>
			
			<div class="input-wrapper">
				<input type="text" id="cardnumber" name="cardnumber" class="numeric" maxlength="16" data-stripe="number" value="<?php if($form_debug == 1) { echo "4242424242424242"; } ?>">
				<div class="placeholder" data-for-input="cardnumber"><?php if($form_debug == 0) { echo "Card Number"; } ?></div>
			</div>
			
			<div class="input-wrapper" style="width: 96px;">
				<input type="text" id="expirationmonth" name="expirationmonth" maxlength="2" data-stripe="exp_month" value="<?php if($form_debug == 1) { echo "04"; } ?>">
				<div class="placeholder" data-for-input="expirationmonth"><?php if($form_debug == 0) { echo "MM"; } ?></div>
			</div>
			
			<div class="input-wrapper" style="width: 96px;">
				<input type="text" id="expirationyear" name="expirationyear" maxlength="2" data-stripe="exp_year" value="<?php if($form_debug == 1) { echo "14"; } ?>">
				<div class="placeholder" data-for-input="expirationyear"><?php if($form_debug == 0) { echo "YY"; } ?></div>
			</div>
			
			<div class="input-wrapper" style="width: 97px;">
				<input type="text" id="cvc" maxlength="4" name="cvc" class="numeric" data-stripe="cvc" value="<?php if($form_debug == 1) { echo "333"; } ?>">
				<div class="placeholder" data-for-input="cvc"><?php if($form_debug == 0) { echo "CVC"; } ?></div>
			</div>
		</div>
		<div id="submit-wrapper">
			<input type="submit" disabled="disabled" value="Purchase">
		</div>
	</form>
	<div class="preview">
		<img src="<?php echo $artPath; ?>full/<?php echo $itemID; ?>.jpg">
		<h1><?php echo itemTitle($item['title'], $itemID); ?></h1>
		<?php if($item['description'] != "") { echo "<h2>".$item['description']."</h2>"; } ?>
		
		<table border="0" cellpadding="0" cellspacing="0" id="price-breakdown">
			<tr>
				<td>Price of Artwork</td>
				<td id="base-amount"><?php if($item['price'] != "0") { echo "$".$item['price']; } ?></td>
			</tr>
			<tr>
				<td>Shipping (<?php echo $shippingDays; ?> business days)</td>
				<td id="shipping-amount"><?php if($shippingAmount == 0) { echo "Free"; } else { echo '$'.$shippingAmount; } ?></td>
			</tr>
			<!--
			<tr>
				<td>Tax</td>
				<td id="tax-amount">N/A</td>
			</tr>
			-->
			<tr>
				<td>Total</td>
				<div id="pre-tax-total-amount"><?php echo $item['price'] + $shippingAmount ?></div>
				<td id="total-amount">$<?php echo $item['price'] + $shippingAmount ?></td>
			</tr>
		</table>
	</div>
</div>