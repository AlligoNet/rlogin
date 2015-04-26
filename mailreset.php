<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<?php
		ini_set('display_errors', 1); error_reporting(E_ALL);
		if(strlen($_POST['email'])>0 && ($_SERVER['HTTP_REFERER'] === 'https://register.crdnl.me/forgot.php' || $_SERVER['HTTP_REFERER'] === 'http://register.crdnl.me/forgot.php')){
			$useremail=$_POST['email'];
			
			//db calls to get username and set key and expiration date
			include 'config/db_credentials.php';
			$conn = mysqli_connect($dbhost , $dbuser, $dbpassword, $dbname);
			if (!$conn) {
				die("Connection failed: " . mysqli_connect_error());
			}
			
			$searchUser = $conn->prepare("SELECT radcheck_crypt.username FROM (radcheck_crypt JOIN raduseremail ON radcheck_crypt.username = raduseremail.username) WHERE raduseremail.useremail=?");
			$searchUser->bind_param("s", $useremail);
			$searchUser->execute();
			$userFind = $searchUser->get_result();
			$searchUser->close();
			if($userFind->num_rows === 0){
				echo "ERROR:user not found";
			}
			else{
				$userResult = $userFind->fetch_assoc();
				$username = $userResult['username'];
				$resetkey = base64_encode (mcrypt_create_iv ( 32 , MCRYPT_DEV_URANDOM ));
				$now = time();
				$expires = $now  + (48*60*60);
				$newResetKey = $conn->prepare('INSERT INTO radresetkey (username, resetkey, expires) VALUES(?, ?, ?)');
				$newResetKey->bind_param("sss", $username, $resetkey, $expires);
				$newResetKey->execute();
				$newResetKey->close();
				
				$pruneResetKeys = $conn->prepare('DELETE FROM radresetkey WHERE expires < ?');
				$pruneResetKeys->bind_param("s", $now);
				$pruneResetKeys->execute();
				$pruneResetKeys->close();
				
				//send actual email

				$to = $useremail;
				$subject = "Password Reset";
				$txt = "Hello " . $username . ", visit this link to reset your password: https://register.crdnl.me/pwReset.php?username=" . $username . "&resetkey=" . $resetkey . "\r\nIf you did not request a password reset, you may safely ignore this email.";
				$headers = "From: no-reply@crdnl.me";
				mail($to,$subject,$txt,$headers);
				echo 'A reset email has been sent. The included link will expire in two days.';
			}

			$conn->close();
		}
		?>
	<br><a href="index.php">Back to login page</i></a>
	</body>
</html>