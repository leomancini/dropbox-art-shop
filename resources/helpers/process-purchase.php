<?php
	session_start();
	// print_r($_POST);
	
	require_once('../lib/Stripe.php');
	
	include("../functions.php");
	$itemID = $_POST['itemID'];
	$price = $_POST['totalAmount'];
	
	// Clean and strip price and itemID to numbers only for security
	$price = strip_tags(preg_replace("/[^0-9\.]/", "", $price));
	$itemID = strip_tags(preg_replace("/[^0-9]/", "", $itemID));
	
	// Escape all other POST variabels for security
	$safePOST = array_map('mysql_real_escape_string', $_POST);
	
	// Get item information
	$item_query = mysql_query("SELECT * FROM work WHERE id = '".$itemID."' LIMIT 1") or die(mysql_error());
	$item = mysql_fetch_array($item_query);

	// Save to orders database
	mysql_query("
		INSERT INTO orders
			(timestamp,
			itemID,
			firstname,
			lastname,
			email,
			address1,
			address2,
			zipcode,
			city,
			state)
		VALUES
			(NOW(),
			'".$itemID."',
			'".$safePOST['firstname']."',
			'".$safePOST['lastname']."',
			'".$safePOST['email']."',
			'".$safePOST['address1']."',
			'".$safePOST['address2']."',
			'".$safePOST['zipcode']."',
			'".$safePOST['city']."',
			'".$safePOST['state']."')
	")
	or die(mysql_error());
	
	// Set your secret key: remember to change this to your live secret key in production
	// See your keys here https://manage.stripe.com/account
	Stripe::setApiKey("SECRET: STRIPE API KEY");

	// Get the credit card details submitted by the form
	$token = $_POST['stripeToken'];

	// Create the charge on Stripe's servers - this will charge the user's card
	try {
		$charge = Stripe_Charge::create(array(
		  "amount" => $price * 100, // amount in cents, again
		  "currency" => "usd",
		  "card" => $token,
		  "description" => '"'.itemTitle($item['title'], $itemID).'" by Laura Mancini')
		);
		// Get id of the row that represents this order
		$mysql_last_id = mysql_insert_id();
		// Save amount paid and card approved status
		mysql_query("UPDATE orders SET `amountpaid` = '".$price."', `cardapproved` = '1' WHERE `index` = '".$mysql_last_id."'");
		// Store this order identifier in the SESSION to pass to the thanks page
		$_SESSION['orderIndex'] = $mysql_last_id;
		// Update the work database to mark this piece as sold
		mysql_query("UPDATE work SET `availability` = '0' WHERE `id` = '".ltrim($itemID, '0')."'");
		// Redirect to the thanks page
		header("Location: /".$item['collectionID']."/".$itemID."/thanks");
	} catch(Stripe_CardError $e) {	
		// The card has been declined
		// Set card approved status to negative
		mysql_query("UPDATE orders SET `cardapproved` = '0' WHERE `index` = '".mysql_insert_id()."'");
		// Show error message for card that has been declined
		// TODO: Make this better
		echo "Sorry, your card was declined.<br><a href='javascript:history.go(-1);'>Go back</a>";
	}
	
?>