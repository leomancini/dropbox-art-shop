<?php
	include("../../resources/functions.php");
	include("../../resources/constants.php");

	// more constants
	$dropbox_auth = 'Authorization: Bearer SECRET: DROPBOX AUTH CODE';
	$dropbox_folder = "SECRET: DROPBOX FOLDER DIRECTORY";
	$server_art_folder = "../..".$artPath;
	
	$thumbnail_size = 240;
	$thumbnail_quality = 100;
	
	function generate_thumbnail($new_height, $thumbnail, $full) {
		global $thumbnail_quality;
		
		$info = getimagesize($full);
		$mime = $info['mime'];

		switch($mime) {
			case 'image/jpeg':
				$image_create_func = 'imagecreatefromjpeg';
				$image_save_func = 'imagejpeg';
				break;
			case 'image/png':
				$image_create_func = 'imagecreatefrompng';
				$image_save_func = 'imagepng';
				break;
			case 'image/gif':
				$image_create_func = 'imagecreatefromgif';
				$image_save_func = 'imagegif';
				break;
			default: 
				throw new Exception('Unknown image type.');
		}

		$img = $image_create_func($full);
		list($width, $height) = getimagesize($full);
		$new_width = ($width / $height) * $new_height;
		
		$tmp = imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($tmp, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

		if (file_exists($thumbnail)) { unlink($thumbnail); }
		$image_save_func($tmp, $thumbnail, $thumbnail_quality);
	}
?>