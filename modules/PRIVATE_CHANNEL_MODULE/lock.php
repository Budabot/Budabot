<?php
  /*
   ** Author: Derroylo (RK2)
   ** Description: Locking the private channel
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 10.03.2006
   ** Date(last modified): 31.01.2007
   ** 
   ** Copyright (C) 2006, 2007 Carsten Lohmann
   **
   ** Licence Infos: 
   ** This file is part of Budabot.
   **
   ** Budabot is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** Budabot is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with Budabot; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   */
   
if (preg_match("/^lock$/i", $message)) {
  	if (Setting::get("priv_status") == "closed") {
	    $msg = "Private channel is already locked.";
		$chatBot->send($msg, $sendto);
		return;
	}
	$msg = "The private channel has been locked by <highlight>$sender<end>.";
	$chatBot->send($msg, 'priv');
	$msg = "You have locked the private channel.";
	if ($type == "msg") {
		$chatBot->send($msg, $sender);
	}
	
	Setting::save("priv_status", "closed");
} else if (preg_match("/^lock (.+)$/i", $message, $arr)) {
  	$reason = $arr[1];
	if (Setting::get("priv_status") == "closed") {
	    $msg = "Private channel is already locked.";
    	$chatBot->send($msg, $sendto);
		return;
	}
	$msg = "The private channel has been locked by <highlight>$sender<end> - Reason: <highlight>$reason<end>.";
	$chatBot->send($msg, 'priv');
	$msg = "You have locked the private channel.";
	if ($type == "msg") {
		$chatBot->send($msg, $sender);
	}
	
	Setting::save("priv_status", "closed");
	Setting::save("priv_status_reason", $reason);
} else if (preg_match("/^unlock$/i", $message)) {
  	if (Setting::get("priv_status") == "open") {
	    $msg = "Private channel is already opened.";
    	$chatBot->send($msg, $sendto);
		return;
	}
	$msg = "The private channel has been opened by <highlight>$sender<end>.";
	$chatBot->send($msg, 'priv');
	$msg = "You have opened the private channel.";
	if ($type == "msg") {
		$chatBot->send($msg, $sender);
	}
	
	Setting::save("priv_status", "open");
	Setting::save("priv_status_reason", "not set");
} else {
	$syntax_error = true;
}

?>