<?php
include_once("config.php");

function generateMobileEmail($number, $carrier)
{
	switch($carrier)
	{
		case 0: // Aircel
			$email = $number . "@aircel.co.in";
			break;
		case 1: // Airtel Andhra Pradesh
			$email = $number . "@airtelap.com";
			break;
		case 2: // Airtel Karnataka
			$email = $number . "@airtelkk.com";
			break;
		case 3: // Alaska Communications
			$email = $number . "@msg.acsalaska.com";
			break;
		case 4: // Aliant
			$email = $number . "@sms.wirefree.informe.ca";
			break;
		case 5: // Alltel
			$email = $number . "@sms.alltelwireless.com";
			break;
		case 6: // Ameritech
			$email = $number . "@paging.acswireless.com";
			break;
		case 7: // Andhra Pra
			$email = $number . "@something.some";
			break;
	}
}

function notifyDelete($gameid)
{
	$headers = 'From: Assassins Overlord <assassins@assassins.myprotosite.com>';
	$subject = "Tournament Deletion";
	$message = "For one reason or another, the admin has deleted the tournament. You are no longer associated with this tournament and will not receive further notifications about it (as it doesn't exist).";
	
	$query = sprintf("SELECT * FROM users WHERE gameid='%s'", $gameid);
	$result = mysql_query($query);
	
	for($i = 0; $i < mysql_num_rows($result); $i++)
	{
		//notify each user
		$row = mysql_fetch_assoc($result);
		$email = $row['email'];
		$mobile = $row['mobile'];
		if(strlen($mobile) > 10)
		{
			$to = $to . ", " . $mobile; //add phone to notification email if phone is verified
		}
		mail($email, $subject, $message, $headers, "-fassassins@assassins.myprotosite.com");
	}
}

function notifyNewContract($userid)
{
	$headers = 'From: Assassins Overlord <assassins@assassins.myprotosite.com>';
	//get email of user id
	$query = sprintf("SELECT * FROM users WHERE userid='%s'",$userid);
	$result = mysql_query($query);
	$row = mysql_fetch_assoc($result);
	$to = $row['email'];
	$mobile = $row['mobile'];
	$secret = $row['secret'];
	if(strlen($mobile) > 10)
	{
		$to = $to . ", " . $mobile; //add phone to notification email if phone is verified
	}
	
	//get name of target id
	$targetid = $row['targetid'];
	if($targetid >= 0)
	{
		//you have a target
		$query = sprintf("SELECT * FROM users WHERE userid='%s'",$targetid);
		$result = mysql_query($query);
		$row = mysql_fetch_assoc($result);
		$targetname = $row['first_name'] . ' ' . $row['last_name'];
		
		$message = "You have been assigned a new contract. Your target's name is $targetname. Your secret: " .  $secret . ".";
		$subject = "Your Next Contract";
	}
	else if($targetid == -1)
	{
		//you're dead
		$message = "Another assassin has reported your death. Good luck next time.";
		$subject = "Reported Death";
	}
	else if($targetid == -2)
	{
		//you dont have a target
		$message = "The overlord hasn't found a suitable contract for you yet. Please wait while an assignment is made.";
		$subject = "Your Next Contract";
	}
	else if($targetid == -3)
	{
		//you won the tournament
		$message = "Congratulations assassin, you have won the tournament. Everyone but you is dead.";
		$subject = "Congratulations Assassin ";
	}
	
	//send email notifying of target
	mail($to, $subject, $message, $headers, "-fassassins@assassins.myprotosite.com");
}

?>