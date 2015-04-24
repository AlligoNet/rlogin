<?php
ini_set('display_errors', 1); error_reporting(E_ALL);
if(strlen($_POST['username']) && strlen($_POST['password'])){
	$username = $_POST['username'];
	$password = $_POST['password'];
	$publicIP = $_SERVER["REMOTE_ADDR"];
	include 'config/db_credentials.php';
	$conn = mysqli_connect($dbhost , $dbuser, $dbpassword, $dbname);
	if (!$conn) {
		die("ERROR:connection failed");
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
				$key = base64_encode (mcrypt_create_iv ( 128 , MCRYPT_DEV_URANDOM ));
				$newKey = $conn->prepare("UPDATE radcheck SET value=? WHERE username=?");
				$newKey->bind_param("ss", $key, $username);
				$newKey->execute();
				$newKey->close();
				echo "SUCCESS:" . $key;
				
				$clearTable = $conn->prepare("DELETE FROM OracleNetDB.IPAddresses WHERE username=?");
				$clearTable->bind_param("s", $username);
				$clearTable->execute();
				$clearTable->close();
				
				$clearTable2 = $conn->prepare("DELETE FROM OracleNetDB.IPAddresses WHERE publicIP=?");
				$clearTable2->bind_param("s", $publicIP);
				$clearTable2->execute();
				$clearTable2->close();
				
				$makeTable = $conn->prepare("INSERT INTO OracleNetDB.IPAddresses (username, publicIP, port) VALUES(?, ?, 11770)");
				$makeTable->bind_param("ss", $username, $publicIP);
				$makeTable->execute();
				$makeTable->close();
			}
		}
		else{
			echo "ERROR:incorrect password";
		}
	}
	$conn->close();
}
else{
	echo "ERROR:missing input";
}
?>