<?php
   /*
   Bossloot Module Ver 1.1
   Written By Jaqueme
   For Budabot
   Database Adapted From One Originally 
   Compiled by Malosar For BeBot
   Boss Drop Table Database Module
   Written 5/11/07
   Last Modified 5/14/07
   */
if (ereg ("^bosstell (.+)$", $message, $arr))  {

	$message = "Bossloot DB Update\n\n";
	$message .= $arr[1];
	$reply = "Request to Update Bossloot DB sent";
	bot::send($message, Thorrest);
}
else {
	$reply = "No data entered.  You must type your request after the <symbol>bosstell command";
}
	
if($type == "msg")
	bot::send($reply, $sender);
elseif($type == "all")
	bot::send($reply);
else
	bot::send($reply, "guild");
?>