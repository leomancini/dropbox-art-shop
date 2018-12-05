<?php
	include("../../../resources/functions.php");
	restricted();
	
	if(isset($_GET['newCollectionID']) && isset($_GET['itemID'])) {
		mysql_query("UPDATE work SET collectionID = '".mysql_real_escape_string($_GET['newCollectionID'])."' WHERE id = '".mysql_real_escape_string($_GET['itemID'])."'") or die(mysql_error());
	} elseif(isset($_GET['collectionID']) && isset($_GET['newCollectionTitle'])) {
		mysql_query("UPDATE collections SET title = '".mysql_real_escape_string($_GET['newCollectionTitle'])."' WHERE id = '".mysql_real_escape_string($_GET['collectionID'])."'");
	} elseif(isset($_GET['collectionID']) && isset($_GET['deleteCollection'])) {
		mysql_query("DELETE FROM collections WHERE id = '".mysql_real_escape_string($_GET['collectionID'])."'");
	} elseif(isset($_GET['collectionID']) && isset($_GET['setDescriptionForCollectionItems'])) {
		mysql_query("UPDATE work SET description = '".mysql_real_escape_string($_GET['setDescriptionForCollectionItems'])."' WHERE collectionID = '".mysql_real_escape_string($_GET['collectionID'])."'");
	}
?>