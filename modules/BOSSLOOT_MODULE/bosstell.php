<?php
if (ereg ("^bosstell (.+)$", $message, $arr))  {

	$message = "Bossloot DB Update\n\n";
	$message .= $arr[1];
	$reply = "Request to Update Bossloot DB sent";
	bot::send($message, Thorrest);
}
else {
	$reply = "No data entered.  You must type your request after the !bosstell command";
	}
	
if($type == "msg")
bot::send($reply, $sender);
elseif($type == "all")
bot::send($reply);
else
bot::send($reply, "guild");
?>