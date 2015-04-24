<?php
session_start();
?>
<!DOCTYPE html>
<html>

	<head>
	
	</head>

	<body>
		<?php
			if(isset($_SESSION['name'])){
				$username = $_SESSION['name'];
				$password = $_POST['password'];
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
					if(password_verify($password , $pwresult)){
						if($userResult['groupname'] === 'banned'){
							echo "ERROR:banned";
						}
						else{
							$update = $conn->prepare("DELETE FROM radcheck WHERE username=?");
							$update->bind_param("s", $username);
							$update->execute();
							$update->close();
							$update4 = $conn->prepare("DELETE FROM radcheck_crypt WHERE username=?");
							$update4->bind_param("s", $username);
							$update4->execute();
							$update4->close();
							$update2 = $conn->prepare("DELETE FROM raduseremail WHERE username=?");
							$update2->bind_param("s", $username);
							$update2->execute();
							$update2->close();
							$update3 = $conn->prepare("DELETE FROM radusergroup WHERE username=?");
							$update3->bind_param("s", $username);
							$update3->execute();
							$update3->close();
							session_unset();
							session_destroy();
							echo "Your account has been deleted.";
						}
					}
					else{
						echo "ERROR:incorrect password";
					}
				}
				
				$conn->close();
				
			}
		?>
		<br><a href="index.php">Back to main page</i></a>
	</body>

</html>