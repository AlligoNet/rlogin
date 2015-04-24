<?php
session_start();
?>
<!DOCTYPE html>
<html>

	<head>
	<?php
		if(isset($_SESSION['name']) && strlen($_POST['password'])){
			$username = $_SESSION['name'];
			$password = $_POST['password'];
			$oldpass = $_POST['oldpassword'];
			$pwhash = password_hash($password, PASSWORD_BCRYPT);
			include 'config/db_credentials.php';
			$conn = mysqli_connect($dbhost , $dbuser, $dbpassword, $dbname);
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			
			$searchUser = $conn->prepare("SELECT radcheck_crypt.value, radusergroup.groupname FROM (radcheck_crypt LEFT JOIN radusergroup ON radcheck_crypt.username = radusergroup.username) WHERE radcheck_crypt.username=?");
			$searchUser->bind_param("s", $username);
			$searchUser->execute();
			$userFind = $searchUser->get_result();
			$searchUser->close();
			if($userFind->num_rows === 0){
				echo "ERROR:user not found";
			}
			else{
				$userResult = $userFind->fetch_assoc();
				$pwresult = $userResult['value'];
				if(password_verify($oldpass , $pwresult)){
					if($userResult['groupname'] === 'banned'){
						echo "ERROR:banned";
					}
					else{
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
					}
				}
				else{
					echo "ERROR:incorrect password";
				}
			}
			
			
			
			
			
			
			
			
			
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