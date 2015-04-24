<?php
session_start();
?>
<!DOCTYPE html>
<html>
	<body>
		<?php
			session_unset();
			session_destroy();
			echo "You have logged out.";
		?>
		<br><a href="https://register.crdnl.me">Back to login page</i></a>
	</body>
</html>