<?php
session_start();
?>
<!DOCTYPE html>
<html>

	<head>
	<?php
		if(isset($_SESSION['name'])){
			$username = $_SESSION['name'];
			$password = $_POST['password'];
			$pwhash = password_hash($password, PASSWORD_BCRYPT);
			$username = $_SESSION['name'];
			include 'config/db_credentials.php';
			$conn = mysqli_connect($dbhost , $dbuser, $dbpassword, $dbname);
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			$radCheckString = "UPDATE radcheck SET value=? WHERE username=?";
			$setDirect = $conn->prepare($radCheckString);
			$setDirect->bind_param("ss", $password, $username);
			$setDirect->execute();
			$setDirect->close();
			$radCheckCryptString = "UPDATE radcheck_crypt SET value=? WHERE username=?";
			$setDirect2 = $conn->prepare($radCheckCryptString);
			$setDirect2->bind_param("ss", $pwhash, $username);
			$setDirect2->execute();
			$setDirect2->close();
			$conn->close();
		}
	?>
	</head>

	<body>
		<?php
		echo "Your password has been changed successfully.";
		?>
		<br><a href="controlpanel.php">Back to settings</i></a>
	</body>

</html>