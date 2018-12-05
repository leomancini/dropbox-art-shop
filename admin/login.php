<?php
	if(isset($_POST['password'])) {
		if($_POST['password'] != "SECRET: ADMIN PASSWORD") {
			header("Location: ./");
		} else {
			setCookie("SECRET: ADMIN LOGIN COOKIE", 1);
			header("Location: ./");
		}
	} else {
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
			<script>
				$(document).ready(function() {
					$("form.login input").focus();
				});
			</script>
		</head>
		<body>
		
			<div id="dashboard">
				<form action="login.php" method="POST" class="login">
					<div class="input-wrapper"><input type="password" name="password"></div>
				</form>
			</div>
		
		</body>
	</html>
<?php
	}
?>