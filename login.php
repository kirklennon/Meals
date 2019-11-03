<?php
session_start();
if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // php123

if ( isset($_POST['email']) && isset($_POST['pass']) ) {
	unset($_SESSION["name"]); // Logout current user
	
	if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION['error'] = "Email and password are required.";
        header ("Location: login.php");
		return;
    } 
	elseif ( strpos($_POST['email'], '@') === false) {
		$_SESSION['error'] = "Email must have an at-sign (@).";
        header ("Location: login.php");
		return;
	} 
	else {
		$check = hash('md5', $salt.$_POST['pass']);
        if ( $check == $stored_hash ) {
            error_log("Login success ".$_POST['email']);
        	$_SESSION['name'] = $_POST['email'];
			$_SESSION['success'] = 'Logged in.';
			header ( 'Location: view.php' );
			return;
        } else {
        	error_log("Login fail ".$_POST['email']." $check");
            $_SESSION['error'] = "Incorrect password.";
			header ("Location: login.php");
			return;
        }	
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Log in to Kirk Lennon's Automobile Database</title>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php
if ( isset($_SESSION["error"]) ) {
	echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
	unset($_SESSION["error"]);
	}
?>
<form method="POST">
<label for="email">Email</label>
<input type="text" name="email" id="email"><br/>
<label for="pass">Password</label>
<input type="text" name="pass" id="pass"><br/>
<input type="submit" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Hint: The password is the three characters of the 
language we're using followed by 123. -->
</p>
</div>
</body>
