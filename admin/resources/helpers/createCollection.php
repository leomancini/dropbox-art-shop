<?php
	include("../../../resources/functions.php");
	restricted();

	mysql_query("INSERT INTO collections (title) VALUES ('New Collection')");
	header("Location: ../../collections.php");
?>