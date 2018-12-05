<?php
	session_start();
	if($_GET['mode'] == 1) {
		setcookie("gallerymode", true, time()+604800);
	} elseif($_GET['mode'] == 0) {
		setcookie("gallerymode", "", time()-604800);
	}
	header("Location: /");
?>