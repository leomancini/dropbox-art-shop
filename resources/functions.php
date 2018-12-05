<?php
	include("constants.php");
	
	// Connect to database based on server
	if($_SERVER['SERVER_NAME'] == "localhost") {	
		mysql_connect(
			"SECRET: LOCAL DB SERVER",
			"SECRET: LOCAL DB USERNAME",
			"SECRET: LOCAL DB PASSWORD",
		) or die(mysql_error());
		mysql_select_db("SECRET: LOCAL DB DATABASE NAME") or die(mysql_error());
	} else {
		mysql_connect(
			"SECRET: REMOTE DB SERVER",
			"SECRET: REMOTE DB USERNAME",
			"SECRET: REMOTE DB PASSWORD",
		) or die(mysql_error());
		mysql_select_db("SECRET: REMOTE DB DATABASE NAME") or die(mysql_error());
	}

	// For items without a title, this function
	// returns a generic "Untitled #ITEMID",
	// otherwise it returns the title of the item
	function itemTitle($itemTitle, $itemID) {
		if($itemTitle == "") {
			return "Untitled #".ltrim($itemID, 0); 
		} else {
			return $itemTitle;
		}
	}
	
	// Provides a way to add only business days
	// to a specified date
	function addDays($timestamp, $days, $skipdays = array("Saturday", "Sunday"), $skipdates = NULL) {
		$i = 1;
		while ($days >= $i) {
			$timestamp = strtotime("+1 day", $timestamp);
			if (in_array(date("l", $timestamp), $skipdays)) { $days++; }
			$i++;
		}
		return $timestamp;
	}

	// Put this function at the top of any admin
	// page to restrict access to that page to
	// users that are successfully logged-in
	function restricted() {
		if(!isset($_COOKIE['SECRET: ADMIN LOGIN COOKIE'])) {
			header("Location: /admin/login.php");
			die();
		}
	}

	function getTrackingURLFromCarrier($trackingCarrier, $trackingNumber) {
		switch ($trackingCarrier) {
			case "FedEx":
			  return "https://www.fedex.com/fedextrack/index.html?tracknumbers=$trackingNumber";
			  break;
			case "UPS":
			  return "http://www.ups.com/WebTracking/track?trackNums=$trackingNumber";
			  break;
			case "USPS":
			  return "http://tools.usps.com/go/TrackConfirmAction!input.action?tLabels=$trackingNumber";
			  break;
			default:
			  return "http://google.com/?q=$trackingNumber#$trackingNumber";
		}
	}
	
	function getCollections() {
		$collections = Array();
		$collection_metadata_query = mysql_query("SELECT * FROM collections ORDER BY displayOrder DESC") or die(mysql_error());
		while($collection_metadata = mysql_fetch_array($collection_metadata_query)) {
			$collections[$collection_metadata['id']] = Array(
				"title" => $collection_metadata["title"],
				"id" => $collection_metadata["id"],
				"displayOrder" => $collection_metadata["displayOrder"]
			);
		}
	
		return $collections;
	}
	
	function getFilename($title) {
		$filename = str_replace("?", "%3F", $title);
		$filename = str_replace("&", "%26", $filename);
		$filename = str_replace("#", "%23", $filename);
		$filename = str_replace("!", "%21", $filename);
		$filename = str_replace("'", "%5C'", $filename);
		$filename = htmlspecialchars($filename);
		$filename = htmlentities($filename);
		
		return $filename;
	}
?>
