<!DOCTYPE html>
<html>

	<head>
	
	</head>

	<body>
		<?php
		ini_set('display_errors', 1); error_reporting(E_ALL);
		if(isset($_GET['resetkey']) && isset($_GET['username'])){
			$username = $_GET['username'];
			$resetKey = $_GET['resetkey'];
			
			include 'config/db_credentials.php';
			$conn = mysqli_connect($dbhost , $dbuser, $dbpassword, $dbname);
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			
			$searchUser = $conn->prepare("SELECT expires FROM radresetkey WHERE username=? AND resetkey=?");
			$searchUser->bind_param("ss", $username, $resetKey);
			$searchUser->execute();
			$userFind = $searchUser->get_result();
			$searchUser->close();
			if($userFind->num_rows === 0){
				echo "ERROR:key not found";
			}
			else{
				$userResult = $userFind->fetch_assoc();
				$pwresult = $userResult['expires'];
				if(intval($pwresult) > intval(time())){
					$password = base64_encode (mcrypt_create_iv ( 16 , MCRYPT_DEV_URANDOM ));
					$radCheckString = "UPDATE radcheck SET value=? WHERE username=?";
					$setDirect = $conn->prepare($radCheckString);
					$setDirect->bind_param("ss", $password, $username);
					$setDirect->execute();
					$setDirect->close();
					$pwhash = password_hash($password, PASSWORD_BCRYPT);
					$radCheckCryptString = "UPDATE radcheck_crypt SET value=? WHERE username=?";
					$setDirect2 = $conn->prepare($radCheckCryptString);
					$setDirect2->bind_param("ss", $pwhash, $username);
					$setDirect2->execute();
					$setDirect2->close();
					echo 'Your new password is: ' . $password . '<br>Please log in and change it in your user control panel.';
					$clearkey = $conn->prepare("DELETE FROM radresetkey WHERE username=? AND resetkey=?");
					$clearkey->bind_param("ss", $username, $resetKey);
					$clearkey->execute();
					$clearkey->close();
				}
				else{
					echo "ERROR:expired key";
				}
			}
			
			
			
			
			
			
			
			
			
			$conn->close();
		}
		else{
			echo 'input not found.';
		}
	?>
		<br><a href="index.php">Back to login page</i></a>
	</body>

</html>