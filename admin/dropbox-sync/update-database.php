<!DOCTYPE HTML>
<html>

	<head>
		<title>Sync Dropbox with Server</title>
		<link rel="stylesheet" href="resources/css/style.css">
		<script src="http://library.noshado.ws/js/jquery.js"></script>
		<script src="resources/js/main.js"></script>
	</head>
	<body>
		
		<?php
			// error_reporting(E_ALL);
			// ini_set('display_errors', 1);
		
			include("base.php");
	
			function download_from_dropbox($collection_title, $work_title, $size, $format) {
				global $dropbox_auth;
				global $dropbox_folder;
				global $server_art_folder;
				global $thumbnail_size;
				
				$path_to_dropbox_image = $dropbox_folder.$collection_title."/".$work_title;
				
				$collection_title = mysql_real_escape_string($collection_title);
				$work_title = mysql_real_escape_string($work_title);
				
				$path_to_saved_image_full = $server_art_folder.$collection_title."/full_".$work_title;
				$path_to_saved_image_thumb = $server_art_folder.$collection_title."/thumb_".$work_title;
				
				if($size == "full") {
					$dropbox_size = "w1024h768";
				} elseif($size == "thumb") {
					$dropbox_size = "w640h480";
				} else {
					$dropbox_size = $size;
				}
					
				set_time_limit(0);
				
				$fp = fopen($path_to_saved_image_full, 'w+');
				
				$ch = curl_init(); 
				curl_setopt($ch, CURLOPT_URL, "https://content.dropboxapi.com/2/files/get_thumbnail"); 
				$headers = array("path" => $path_to_dropbox_image, "format" => $format, "size" => $dropbox_size);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					$dropbox_auth,
					'Dropbox-API-Arg: '. json_encode($headers)
				));
				curl_setopt($ch, CURLOPT_TIMEOUT, 0); 
				// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_FILE, $fp);
				$image = curl_exec($ch); 
				curl_close($ch);
				
				generate_thumbnail($thumbnail_size, $path_to_saved_image_thumb, $path_to_saved_image_full);
			}
							
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, "https://api.dropboxapi.com/2/files/list_folder"); 
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			    $dropbox_auth,
			    'Content-Type: application/json'
			));
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('path'=> $dropbox_folder)));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			$files = curl_exec($ch); 
			curl_close($ch);      

			$files = json_decode($files, 1);

			$dropbox_collections = Array();
			$dropbox_works = Array();
	
			foreach($files["entries"] as $file) {
				if($file[".tag"] == "folder") {
				    $ch = curl_init(); 
				    curl_setopt($ch, CURLOPT_URL, "https://api.dropboxapi.com/2/files/list_folder"); 
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					    $dropbox_auth,
					    'Content-Type: application/json'
					));
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('path'=> $dropbox_folder.$file["name"])));
				    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
				    $files = curl_exec($ch); 
				    curl_close($ch);      
			

					$files = json_decode($files, 1);

					$dropbox_collections[] = $file["name"];
					
					foreach($files["entries"] as $image) {
						$dropbox_works[] = Array(
							"collectionTitle" => $file["name"],
							"title" => $image['name']
						);
					}
				}
			}
	
	
			// DATABASE QUERIES

			$database_collections = Array();
			$database_collections_query = "SELECT * FROM collections"; 
			$database_collections_results = mysql_query($database_collections_query) or die(mysql_error());

			while($database_collection_result = mysql_fetch_array($database_collections_results)) {
				$database_collections[$database_collection_result['id']] = $database_collection_result['title'];
			}
	
			$database_works = Array();
			$database_works_query = "SELECT * FROM work"; 
			$database_works_results = mysql_query($database_works_query) or die(mysql_error());

			while($database_work_result = mysql_fetch_array($database_works_results)){	
				$database_works[] = Array(
					"collectionID" => $database_work_result['collectionID'],
					"id" => $database_work_result['id'],
					"title" => $database_work_result['title'],
					"displayOrder" => $database_work_result['displayOrder'],
				);
			}
			
			
			// ARRAYS FOR COLLECTION/WORK PAIR COMPARISON
	
			$database_collection_and_work_pair = Array();
			foreach($database_works as $database_work) {
				$database_collection_and_work_pair[] = $database_collections[$database_work['collectionID']]."/".$database_work["title"];
			}
						
			$dropbox_collection_and_work_pair = Array();
			foreach($dropbox_works as $dropbox_work) {
				$dropbox_collection_and_work_pair[] = $dropbox_work["collectionTitle"]."/".$dropbox_work["title"];
			}
			
	
			// COMPARISON
			
			echo "<h3>Database</h3>";
		
			echo "<h4>work</h3>";
			foreach($database_works as $database_work) {
				echo "<div class='row'>";
				
				echo "<span class='title'>".$database_collections[$database_work['collectionID']]."/".$database_work["title"]."</span>";
				
				// echo "<img src='get_image.php?path=".$dropbox_folder.$database_work["collectionTitle"]."/".$database_work["workTitle"]."&size=thumb'><br><br><br>";
				if(in_array($database_collections[$database_work['collectionID']]."/".$database_work["title"], $dropbox_collection_and_work_pair)) {
					// Database work exists in Dropbox
					echo "<span class='label maintain'>MAINTAIN</span> <span class='description'>exists in Dropbox</span>";
				} else {
					// Database work does not exist in Dropbox
					echo "<span class='label delete'>DELETE</span> <span class='description'>does not exist in Dropbox</span>";
					mysql_query("DELETE FROM work WHERE id = '".$database_work["id"]."' AND collectionID = ' ".$database_work['collectionID']."'");
					unlink($server_art_folder.$database_collections[$database_work['collectionID']]."/full_".$database_work["title"]);
					unlink($server_art_folder.$database_collections[$database_work['collectionID']]."/thumb_".$database_work["title"]);
				}
		
				echo "</div>";
			}
			
			echo "<h4>collections</h3>";
			foreach($database_collections as $database_collection_title) {
				echo "<div class='row'>";
				
				echo "<span class='title'>".$database_collection_title."</span>";
		
				if(in_array($database_collection_title, $dropbox_collections)) {
					// Database collection exists in Dropbox 
					echo "<span class='label maintain'>MAINTAIN</span> <span class='description'>exists in Dropbox</span>";
				} else {
					// Database collection does not exist in Dropbox 
					echo "<span class='label delete'>DELETE</span> <span class='description'>does not exist in Dropbox</span>";
					mysql_query("DELETE FROM collections WHERE title = '".$database_collection_title."'");
					rmdir($server_art_folder.$database_collection_title);
				}
		
				echo "</div>";
			}
	
			echo "<br><br>";
	
			echo "<h3>Dropbox</h3>";
			
			$old_and_new_database_collections = $database_collections;
			$last_displayOrder_per_collection = Array();
			$collection_displayOrder_counter = 0;
			
			echo "<h4>collections</h3>";
			foreach(array_unique($dropbox_collections) as $dropbox_collection_title) {
				echo "<div class='row'>";
				
				echo "<span class='title'>".$dropbox_collection_title."</span>";
		
				if(in_array($dropbox_collection_title, $database_collections)) {
					// Dropbox collection exists in database 
					echo "<span class='label maintain'>MAINTAIN</span> <span class='description'>exists in database</span>";
				} else {
					// Dropbox collection does not exist in database 
					echo "<span class='label add'>ADD</span> <span class='description'>does not exist in database</span>";
					
					$collection_with_highest_displayOrder_result = mysql_query("SELECT * FROM collections ORDER BY displayOrder DESC LIMIT 1");
					$collection_with_highest_displayOrder = mysql_fetch_array($collection_with_highest_displayOrder_result);
					
					$collection_displayOrder_counter++;
					$next_collection_displayOrder = $collection_with_highest_displayOrder['displayOrder'] + $collection_displayOrder_counter;

					$next_collection_displayOrder = mysql_real_escape_string($next_collection_displayOrder);
					$dropbox_collection_title = mysql_real_escape_string($dropbox_collection_title);
					
					mysql_query("INSERT INTO collections (displayOrder, title) VALUES ('".$next_collection_displayOrder."', '".$dropbox_collection_title."')");
					mkdir($server_art_folder.$dropbox_collection_title, 0777);
					
					$new_collection_id = mysql_insert_id();
					$new_collections_results = mysql_query("SELECT * FROM collections WHERE id = '".$new_collection_id."'");
					$new_collection = mysql_fetch_array($new_collections_results);
					$old_and_new_database_collections[$new_collection_id] = $new_collection['title'];	
				}
			
				echo "</div>";
			}
			
			foreach($old_and_new_database_collections as $old_and_new_database_collection_id => $old_and_new_database_collection_title) {
				$last_work_inserted_to_this_collections_results = mysql_query("SELECT * FROM work WHERE collectionID = '".$old_and_new_database_collection_id."' ORDER BY displayOrder DESC LIMIT 1");
				$last_work_inserted_to_this_collection = mysql_fetch_array($last_work_inserted_to_this_collections_results);
				$last_displayOrder_per_collection[$old_and_new_database_collection_id] = $last_work_inserted_to_this_collection['displayOrder'];
			}
			
			$displayOrder_counter = Array();
			$new_dropbox_works = Array();
			
			echo "<h4>work</h3>";
			foreach($dropbox_works as $dropbox_work) {
				echo "<div class='row'>";
				
				echo "<span class='title'>".$dropbox_work["collectionTitle"]."/".$dropbox_work["title"]."</span>";
		
				if(in_array($dropbox_work["collectionTitle"]."/".$dropbox_work["title"], $database_collection_and_work_pair)) {
					// Dropbox work exists in database 
					echo "<span class='label maintain'>MAINTAIN</span> <span class='description'>exists in database</span>";
				} else {
					// Dropbox work does not exist in database
					echo "<span class='label add'>ADD</span> <span class='description'>does not exist in database</span>";
					$collectionID = array_search($dropbox_work["collectionTitle"], $old_and_new_database_collections);
					
					$displayOrder_counter[$collectionID]++;
					$next_displayOrder = $last_displayOrder_per_collection[$collectionID] + $displayOrder_counter[$collectionID];
			
					mysql_query("INSERT INTO work (displayOrder, title, timestamp, availability, for_sale, visibility, collectionID) VALUES ('".mysql_real_escape_string($next_displayOrder)."', '".mysql_real_escape_string($dropbox_work["title"])."', '".time()."', '1', '0', '1', '".mysql_real_escape_string($collectionID)."')");
				
					download_from_dropbox($dropbox_work["collectionTitle"], $dropbox_work["title"], "full", "jpeg");
				}
		
				echo "</div>";
				
				// echo "<img src='get_image.php?path=".$dropbox_folder.$dropbox_work["collectionTitle"]."/".$dropbox_work["title"]."&size=thumb'><br><br><br>";
			}
			
		?>
	</body>
</html>