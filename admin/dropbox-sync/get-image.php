<?php
	header("Content-type: image/jpeg");
	include("base.php");
	
	$path = $_GET['path'];
	if($_GET['format']) { $format = $_GET['jpeg']; } else { $format = "jpeg"; }

	if($_GET['size'] == "full") {
		$size = "w1024h768";
	} else if($_GET['size'] == "thumb") {
		$size = "w640h480";
	} else {
		$size = $_GET['size'];
	}
	
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, "https://content.dropboxapi.com/2/files/get_thumbnail"); 
	$headers = array("path" => $_GET['path'], "format" => $format, "size" => $size);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    $dropbox_auth,
		'Dropbox-API-Arg: '. json_encode($headers)
	));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $image = curl_exec($ch); 
    curl_close($ch);      
	
	echo $image;
?>