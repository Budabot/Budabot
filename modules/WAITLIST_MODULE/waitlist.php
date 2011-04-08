<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Listbot
   ** Version: 0.5
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 03.06.2006
   ** Date(last modified): 10.12.2006
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
   
global $listbot_waitlist;
if (preg_match("/^waitlist next$/i", $message)) {
	if (count($listbot_waitlist[$sender]) == 0) {
	  	$msg = "There is no one on your waitlist atm!";
	    $chatBot->send($msg, $sendto);
      	return;
	}
	
	$db->beginTransaction();
	//Resort the DB and the array
	forEach ($listbot_waitlist[$sender] as $key => $value) {
	 	if ($listbot_waitlist[$sender][$key]["position"] == 1) {
			$db->query("DELETE FROM waitlist_<myname> WHERE `owner` = '$sender' AND `name` = '{$listbot_waitlist[$sender][$key]["name"]}'");
			$name = $listbot_waitlist[$sender][$key]["name"];
			$chatBot->send("<highlight>$sender waitlist<end>: You can come now!", $name);
		   	unset($listbot_waitlist[$sender][$key]);
		   	break;
		}
	}
	
	forEach ($listbot_waitlist[$sender] as $key => $value) {
	   	$listbot_waitlist[$sender][$key]["position"] -= 1;
		$db->query("UPDATE waitlist_<myname> SET `position` = {$listbot_waitlist[$sender][$key]["position"]} WHERE `owner` = '$sender' AND `name` = '{$listbot_waitlist[$sender][$key]["name"]}'");
		$chatBot->send("Your Position on <highlight>$sender<end>'s waitlist has been changed to <highlight>{$listbot_waitlist[$sender][$key]["position"]}<end>", $listbot_waitlist[$sender][$key]["name"]);
	}
	$db->Commit();

	$msg = "<highlight>$name<end> has been called to come now.";
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^waitlist add (.+)$/i", $message, $arr)) {
  	$uid = AoChat::get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    if (!$uid) {
      	$msg = "Player <highlight>".$name."<end> does not exist.";
		$chatBot->send($msg, $sendto);
	    return;
    }

	if (count($listbot_waitlist[$sender]) > 10) {
	  	$msg = "You can't have more then 10 users on your waitlist!";
	  	$chatBot->send($msg, $sendto);
      	return;
	}
	
	 //Search trough the array if the player is on the list
	$found = false;
	forEach ($listbot_waitlist[$sender] as $key => $value) {
	 	if ($listbot_waitlist[$sender][$key]["name"] == $name) {
		   	$found = true;
		   	break;
		}
	}
	
	if ($found == true) {
	  	$msg = "<highlight>$name<end> is already on your waitlist!";
	  	$chatBot->send($msg, $sendto);
      	return;
	}
	
	$pos = count($listbot_waitlist[$sender]) + 1;
	$listbot_waitlist[$sender][] = array("name" => $name, "position" => $pos);
	$db->query("INSERT INTO waitlist_<myname> VALUES ('$sender', '$name', $pos, ".time().")");
	$msg = "<highlight>$name<end> has been added to your waitlist at Pos. <highlight>$pos<end>.";
	$chatBot->send($msg, $sendto);
	  	
	$chatBot->send("You have been added to the waitlist of <highlight>$sender<end> at Pos. <highlight>$pos<end>. You will be notified everytime you get one position up.", $name);
} else if (preg_match("/^waitlist rem all$/i", $message)) {
  	if (count($listbot_waitlist[$sender]) == 0) {
	  	$msg = "There is no one on your waitlist atm!";
	  	$chatBot->send($msg, $sendto);
      	return;
	}
	
	$db->query("DELETE FROM waitlist_<myname> WHERE `owner` = '$sender'");
	unset($listbot_waitlist[$sender]);
	
	$msg = "<highlight>$sender<end> your waitlist has been cleared.";
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^waitlist rem (.+)$/i", $message, $arr)) {
  	$uid = AoChat::get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    if (!$uid) {
      	$msg = "Player <highlight>".$name."<end> does not exist.";
   	    $chatBot->send($msg, $sendto);
      	return;
    }
    //Search trough the array if the player is on the list
	$found = false;
	forEach ($listbot_waitlist[$sender] as $key => $value) {
	 	if ($listbot_waitlist[$sender][$key]["name"] == $name) {
		   	$found = true;
		   	$position = $listbot_waitlist[$sender][$key]["position"];
		   	$db->query("DELETE FROM waitlist_<myname> WHERE owner = '$sender' AND name = '{$listbot_waitlist[$sender][$key]["name"]}'");
		   	unset($listbot_waitlist[$sender][$key]);
		   	break;
		}
	}
	
	if ($found == false) {
	  	$msg = "<highlight>$name<end> is not on your waitlist!";
	  	$chatBot->send($msg, $sendto);
      	return;
	}

	$db->beginTransaction();
	//Resort the DB and the array
	forEach ($listbot_waitlist[$sender] as $key => $value) {
	 	if ($listbot_waitlist[$sender][$key]["position"] > $position) {
		   	$listbot_waitlist[$sender][$key]["position"] -= 1;
			$db->query("UPDATE waitlist_<myname> SET position = {$listbot_waitlist[$sender][$key]["position"]} WHERE owner = '$sender' AND name = '{$listbot_waitlist[$sender][$key]["name"]}'");
			$chatBot->send("Your Position on <highlight>$sender<end>'s waitlist has been changed to <highlight>{$listbot_waitlist[$sender][$key]["position"]}<end>", $listbot_waitlist[$sender][$key]["name"]);
		}
	}
	$db->Commit();
	
	$msg = "<highlight>$name<end> has been removed from your waitlist.";
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^waitlist$/i", $message)) {
  	if (count($listbot_waitlist[$sender]) == 0) {
	 	$msg = "You don't have any waitlist created yet!";
	  	$chatBot->send($msg, $sendto);
      	return;
	}
	
	$msg = "Waitlist of $sender: ";
	$first = true;
	forEach($listbot_waitlist[$sender] as $key => $value) {
	  	if ($first){
		  	$msg .= "{$listbot_waitlist[$sender][$key]["position"]}: <highlight>{$listbot_waitlist[$sender][$key]["name"]}<end>";
		  	$first = false;
		} else {
		  	$msg .= ", {$listbot_waitlist[$sender][$key]["position"]}: <highlight>{$listbot_waitlist[$sender][$key]["name"]}<end>";		
		}
	}

  	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>