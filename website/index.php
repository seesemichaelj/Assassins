<?php
include_once("config.php");
?>

<html>
	<head>
		<title>Assassins of the Spoon</title>
	</head>
	
	<body>
		<?php
			$logged_in = '
											<a href="register.php">Register</a><br>
											<form action="login.php" method="POST">
												<input type="text" name="username" />
												<input type="password" name="password" />
												<input type="hidden" name="login" value="true" />
												<input type="submit" value="Login" />
											</form>
									 ';
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
						//logged in
						$userid = $row['userid'];
						$query = sprintf("SELECT * FROM users WHERE userid='%s'",$userid);
						$result = mysql_query($query);
						$row = mysql_fetch_assoc($result);
						$secret = $row['secret'];
						$logged_in = '
												 		Hello, ' . $row['first_name'] . ' ' . $row['last_name'] . '.<br>
												 		<a href="/">Home</a><br>
												 		<a href="/login.php?logout=true">Logout</a><br>
												 		<a href="/settings.php">Settings</a><br>
												 		<a href="/create.php">Create Tournament</a><br>
												 		<a href="/join.php">Join Tournament</a><br>
												 ';
						if($row['gameid'] != NULL)
						{
							//if in a game
							$gameid = $row['gameid'];
							$targetid = $row['targetid'];
							if($targetid >= 0)
							{
								//you have an assignment, get target name
								$query = sprintf("SELECT * FROM users WHERE userid='%s'", $targetid);
								$result = mysql_query($query);
								$row = mysql_fetch_assoc($result);
								$target_name = $row['first_name'] . ' ' . $row['last_name'];
							}
							$query = sprintf("SELECT * FROM games WHERE gameid='%s'",$gameid);
							$result = mysql_query($query);
							$row = mysql_fetch_assoc($result);
							if($row['adminid'] == $userid)
							{
								//you're an admin
								$logged_in = $logged_in . '
																						<a href="/admin.php">Admin</a><br>
																					';
							}
							$logged_in = $logged_in . '
																					<br>
																					You are currently in the tournament, ' . $row['name'] . '. <a href="join.php?leave=true">Leave tournament</a>.<br>
																				';
							if($row['state'] == 0)
							{
								//game not started
								if($row['adminid'] == $userid)
								{
									$logged_in = $logged_in . '
																							The tournament hasn\'t started yet. You can change this at the <a href="/admin.php">admin page</a>.<br>
																						';
								}
								else
								{
									$logged_in = $logged_in . '
																							The tournament hasn\'t started yet. You\'ll receive an email and/or text with your text when the it starts.<br>
																						';
								}
							}
							else if($row['state'] == 1)
							{
								//game started, show target
								if($targetid == -2)
								{
									$logged_in = $logged_in . '
																							You currently do not have an assignment. Please wait for the overlord to find you a suitable contract.<br>
																						';
								}
								else if($targetid == -1)
								{
									$logged_in = $logged_in . '
																							You are currently dead. You\'ll have to wait until the next session to play in this tournament.<br>
																						';
								}
								else if($targetid == -3)
								{
									$logged_in = $logged_in . '
																							You are the last assassin standing; congratulations.<br>
																						';
								}
								else
								{
									$logged_in = $logged_in . '
																							Your target is ' . $target_name . '. Your secret (for when you die) is ' . $secret . '. Good luck assassin.<br>
													 										<a href="/report.php">Report Kill</a>
																						';
								}
							}
							else if($row['state'] == 2)
							{
								//registration is closed
								if($row['adminid'] == $userid)
								{
									$logged_in = $logged_in . '
																							The tournament\'s registration is currently disabled. Enable or start the tournament at the <a href="/admin.php">admin page</a>.<br>
																						';
								}
								else
								{
									$logged_in = $logged_in . '
																							The tournament isn\'t running currently disabled. If you leave the tournament, you will not be able to join back until registration is opened again.<br>
																							You\'ll receive an email and/or text with your text when the it starts.<br>
																						';
								}
							}
						}
					}
				}
			}
			else
			{
				header("Location: /");
			}
			echo($logged_in);
		?>
		
		<br>
		<br>
		To use the SMS feature, go to Settings, enter in your 10-digit phone number, and text the word "verify" to assassins@assassins.myprotosite.com.<br>
		From here you can use the SMS commands:
		<ul>
			<li>"report x" where x is the 6 character alphanumeric secret of your target (when you kill them)</li>
			<li>"status" to get a text of who your target is and your secret</li>
		<ul>
		
	</body>
</html>
