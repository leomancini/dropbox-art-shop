<?php
	include("views/popup.php");
	include("resources/functions.php");
	include("views/components.php");
	
	$collections = getCollections();
	
	foreach($collections as $collectionID => $collection_metadata) {
		render_collection($collection_metadata, $artPath, null);
	}
?>
<script>
	popup("permalink", "<?php echo str_pad($_GET['item'], 5, "0", STR_PAD_LEFT); ?>");
</script>