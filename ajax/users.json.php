<?php
session_start();
?>
<?php
	if($_SESSION["status"] === 'admin'){
		//list users
		include '../config/db_credentials.php';
		$conn = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
		if (!$conn) {
			die("Connection failed: " . mysqli_connect_error());
		}
		$query = "SELECT radcheck.id, radcheck.username, raduseremail.useremail, radusergroup.groupname FROM (radcheck LEFT JOIN radusergroup ON radcheck.username=radusergroup.username) LEFT JOIN raduseremail ON radcheck.username=raduseremail.username";
		$result = $conn->query($query);
		$rows = array();
		while($row = $result->fetch_assoc()){
			array_push($rows, $row);
		}
		$conn->close();
		echo json_encode($rows);
	}
?>