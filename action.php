<?php
session_start();
?>
<!DOCTYPE html>
<html>
	<body>
		<?php
			if($_SESSION["status"] === 'admin'){
				$username = $_GET["user"];
				$action = $_GET["action"];
				include 'config/db_credentials.php';
				$conn = mysqli_connect($dbhost , $dbuser, $dbpassword, $dbname);
				if (!$conn) {
					die("Connection failed: " . mysqli_connect_error());
				}
				switch($action){
					case 'ban':
						$update = $conn->prepare("UPDATE radusergroup SET groupname='banned' WHERE username=?");
						$update->bind_param("s", $username);
						$update->execute();
						$update->close();
					break;
					case 'setadmin':
						$update = $conn->prepare("UPDATE radusergroup SET groupname='admin' WHERE username=?");
						$update->bind_param("s", $username);
						$update->execute();
						$update->close();
					break;
					case 'delete':
						$update = $conn->prepare("DELETE FROM radcheck WHERE username=?");
						$update->bind_param("s", $username);
						$update->execute();
						$update->close();
						$update = $conn->prepare("DELETE FROM radcheck_crypt WHERE username=?");
						$update->bind_param("s", $username);
						$update->execute();
						$update->close();
						$update2 = $conn->prepare("DELETE FROM raduseremail WHERE username=?");
						$update2->bind_param("s", $username);
						$update2->execute();
						$update2->close();
						$update3 = $conn->prepare("DELETE FROM radusergroup WHERE username=?");
						$update3->bind_param("s", $username);
						$update3->execute();
						$update3->close();
					break;
					case 'reset':
						$update = $conn->prepare("UPDATE radusergroup SET groupname='user' WHERE username=?");
						$update->bind_param("s", $username);
						$update->execute();
						$update->close();
					break;
					default:
						echo "Invalid action " . $_GET["action"];
				}
				$conn->close();
				echo $user . " " . $action . " successful.";
			}
		?>
		<br><a href="admin.php">Back to admin page</i></a>
	</body>
</html>