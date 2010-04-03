<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Adds an item to the roll
   ** Version: 0.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 01.03.2006
   ** Date(last modified): 30.01.2007
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

global $loot;
if(eregi("^(loot clear|clear)$", $message)) {
  	$loot = "";
  	$msg = "Loot has been cleared by <highlight>$sender<end>.";
  	bot::send($msg);	
} elseif(eregi("^loot (.+)$", $message, $arr)) {
 	if($this->vars["raid_status"] != "" && $this->vars["raid_pts"] == 0) {
		$msg = "<red>A flatrolled raid is already started and you can´t flatroll items while it is running!<end>";
		bot::send($msg);
		return;
	}
 
	//Check if the item is a link
  	if(eregi("^<a href=\"itemref:\/\/([0-9]+)\/([0-9]+)\/([0-9]+)\">(.+)<\/a>(.*)$", $arr[1], $item)) {
	    $item_ql = $item[3];
	    $item_highid = $item[1];
	    $item_lowid = $item[2];
	    $item_name = $item[4];
	    $item_comment2 = trim($item[5]);
	} elseif(eregi("^(.+)<a href=\"itemref:\/\/([0-9]+)\/([0-9]+)\/([0-9]+)\">(.+)<\/a>(.*)$", $arr[1], $item)) {
	    $item_ql = $item[4];
	    $item_highid = $item[2];
	    $item_lowid = $item[3];
	    $item_name = $item[5];
	    $item_comment1 = trim($item[1]);
	    $item_comment2 = trim($item[6]);
	} else
		$item_name = $arr[1];
		
	//get a slot for the item
  	if(is_array($loot)) {
	  	$num_loot = count($loot);
	  	$num_loot++;
	} else
		$num_loot = 1;
	
	//Check if max slots is reached
  	if($num_loot >= 30) {
	    $msg = "You can only roll 30items max at one time!";
	    bot::send($msg);
	    return;
	}

	//Check if there is a icon available
	$item_name = str_replace("'", "\'", $item_name);
	$item_name = str_replace(":", "&#58;", $item_name);
	$item_name = str_replace("&", "&amp;", $item_name);
	$db->query("SELECT * FROM aodb WHERE `name` LIKE \"$item_name\"");
	if($db->numrows() != 0) {
		//Create an Object of the data
	  	$row = $db->fObject();
	  	$item_name = $row->name;
		//Return HTML to normal characters		
  		$item_name = str_replace("\'", "'", $item_name);
		$item_name = str_replace("&#58;", ":", $item_name);
		$item_name = str_replace("&amp;", "&", $item_name);
		//Save the icon
		$loot[$num_loot]["icon"] = $row->icon;
		//Save the aoid and ql if not set yet
		if(!isset($item_highid)) {
			$item_lowid = $row->lowid;
			$item_highid = $row->highid;
			$item_ql = $row->highql;	  
		}
	}

	//Save item
	if(isset($item_highid)) {
		$loot[$num_loot]["name"] = "<a href='itemref://$item_lowid/$item_highid/$item_ql'>$item_name</a>";
		if($item_comment1 != "")
			$loot[$num_loot]["name"] = $item_comment1." ".$loot[$num_loot]["name"];
			
		if($item_comment2 != "")
			$loot[$num_loot]["name"] .= " ".$item_comment2;
	} else
		$loot[$num_loot]["name"] = $item_name;

	//Save the person who has added the loot item
	$loot[$num_loot]["added_by"] = $sender;
	
	//Send info
	bot::send("<highlight>{$loot[$num_loot]["name"]}<end> will be rolled in Slot <highlight>#$num_loot<end>");
	bot::send("To add use <symbol>add $num_loot, or <symbol>add 0 to remove yourself");
} else
	$syntax_error = true;
?>