<?php
	include("../resources/functions.php");
	restricted();
?>
<!DOCTYPE HTML>
<html>

	<head>
		<title>Laura Mancini</title>
		<link rel="stylesheet/less" href="resources/css/admin.less">
		<link rel="stylesheet/less" href="../resources/css/style.less">
		<script src="../resources/js/less.js"></script>
		<script src="../resources/js/jquery.js"></script>
		<script src="resources/js/ui.js"></script>
		<script src="../resources/js/delay.js"></script>
		<script src="resources/js/form.js"></script>
		<script src="resources/js/admin.js"></script>
	</head>
	<body>	

		<div id="editor" class="show-editor">
			<div id="editor-cover" class="show-editor">
				<div id="editor-contents" class="show-editor">
					<form method="POST" action="resources/helpers/updateItem.php" class="show-editor">
						<input type="hidden" id="itemID" name="itemID">
						<label for="title">Title</label><input type="text" id="title" name="title" disabled>
						<br>
						<label for="description">Description</label>
						<br>
						<textarea id="description" name="description"></textarea>
						<br>
						<label for="price">Price</label>
						<select id="price" name="price">
							<option value="25">$25</option>
							<option value="50">$50</option>
							<option value="50">$75</option>
							<option value="100" selected="selected">$100</option>
							<option value="200">$200</option>
							<option value="250">$250</option>
						</select>
						<input type="checkbox" id="availability" checked="checked" name="availability" value="1"><label for="availability">Available</label>
						<br>
						<input type="submit" value="Save">
					</form>
				</div>
			</div>
		</div>

		<header>
			<a href="." class="back">&laquo; back</a>
			<h1>Collection Editor</h1>
			<div class="right">
				<a href="#collapse-all" class="collapse-all">Collapse All</a>
				<a href="resources/helpers/createCollection.php" class="new-collection">Create New Collection</a>
				<a href="../">View Public Site</a>
			</div>
		</header>
	
		<div class="content">
			<ul class="collections-wrapper">
				<?php
					$collection_metadata_query = mysql_query("SELECT * FROM collections ORDER BY displayOrder DESC") or die(mysql_error());
					while($collection_metadata = mysql_fetch_array($collection_metadata_query)) {
						$collections[$collection_metadata['id']] = Array(
							"title" => $collection_metadata["title"],
							"id" => $collection_metadata["id"]
						);
					}

					foreach($collections as $collectionID => $collection_metadata) {
				?>
					<li class="collection-sortable" id="collectionID-<?php echo $collection_metadata['id']; ?>">
		
						<div class="collection-metadata">
							<input value="<?php echo $collection_metadata['title']; ?>" class="collection-title" data-collectionID="<?php echo $collection_metadata['id']; ?>" disabled>
							<button class="autosave">Save</button>
							&nbsp;&nbsp;&nbsp;
							<?php echo $collection_metadata['displayOrder']; ?>
							<a class="setDescriptionForCollectionItems" data-collectionID="<?php echo $collection_metadata['id']; ?>">Set description for all items</a>
							<a class="deleteCollection" data-collectionID="<?php echo $collection_metadata['id']; ?>">Delete collection</a>
							<form class="setDescriptionForCollectionItemsForm" data-collectionID="<?php echo $collection_metadata['id']; ?>">
							<br>
							<label>Set description for all items in this collection:</label><input class="collection-items-description" data-collectionID="<?php echo $collection_metadata['id']; ?>">
							<button class="confirm" data-collectionID="<?php echo $collection_metadata['id']; ?>">Set</button>
							</form>
						</div>
			
						<ul id="sortable-<?php echo $collectionID; ?>" class="collection">
							<?php
								$pieces_query = mysql_query("SELECT * FROM work WHERE collectionID = '".$collectionID."' ORDER BY displayOrder ASC") or die(mysql_error());
								while($piece = mysql_fetch_array($pieces_query)) {
									$itemID = str_pad($piece['id'], 5, "0", STR_PAD_LEFT);
									echo '<li class="image-wrapper show-editor '; if($piece['visibility'] == "1") { echo "visibility-on"; } elseif($piece['visibility'] == "0") { echo "visibility-off"; } echo '" disabledHref="/'.$collectionID.'/'.$itemID.'" data-itemID="'.$itemID.'" id="itemID-'.$piece['id'].'"><img src="'.$artPath.getFilename($collection_metadata['title'])."/thumb_".getFilename($piece['title']).'" class="show-editor"><div class="visibility-toggle"></div></a>';
								}
							?>
						</ul>
		
					</li>
	
				<?php
					}
				?>
			</ul>
		</div>
	
	</body>
	
</html>