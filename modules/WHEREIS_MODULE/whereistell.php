<?php

if (preg_match("/^whereistell (.+)$/i", $message, $arr))  {
	$message = "Whereis DB Update\n\n";
	$message .= $arr[1];
	$reply = "Request to Update Whereis DB sent";
	bot::send($message, Thorrest);
} else {
	$reply = "No data entered.  You must type your request after the <symbol>whereistell command";
}
	
bot::send($reply, $sendto);

?>