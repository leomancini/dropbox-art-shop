<?php
	include("views/popup.php");
	include("resources/functions.php");
	include("views/components.php");
	
	$collections = getCollections();
	
	if(is_numeric($_GET['collection'])) {
		$collection_metadata_query = mysql_query("SELECT * FROM collections WHERE id = '".$_GET['collection']."'") or die(mysql_error());
	} else {
		$collection_title = str_replace("-", " ", $_GET['collection']);
		$collection_metadata_query = mysql_query("SELECT * FROM collections WHERE title = '".$collection_title."'") or die(mysql_error());
	}
	
	while($collection_metadata = mysql_fetch_array($collection_metadata_query)) {
		$this_collection_metadata = Array(
			"title" => $collection_metadata["title"],
			"id" => $collection_metadata["id"]
		);
	}
	
	$pieces_query = mysql_query("SELECT * FROM work WHERE collectionID = '".$this_collection_metadata['id']."' AND visibility = '1' ORDER BY displayOrder ASC") or die(mysql_error());
	$pieces_count = mysql_num_rows($pieces_query);
	if($pieces_count != 0) {
		render_collection($this_collection_metadata, $artPath, "collection-permalink");
	} else {
?>
		<script type="text/javascript"> window.location = "../../" </script>
<?php
		die();
	}
?>