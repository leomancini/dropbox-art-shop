<?php
	// Set base ammount and zip code from $_GET
	$baseAmount = $_GET['baseAmount'];
	$zipCode = $_GET['zipCode'];
	
	// Set tax rate
	// Tax rate is 0 right now since we're using
	// the shipping and handling fee to pay taxes later
	$taxRate = 0;
	
	// Amount of taxes for this base amount
	$taxAmount = $baseAmount * ($taxRate / 100);
	
	// Return formatted tax amount value
	setlocale(LC_MONETARY, 'en_US');
	echo money_format('%n', $taxAmount) . "\n";
?>