<?php
	include("views/popup.php");
	include("resources/functions.php");
	include("views/components.php");

	$collections = getCollections();
	
	foreach($collections as $collectionID => $collection_metadata) {
		$pieces_query = mysql_query("SELECT * FROM work WHERE collectionID = '".$collection_metadata['id']."' AND visibility = '1' ORDER BY displayOrder ASC") or die(mysql_error());
		$pieces_count = mysql_num_rows($pieces_query);
		if($pieces_count != 0) {
			render_collection($collection_metadata, $artPath, null);
		}
	}
?>