<?php
	include("../../../resources/functions.php");
	restricted();
	
	// Escape all POST and GET variabels for security
	$safePOST = array_map('mysql_real_escape_string', $_POST);
	$safeGET = array_map('mysql_real_escape_string', $_GET);
	
	if(isset($_GET['visibility'])) {
		mysql_query("UPDATE work SET visibility = '".$safeGET['visibility']."' WHERE id = '".$safeGET['itemID']."'") or die(mysql_error());
	} else {
		$availability = (isset($_POST['availability']) && $_POST['availability'] == "1") ? 1 : 0;
		mysql_query("UPDATE work SET description = '".$safePOST['description']."', price = '".$safePOST['price']."', availability = '".$availability."' WHERE id = '".$safePOST['itemID']."'") or die(mysql_error());
	}
?>