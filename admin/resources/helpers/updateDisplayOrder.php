<?php
	include("../../../resources/functions.php");
	restricted();
	
	$items = explode(',', $_GET['order']);

	if($_GET['type'] == "pieces") {
		foreach($items as $displayOrder => $itemID) {
			$result = mysql_query("UPDATE work SET displayOrder = '".$displayOrder."' WHERE id = '".$itemID."' AND collectionID = '".$_GET['collectionID']."'") or die(mysql_error());
		}
	} elseif($_GET['type'] == "collections") {
		foreach($items as $displayOrder => $collectionID) {
			$result = mysql_query("UPDATE collections SET displayOrder = '".$displayOrder."' WHERE id = '".$collectionID."'") or die(mysql_error());
		}
	}
?>