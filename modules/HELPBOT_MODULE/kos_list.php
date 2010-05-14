<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Kill On Sight List
   ** Version: 0.6
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 31.01.2006
   ** Date(last modified): 21.11.2006
   ** 
   ** Copyright (C) 2006 Carsten Lohmann
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

if(eregi("^kos$", $message)) {
  	$db->query("SELECT * FROM koslist_<myname>");
  	if($db->numrows() == 0)
  		$msg = "No list exists yet.";
	else {
	  	while($row = $db->fObject())
		    $list[$row->name]++;
		
		arsort($list);
		$list = array_slice($list, 0, 25, true);
	  	$link  = "<header>::::: Kill On Sight list :::::<end>\n\n";
	  	$link .= "This list shows the top25 of added Players\n\n";
		$i = 0;
		foreach($list as $key => $value) {
		  	$i++;
		  	$link .= "$i. $key <highlight>(Voted {$value}times)<end>\n";
		}
			
	  	$msg = bot::makeLink("KOS-List", $link);
	}  	

  	if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
    	bot::send($msg);
    elseif($type == "guild")
    	bot::send($msg, "guild"); 	
} elseif(eregi("^kos add (.+) reason (.+)$", $message, $arr)) {
    $name = ucfirst(strtolower($arr[1]));
	$reason = str_replace("'", "''", $arr[2]);
  	$uid = AoChat::get_uid($name);
	if(strlen($reason) >= 50)
		$msg = "The reason can't be longer than 50 characters.";
	elseif($uid) {
		$db->query("INSERT INTO koslist_<myname> (`time`, `name`, `sender`, `reason`) VALUES (".time().", '$name', '$sender', '$reason')");
		$msg = "You have successfull added <highlight>$name<end> to the KOS List.";
	} else
		$msg = "The Player you want to add doesn't exists.";

    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
    	bot::send($msg);
    elseif($type == "guild")
    	bot::send($msg, "guild");
} elseif(eregi("^kos add (.+)$", $message, $arr)) {
    $name = ucfirst(strtolower($arr[1]));
  	$uid = AoChat::get_uid($name);
	if($uid) {
	  	$db->query("SELECT * FROM koslist_<myname> WHERE `sender` = '$sender' AND `name` = '$name'");
		if($db->numrows() == 1)
			$msg = "You have already <highlight>$name<end> on your KOS List.";	  	
		else {
			$db->query("INSERT INTO koslist_<myname> (`time`, `name`, `sender`) VALUES (".time().", '$name', '$sender')");
			$msg = "You have successfull added <highlight>$name<end> to the KOS List.";	  
		}
	} else
		$msg = "The Player you want to add doesn't exists.";

    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
    	bot::send($msg);
    elseif($type == "guild")
    	bot::send($msg, "guild");
} elseif(eregi("^kos rem (.+)$", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$db->query("SELECT * FROM koslist_<myname> WHERE `sender` = '$sender' AND `name` = '$name'");
	if($db->numrows() == 1) {
	  	$db->query("DELETE FROM koslist_<myname> WHERE `sender` = '$sender' AND `name` = '$name'");
		$msg = "You have successfull removed <highlight>$name<end> from the KOS List.";	  	
	} elseif($this->guildmembers[$sender] < $this->vars['guild admin level']) {
	  	$db->query("SELECT * FROM koslist_<myname> WHERE `name` = '$name'");
	  	if($db->numrows() != 0) {
		  	$db->query("DELETE FROM koslist_<myname> WHERE `name` = '$name'");	  	  
			$msg = "You have successfull removed <highlight>$name<end> from the KOS List.";	  			    
		} else
			$msg = "No one with this name is on the KOS List.";
	} else
		$msg = "You don't have this player on your KOS List.";

    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
    	bot::send($msg);
    elseif($type == "guild")
    	bot::send($msg, "guild");	
} elseif(eregi("^kos (.+)$", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$db->query("SELECT * FROM koslist_<myname> WHERE `name` = '$name' LIMIT 0, 40");
	if($db->numrows() >= 1) {
	  	$link  = "<header>::::: Kill On Sight list :::::<end>\n\n";
	  	$link .= "The following Players has added <highlight>$name<end> to his list\n\n";
		while($row = $db->fObject()) {
			$link .= "Name: <highlight>$row->sender<end>\n";
			$link .= "Date: <highlight>".gmdate("dS F Y, H:i", $row->time)."<end>\n";
			if($row->reason != "0")
				$link .= "Reason: <highlight>$row->reason<end>\n";
				
			$link .= "\n";
		}		
	  	$msg = bot::makeLink("KOS-List from $name", $link);
	} else
		$msg = "The player <highlight>$name<end> isn't on the KOS List.";

    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
    	bot::send($msg);
    elseif($type == "guild")
    	bot::send($msg, "guild");	
} else
	$syntax_error = true;
?>