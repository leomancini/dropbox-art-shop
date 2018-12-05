<?php
	// COLLECTION ORDER IS DISPLAYED AS REVERSED FROM DATABASE ORDER
	
	include("../functions.php");
	
	$pieces = array();
	if(isset($_GET['admin'])) {
		$query = mysql_query("SELECT * FROM work WHERE id = '".$_GET['itemID']."'") or die(mysql_error());
	} else {
		$query = mysql_query("SELECT * FROM work WHERE id = '".$_GET['itemID']."' AND visibility = '1'") or die(mysql_error());	
	}
		
	// SETUP
	
	// first collection
	$firstCollection = mysql_query("SELECT * FROM collections ORDER BY displayOrder DESC LIMIT 1") or die(mysql_error());
	$firstCollection = mysql_fetch_array($firstCollection);
	
	// last collection
	$lastCollection = mysql_query("SELECT * FROM collections ORDER BY displayOrder ASC LIMIT 1") or die(mysql_error());
	$lastCollection = mysql_fetch_array($lastCollection);
	
	// first piece
	$firstPiece = mysql_query("SELECT * FROM work WHERE visibility = '1' ORDER BY displayOrder ASC LIMIT 1") or die(mysql_error());
	$firstPiece = mysql_fetch_array($firstPiece);

	// last piece
	$lastPiece = mysql_query("SELECT * FROM work WHERE visibility = '1' ORDER BY displayOrder DESC LIMIT 1") or die(mysql_error());
	$lastPiece = mysql_fetch_array($lastPiece);
	
	// all collections
	$allCollections = mysql_query("SELECT * FROM collections") or die(mysql_error());
	
	while($piece = mysql_fetch_array($query)) {
		
		// EACH PIECE
		
		// all pieces in the collection that this piece is in
		$allPiecesInCollection = mysql_query("SELECT * FROM work WHERE collectionID = '".$piece['collectionID']."' AND visibility = '1'") or die(mysql_error());
		
		// collection that this piece is in
		$thisCollection = mysql_query("SELECT * FROM collections WHERE id = '".$piece['collectionID']."'") or die(mysql_error());
		$thisCollection = mysql_fetch_array($thisCollection);

		// first piece in the collection that this piece is in
		$firstPieceInCollection = mysql_query("SELECT * FROM work WHERE collectionID = '".$piece['collectionID']."' AND visibility = '1' ORDER BY displayOrder ASC LIMIT 1") or die(mysql_error());
		$firstPieceInCollection = mysql_fetch_array($firstPieceInCollection);
		
		// last piece in the collection that this piece is in
		$lastPieceInCollection = mysql_query("SELECT * FROM work WHERE collectionID = '".$piece['collectionID']."' AND visibility = '1' ORDER BY displayOrder DESC LIMIT 1") or die(mysql_error());
		$lastPieceInCollection = mysql_fetch_array($lastPieceInCollection);
		
		
		// NEXT ITEM
		
		if($piece['displayOrder'] == $lastPieceInCollection['displayOrder']) {
			// if this is the last piece in the collection
			if($thisCollection['displayOrder'] == $lastCollection['displayOrder']) {
				 // if this is the last collection, set the next collection to the first collection
				$nextCollection = mysql_query("SELECT * FROM collections WHERE displayOrder = '".$firstCollection['displayOrder']."' ORDER BY displayOrder DESC LIMIT 1") or die(mysql_error());
			} else {
				// otherwise set the next collection to the collection with the next largest displayOrder
				$nextCollection = mysql_query("SELECT * FROM collections WHERE displayOrder < '".$thisCollection['displayOrder']."' ORDER BY displayOrder DESC LIMIT 1") or die(mysql_error());
			}
			
			$nextCollection = mysql_fetch_array($nextCollection);

			// the next item is the first piece in the next collection
			$firstPieceInNextCollection = mysql_query("SELECT * FROM work WHERE collectionID = '".$nextCollection['id']."' AND visibility = '1' ORDER BY displayOrder ASC LIMIT 1") or die(mysql_error());
			$firstPieceInNextCollection = mysql_fetch_array($firstPieceInNextCollection);
			$nextItemID = str_pad($firstPieceInNextCollection['id'], 5, "0", STR_PAD_LEFT);
			
			// THERE IS A BUG HERE IF THE NEXT COLLECTION HAS NO WORK THAT IS VISIBLE, IT WON'T RETURN A NEXT ITEM ID

		} else {
			// if this is not the last piece in this collection, the next item is the next item with the next largest displayOrder in this collection
			$nextItem = mysql_query("SELECT * FROM work WHERE displayOrder > '".$piece['displayOrder']."' AND collectionID = '".$piece['collectionID']."' AND visibility = '1' ORDER BY displayOrder ASC LIMIT 1") or die(mysql_error());
			$nextItem = mysql_fetch_array($nextItem);
			$nextItemID = str_pad($nextItem['id'], 5, "0", STR_PAD_LEFT);
		}
		
		// PREVIOUS ITEM
		
		if($piece['displayOrder'] == $firstPieceInCollection['displayOrder']) {
			// if this is the first piece in the collection
			if($thisCollection['displayOrder'] == $firstCollection['displayOrder']) {
				// if this is the first collection, set the previous collection to the last collection
				$prevCollection = mysql_query("SELECT * FROM collections WHERE displayOrder = '".$lastCollection['displayOrder']."' ORDER BY displayOrder ASC LIMIT 1") or die(mysql_error());
			} else {
				// otherwise set the previous collection to the collection with the next smallest displayOrder
				$prevCollection = mysql_query("SELECT * FROM collections WHERE displayOrder > '".$thisCollection['displayOrder']."' ORDER BY displayOrder ASC LIMIT 1") or die(mysql_error());
			}
			
			$prevCollection = mysql_fetch_array($prevCollection);
			
			// the previous item is the last piece in the previous collection
			$lastItemInPreviousCollection = mysql_query("SELECT * FROM work WHERE collectionID = '".$prevCollection['id']."' AND visibility = '1' ORDER BY displayOrder DESC LIMIT 1") or die(mysql_error());
			$lastItemInPreviousCollection = mysql_fetch_array($lastItemInPreviousCollection);
			$prevItemID = str_pad($lastItemInPreviousCollection['id'], 5, "0", STR_PAD_LEFT);
			
			// THERE IS A BUG HERE IF THE PREV COLLECTION HAS NO WORK THAT IS VISIBLE, IT WON'T RETURN A PREV ITEM ID
			
		} else {
			// if this is not the first piece in this collection, the previous item is the previous item with the next smallest displayOrder in this collection
			$prevItem = mysql_query("SELECT * FROM work WHERE displayOrder < '".$piece['displayOrder']."' AND collectionID = '".$piece['collectionID']."' AND visibility = '1' ORDER BY displayOrder DESC LIMIT 1") or die(mysql_error());
			$prevItem = mysql_fetch_array($prevItem);
			$prevItemID = str_pad($prevItem['id'], 5, "0", STR_PAD_LEFT);
		}
		
		// CLEAN
		
		$filename = getFilename($piece['title']);
		
		$title = str_replace(".jpeg", "", $piece['title']);
		$title = str_replace(".jpg", "", $title);
		$title = str_replace(".png", "", $title);
		$title = str_replace(".gif", "", $title);
		
		// APPEND TO OUTPUT
		
		array_push($pieces, array(
			"id" => $piece['id'],
			"displayOrder" => $piece['displayOrder'],
			"collectionID" => $piece['collectionID'],
			"collectionTitle" => $thisCollection['title'],
			"collectionTitlePath" => getFilename($thisCollection['title']),
			"title" => $title,
			"filename" => $filename,
			"description" => $piece['description'],
			"price" => $piece['price'],
			"for_sale" => $piece['for_sale'],
			"availability" => $piece['availability'],
			"prevItemID" => $prevItemID,
			"nextItemID" => $nextItemID
		));
	}
	
	// OUTPUT
	
	echo json_encode($pieces);
?>