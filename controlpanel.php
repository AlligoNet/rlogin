<?php
session_start();
?>
<!DOCTYPE html>
<html>
	<head>

	</head>
	<body>
	<?php
		if(isset($_SESSION["name"])){
			https://register.crdnl.me/enableDirect.php?public=
				echo '<a href="https://register.crdnl.me/enableDirect.php?public=1">Enable Direct Connection</a> (Please make sure that the correct ports are forwarded to your computer and that CoolRanch is running.)<br>';
				echo '<a href="https://register.crdnl.me/enableDirect.php?public=0">Disable Direct Connection</a><br>';
				
				
				
		}
	?>
	<br>
	<form action="pwChange.php" method="POST">
		<br>Change my password<br>
		<span>Enter current password:</span><input type="password" name="oldpassword" required autocomplete="off"/><br>
		<span>Enter new password:</span><input type="password" name="password" required autocomplete="off"/>
		<input type="submit" id="button" value="Submit change">
	</form>
	<form action="delete.php" method="POST">
		<br>Delete my account<br>
		<span>Enter current password:</span><input type="password" name="password" required autocomplete="off"/>
		<input type="submit" id="button" value="Submit change">
	</form>
	<br><a href="index.php">Back to login page</i></a>
	</body>
</html>