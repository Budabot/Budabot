<?php
if (ereg ("^whereistell (.+)$", $message, $arr))  {

	$message = "Whereis DB Update\n\n";
	$message .= $arr[1];
	$reply = "Request to Update Whereis DB sent";
	bot::send($message, Thorrest);
}
else {
	$reply = "No data entered.  You must type your request after the <symbol>whereistell command";
	}
	
if($type == "msg")
bot::send($reply, $sender);
elseif($type == "all")
bot::send($reply);
else
bot::send($reply, "guild");
?>