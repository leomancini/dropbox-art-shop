<?php
	// Respond to Dropbox webhook request

	if(isset($_GET["challenge"]) || $_GET['challenge'] != "") {
		echo $_GET['challenge'];
	} else {	
		// Figure out where the update_database.php script is
		$host = $_SERVER['HTTP_HOST'];
		$filename = basename($_SERVER['SCRIPT_FILENAME'], ".php");
		$directory = htmlspecialchars($_SERVER['REQUEST_URI']);
		$directory = htmlentities($directory);
		$directory = strip_tags($directory);
		$directory = str_replace($filename.".php", "", $directory);
		$update_database_script = "http://".$host.$directory.'update-database.php';

		// Curl update_database.php and don't wait for response
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $update_database_script);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
	    curl_exec($curl);
		
		echo "ran ".$update_database_script;	
	}
?>