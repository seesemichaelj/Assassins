<?php
include_once("config.php");

$errors = "";
$missing_required = "";

function error($errors, $message)
{
	if($errors == "")
		$errors .= $message;
	else
		$errors .= "<br>" . $message;
	return $errors;
}

function missing_required($missing_required, $field)
{
	if($missing_required == "")
		$missing_required = "Missing required fields: " . $field;
	else
		$missing_required .= ", " . $field;
	return $missing_required;
}

if(session_id() != "")
{
	$sessionid = session_id();
	$ipaddr = $_SERVER['REMOTE_ADDR'];
	$query = sprintf("SELECT * FROM sessions WHERE sessionid='%s'",$sessionid);
	$result = mysql_query($query);
	$row = mysql_fetch_assoc($result);
	if($row['sessionid'] == $sessionid)
	{
		if($row['ipaddr'] == $ipaddr)
		{
			header("location: /");
		}
	}
}
if($_POST['register'] == "true")
{
	$username = $_POST['username'];
	$password = $_POST['password'];
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$confirm_password = $_POST['confirm_password'];
	$email = $_POST['email'];
	$confirm_email = $_POST['confirm_email'];
	
	// Check for errors
	
	//check if required fields are entered
	if($username == "")
	{
		$missing_required = missing_required($missing_required, "Username");
	}
	else
	{
		//check if username used
		$query = sprintf("SELECT * FROM users WHERE username='%s'",$username);
		$result = mysql_query($query);
		if(mysql_num_rows($result) == 1)
			$errors = error($errors, "Username is not available.");
	}
	if($password == "")
		$missing_required = missing_required($missing_required, "Password");
	else
	{
		//see if password is long enough
		if(strlen($password) < 4)
			$errors = error($errors, "Password needs to be longer than four characters.");
	}
	if($confirm_password == "")
		$missing_required = missing_required($missing_required, "Confirm Password");
	else
	{
		//check confirm password
		if($password != $confirm_password)
			$errors = error($errors, "Your confirm password does not match the password given.");
	}
	if($first_name == "")
		$missing_required = missing_required($missing_required, "First Name");
	if($last_name == "")
		$missing_required = missing_required($missing_required, "Last Name");
	if($email == "")
		$missing_required = missing_required($missing_required, "Email");
	else
	{
		// see if email is a valid email(x@x.x)
	}
	if($confirm_email == "")
		$missing_required = missing_required($missing_required, "Confirm Email");
	else
	{
		// check confirm email
		if($email != $confirm_email)
			$errors = error($errors, "Your confirm email does not match the email given.");
	}
	
	//if no errors, insert user into DB
	if($errors == "" && $missing_required == "")
	{
		// To protect SQL injection (more detail about SQL injection)
		$username = stripslashes($username);
		$password = stripslashes($password);
		$username = mysql_real_escape_string($username);
		$password = mysql_real_escape_string($password);
		$salt = '.-=0+4,3!0=-^';
		$password = md5(md5($salt.$password).$salt);
		
		//create user
		$query = sprintf("INSERT INTO users (username, password, email, first_name, last_name) VALUES ('%s', '%s', '%s', '%s', '%s')", $username, $password, $email, $first_name, $last_name);
		$result = mysql_query($query);
		header("Location: /");
	}
}
else
{
	$username = "";
	$password = "";
	$confirm_password = "";
	$email = "";
	$confirm_email = "";
	$tagid = "";
}

?>

<html>
	<head>
		<title>Assassins of the Spoon - Register</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>
	
	<body>
		<center>
		<form action="login.php" method="POST">
			Username:<br>
			<input type="text" name="username" /><br>
			Password:<br>
			<input type="password" name="password" /><br>
			<input type="submit" value="Login" />
		</form>
		<br>
		<fieldset class="register_name">
			<b><font color="red">
			<?php echo($missing_required); ?><br><!-- missing required fields -->
			<?php echo($errors); ?><br><!-- errors -->
			</font></b>
			<br>
			<b><u>Register Your Username</u></b><br>
			<br>
			<form action="register.php" method="POST">
				<input type="hidden" name="register" value="true" />
				<table>
					<tr><td>Username:* </td><td><input type="text" name="username" value="<?php echo($username); ?>"/></td></tr>
					<tr><td>Password:* </td><td><input type="password" name="password" value="<?php echo($password); ?>"/></td></tr>
					<tr><td>Confirm Password:* </td><td><input type="password" name="confirm_password" value="<?php echo($confirm_password); ?>"/></td></tr>
					<tr><td>First Name:* </td><td><input type="text" name="first_name" value="<?php echo($first_name); ?>"/></td></tr>
					<tr><td>Last Name:* </td><td><input type="text" name="last_name" value="<?php echo($last_name); ?>"/></td></tr>
					<tr><td>Email:* </td><td><input type="text" name="email" value="<?php echo($email); ?>"/></td></tr>
					<tr><td>Confirm Email:* </td><td><input type="text" name="confirm_email" value="<?php echo($confirm_email); ?>"/></td></tr>
				</table><br>
				<input type="submit" value="Register" />
			</form>
		</fieldset>
	</body>
</html>
