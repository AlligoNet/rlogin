<?php
session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<?php
			if($_SESSION["status"] === 'admin'){
				echo '<script src="js/adminpage4.js"></script>';				
			}
		?>
		<link rel="stylesheet" href="css/login.css">
	</head>
	<body>
		<?php
			if($_SESSION["status"] === 'admin'){
				echo '<form id="filters">';
				echo '<span>id:</span><input onkeyup="reFilter()" type="text" name="id">';
				echo '<span>name:</span><input onkeyup="reFilter()" type="text" name="username">';
				echo '<span>email:</span><input onkeyup="reFilter()" type="text" name="useremail">';
				echo '<span>group:</span><select onchange="reFilter()" type="text" name="groupname">';
				echo '<option value="">all</option>';
				echo '<option value="admin">admin</option>';
				echo '<option value="banned">banned</option>';
				echo '</select>';
				echo '</form>';
				echo '<table id="users" class="userTable">';
				echo '</table>';
			}
		?>
		<br><a href="index.php">Back to login page</i></a>
	</body>
</html>