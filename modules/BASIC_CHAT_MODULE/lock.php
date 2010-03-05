<?
  /*
   ** Author: Derroylo (RK2)
   ** Description: Locking the privategroup
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
   
if(eregi("^lock$", $message)) {
  	if($this->settings["priv_status"] == "closed") {
	    $msg = "Privategroup is already locked.";
	    if($type == "msg")
	    	bot::send($msg, $sender);
	    elseif($type == "priv")
	    	bot::send($msg);
		return;
	}
	$msg = "The privategroup has been locked by <highlight>$sender<end>.";
	bot::send($msg);
	$msg = "You have locked the privategroup.";
	if($type == "msg")
		bot::send($msg, $sender);
	
	bot::savesetting("priv_status", "closed");
} elseif(eregi("^lock (.+)$", $message, $arr)) {
  	$reason = $arr[1];
	if($this->settings["priv_status"] == "closed") {
	    $msg = "Privategroup is already locked.";
	    if($type == "msg")
	    	bot::send($msg, $sender);
	    elseif($type == "priv")
	    	bot::send($msg);
		return;
	}
	$msg = "The privategroup has been locked with the reason <highlight>$reason<end> by <highlight>$sender<end>.";
	bot::send($msg);
	$msg = "You have locked the privategroup.";
	if($type == "msg")
		bot::send($msg, $sender);
	
	bot::savesetting("priv_status", "closed");
	bot::savesetting("priv_status_reason", $reason);
} elseif(eregi("^unlock$", $message)) {
  	if($this->settings["priv_status"] == "open") {
	    $msg = "Privategroup is already opened.";
	    if($type == "msg")
	    	bot::send($msg, $sender);
	    elseif($type == "priv")
	    	bot::send($msg);
		return;
	}
	$msg = "The privategroup has been opened by <highlight>$sender<end>.";
	bot::send($msg);
	$msg = "You have opened the privategroup.";
	if($type == "msg")
		bot::send($msg, $sender);
	
	bot::savesetting("priv_status", "open");
	bot::savesetting("priv_status_reason", "not set");	
} else
	$syntax_error = true;
?>