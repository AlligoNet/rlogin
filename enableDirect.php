<?php
session_start();
?>
<!DOCTYPE html>
<html>

	<head>
	<?php
		if(isset($_SESSION['name']) && isset($_GET['public'])){
			$public = ($_GET['public'] ? 'TRUE':'FALSE');
			$prepString = ($_GET['public'] ? "INSERT INTO raduserpublic (username, public) VALUES (?, 1) ON DUPLICATE KEY UPDATE public=1" : "INSERT INTO raduserpublic (username, public) VALUES (?, 0) ON DUPLICATE KEY UPDATE public=0" );
			$username = $_SESSION['name'];
			include 'config/db_credentials.php';
			$conn = mysqli_connect($dbhost , $dbuser, $dbpassword, $dbname);
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			$setDirect = $conn->prepare($prepString);
			$setDirect->bind_param("s", $username);
			$setDirect->execute();
			$setDirect->close();
			$conn->close();
		}
	?>
	</head>

	<body>
		<?php
		if($_GET['public']){
			echo "Direct connections are now enabled for your account.";
		}
		else{
			echo "Direct connections are now disabled for your account.";
		}
		?>
		<br><a href="controlpanel.php">Back to settings</i></a>
	</body>

</html>