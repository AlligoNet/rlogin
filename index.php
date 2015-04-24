<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head>
	<?php
		//ini_set('display_errors', 1); error_reporting(E_ALL);
		if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['name']) && !empty($_POST['password'])){
			if(!empty($_POST['email'])){
				//register new user
				$useremail = clean($_POST['email']);
				if($useremail !== $_POST['email'] || !filter_var($useremail, FILTER_VALIDATE_EMAIL)){
					$emailerr = "Please enter a valid email address.<br>";
				}
				elseif(strlen($useremail) > 50){
					$emailerr = "Please use an email address under 50 characters.<br>";
				}
			}
			$username = clean($_POST['name']);
			if($username !== $_POST['name']){
				$nameerr = "Please enter a valid username.<br>";
			}
			elseif(strlen($username) > 50){
				$nameerr = "Please use a user name under 30 characters.<br>";
			}
			$password = $_POST['password'];
			if(strlen($password) > 253){
				$pwerr = "Please use a password under 253 characters.<br>";
			}
			if(!isset($emailerr) && !isset($nameerr) && !isset($pwerr)){
				include 'config/db_credentials.php';
				$conn = mysqli_connect($dbhost , $dbuser, $dbpassword, $dbname);
				if (!$conn) {
					die("Connection failed: " . mysqli_connect_error());
				}
				if(isset($useremail)){
					//register new user
					$searchName = $conn->prepare("SELECT username FROM radcheck_crypt WHERE username=?");
					$searchName->bind_param("s", $username);
					$searchName->execute();
					$nameFind = $searchName->get_result();
					$searchName->close();
					if($nameFind->num_rows > 0){
						$nameerr = "A user with that name already exists.<br>";
					}
					$searchEmail = $conn->prepare("SELECT useremail FROM raduseremail WHERE useremail=?");
					$searchEmail->bind_param("s", $useremail);
					$searchEmail->execute();
					$emailFind = $searchEmail->get_result();
					$searchEmail->close();
					if($emailFind->num_rows > 0){
						$emailerr = "A user with that email address already exists.<br>";
					}
					if(!isset($emailerr) && !isset($nameerr)){
						//user does not exist; continue
						$insertUser = $conn->prepare("INSERT INTO radcheck (username, attribute, op, value) VALUES (?, 'Cleartext-Password', ':=', ?)");
						/*
						* inb4 >storing plaintext in db
						* We know; compatibility issues prevent normal use of hashed passwords for the VPN
						* Replacement key-based workaround backend is in place; one client update away from implementation.
						*/
						$insertUser->bind_param("ss", $username, $password);
						$insertUser->execute();
						$insertUser->close();
						$pwhash = password_hash($password, PASSWORD_BCRYPT);
						$insertUserCrypt = $conn->prepare("INSERT INTO radcheck_crypt (username, attribute, op, value) VALUES (?, 'Crypt-Password', ':=', ?)");
						$insertUserCrypt->bind_param("ss", $username, $pwhash);
						$insertUserCrypt->execute();
						$insertUserCrypt->close();
						$insertEmail = $conn->prepare("INSERT INTO raduseremail (username, useremail) VALUES (?, ?)");
						$insertEmail->bind_param("ss", $username, $useremail);
						$insertEmail->execute();
						$insertEmail->close();
						$insertGroup = $conn->prepare("INSERT INTO radusergroup (username, groupname) VALUES (?, 'user')");
						$insertGroup->bind_param("s", $username);
						$insertGroup->execute();
						$insertGroup->close();
						$err = "Registration successful.<br>";
						$_SESSION["name"] = $username;
					}
				}
				else{
					//log in existing user
					$searchUser = $conn->prepare("SELECT radcheck_crypt.value, radusergroup.groupname FROM (radcheck_crypt LEFT JOIN radusergroup ON radcheck_crypt.username = radusergroup.username) WHERE radcheck_crypt.username=?");
					$searchUser->bind_param("s", $username);
					$searchUser->execute();
					$userFind = $searchUser->get_result();
					$searchUser->close();
					if($userFind->num_rows === 0){
						$nameerr = "A user with that name was not found.<br>";
					}
					else{
						$userResult = $userFind->fetch_assoc();
						$pwresult = $userResult['value'];
						if(password_verify($password , $pwresult)){
							if($userResult['groupname'] === 'banned'){
								$err = "You are banned<br>";
							}
							else{
								$err = "Logged in successfully<br>";
								$_SESSION["name"] = $username;
								$_SESSION["status"] = $userResult['groupname'];
							}
						}
						else{
							$pwerr = "Incorrect password.";
						}
					}
				}
				$conn->close();
			}
			else{
				$err = "Problems were found with your input:<br>";
			}
		}
		function clean($input){
			return htmlspecialchars(stripslashes(trim($input)));
		}
	?>
  <meta charset="UTF-8">

  <title>OracleNet Signup/Login</title>

  <link href='//fonts.googleapis.com/css?family=Titillium+Web:400,300,600' rel='stylesheet' type='text/css'>

  <link rel="stylesheet" href="css/normalize.css">

  <link rel="stylesheet" href="css/style.css">
  
  <link rel="stylesheet" href="css/login2.css">

</head>

<body>
	<div class="statusbar">
	<?php
	
	if(isset($_SESSION["name"])){
		echo 'You are logged in as ' . $_SESSION["name"];
		echo '. <a href="logout.php">Log Out</a>';
		echo ' |  <a href="controlpanel.php">Settings</a>';
		if($_SESSION["status"] === 'admin') echo ' | <a href="admin.php">Admin Control Panel</a>';
		
	}
	else{
		echo 'You are not currently logged in.';
	}
	$versionNumber = trim(file_get_contents('https://files.fractalcore.net/oracle/VERSION'));
	$versionLink = trim(file_get_contents('https://files.fractalcore.net/oracle/LATEST'));
	echo "<span class=\"rightFloat\">The latest version of the OracleNet Client is " . $versionNumber . ". Get the latest version <a href=\"" . $versionLink . "\">here.</a></span>";
	//echo '<br>' . $_POST['name'];
	?>
	</div>
	<?php
	if(isset($err) | isset($nameerr) || isset($emailerr) || isset($pwerr)){
		echo '<div class="statusbarrev">';
		echo $err;
		echo $nameerr;
		echo $emailerr;
		echo $pwerr;
		echo '</div>';
	}
	?>
  <div class="form">

      <ul class="tab-group">
        <li class="tab active"><a href="#signup">Sign Up</a></li>
        <li class="tab"><a href="#login">Log In</a></li>
      </ul>

      <div class="tab-content">
        <div id="signup">   
          <h1>Sign Up for OracleNet</h1>

          <form method="post">
			
            <div class="field-wrap">
              <label>
                Username<span class="req" >*</span>
              </label>
              <input type="text" name="name" required autocomplete="off" />
            </div>

          

          <div class="field-wrap">
            <label>
              Email Address<span class="req">*</span>
            </label>
            <input type="email" name="email" required autocomplete="off"/>
          </div>

          <div class="field-wrap">
            <label>
              Set A Password<span class="req">*</span>
            </label>
            <input type="password" name="password" required autocomplete="off"/>
          </div>

          <button type="submit" class="button button-block">Get Started</button>

          </form>

        </div>

        <div id="login">   
          <h1>Welcome Back!</h1>

          <form method="post">
			
            <div class="field-wrap">
            <label>
              Username<span class="req">*</span>
            </label>
            <input type="text" name="name" required autocomplete="off"/>
          </div>

          <div class="field-wrap">
            <label>
              Password<span class="req">*</span>
            </label>
            <input type="password" name="password" required autocomplete="off"/>
          </div>

          <p class="forgot"><a href="#">Forgot Password?</a></p>

          <button class="button button-block">Log In</button>

          </form>

        </div>

      </div><!-- tab-content -->

</div> <!-- /form -->

  <script src='//codepen.io/assets/libs/fullpage/jquery.js'></script>

  <script src="js/index.js"></script>

</body>

</html>