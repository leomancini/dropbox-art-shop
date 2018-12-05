<?php
	function collectionTitle($direction, $collection_id) {
		global $collections;
		
		if($direction == "display") {
			// $new_string = lowercase($string);
			$new_string = $collections[$collection_id]['title'];
		} else if($direction == "url") {
			$new_string = strtolower($collections[$collection_id]['title']);
			$new_string = str_replace(" ", "-", $new_string);
		}
	
		return $new_string;
	}

	function render_collection($collection_metadata, $artPath, $extra) {
?>
		<div class="collection wrapper <?php echo $extra; ?>" data-collection-id="<?php echo $collection_metadata['id']; ?>">
			<?php if($collection_metadata['title'] != "" && $collection_metadata['id'] != "0") { ?>
				<div class="label">
					<a href="/<?php echo collectionTitle('url', $collection_metadata['id']); ?>"><?php echo collectionTitle('display', $collection_metadata['id']); ?></a>
				</div>
			<?php } ?>
			<div class="images">
				<?php
					$pieces_query = mysql_query("SELECT * FROM work WHERE collectionID = '".$collection_metadata['id']."' AND visibility = '1' ORDER BY displayOrder ASC") or die(mysql_error());
					$pieces_count = mysql_num_rows($pieces_query);
					while($piece = mysql_fetch_array($pieces_query)) {
						$itemID = str_pad($piece['id'], 5, "0", STR_PAD_LEFT);
						echo '<a class="image-wrapper';
						if($pieces_count == 1) { echo " only-one"; }
						if($pieces_count %3 != 0) { echo " lastOdd"; }
						if($piece['for_sale'] == 1 && $piece['availability'] == 0) { echo " sold"; }
						echo '" data-itemID="'.$itemID.'">';
						echo '<img src="';
							echo $artPath;
							echo getFilename(collectionTitle('display', $collection_metadata['id']));
							echo "/";
							if($pieces_count == 1) { echo "full"; } else { echo "thumb"; }
							echo "_";
							echo getFilename($piece['title']);
						echo '"';
						echo ' class="';
						if($pieces_count == 1) { echo "only-one"; }
						echo '"></a>';
					}
				?>
			</div>
		</div>
<?php
		if($extra == "collection-permalink") {
			echo '<a href="../" class="back-home">Back to all collections</a>';
		}
	}
?>