<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Configure Raids
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 10.10.2006
   ** Date(last modified): 03.02.2007
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
   
if(eregi("^raidconfig$", $message)) {
	$list = "<header>::::: Configuration of the Raidmodule :::::<end>\n\n";
	$list .= "The Raidmodule allows you to create your own Raids. Allong with this Bot you are getting an Loottable for various raids like Pandemonium, Mercs or Alien Playfield. However you will need to configure it to match your own wishes. At the first time you should upload the current Loottables. Once you have done that you can look at them and set minlvl, raidmode(if it is flatrolled or pointsbased) and bidcosts. Once you done it you need to enable the Raid so that it is available to use the raidstart command.\n\n";
	$list .= "*<u>Raidconfig Commands</u>*\n";
	$list .= "Creating a new raid: <highlight>/tell <myname> raidconfig addraid 'raidname'<end>\n";
	$list .= "Set the shortform(That one are you using with raidstart) of a raid: <highlight>/tell <myname> raidconfig addshortform 'shortform' 'raidname'<end>\n";
	$list .= "Note: The following characters are only allowed in the shortform 0-9, a-z and A-Z. <red>No Spaces!<end>\n";
	$list .= "Adding an item to a loottable of a raid: <highlight>/tell <myname> raidconfig additem 'itemname'<end>\n";	
	$list .= "Set starter Points for new Players(".bot::makeLink("Change this", "/tell <myname> raidconfig starterpts", "chatcmd")."): <highlight>{$this->settings["starter_pts"]}<end>\n";
	$list .= bot::makeLink("Import/Export the Raidconfig", "/tell <myname> raidconfig inport_exportdb", "chatcmd")." Use this to import or export the Raidconfiguration\n\n";
	$list .= "*<u>Registered Raid Configurations</u>*\n";
	$db->query("SELECT * FROM raids_settings_<myname>");
	if($db->numrows() == 0) {
		$list .= "<red>No Raids registered yet<end>\n";
	} else {
		while($row = $db->fObject()) {
			$list .= "Name(Shortform): <highlight>$row->raid_name<end>(";
			if($row->shortform != "Not set")
				$list .= "<highlight>$row->shortform<end>)\n";
			else
				$list .= "<red>$row->shortform<end>)\n";
				
			if($row->status == 1 && $row->shortform != "Not set") {
				$list .= "Status: <green>Enabled<end>";
				$list .= " (".bot::makeLink("Disable", "/tell <myname> raidconfig disable $row->shortform", "chatcmd").")\n";
			} elseif($row->shortform != "Not set") {
				$list .= "Status: <red>Disabled<end>";
				$list .= " (".bot::makeLink("Enable", "/tell <myname> raidconfig enable $row->shortform", "chatcmd").")\n";
			}
			
			if($row->pts == 0 && $row->shortform != "Not set" && $row->flat_multiroll == 0)
				$list .= "Roll mode: <highlight>Flatroll(all seperate)<end>\n";
			elseif($row->pts == 0 && $row->shortform != "Not set" && $row->flat_multiroll == 1)
				$list .= "Roll mode: <highlight>Flatroll(all together)<end>\n";
			elseif($row->shortform != "Not set")
				$list .= "Roll mode: <highlight>Points<end>(<highlight>$row->pts<end>) and Bid cost is <highlight>$row->bid_cost<end>pt(s)\n";

			if($row->spawntime != 0 && $row->shortform != "Not set") {
				$list .= "Spawntime: <highlight>";
				$hrs = floor($row->spawntime/3600);
				$mins = ($row->spawntime - ($hrs * 3600)) / 60;
				$list .= "{$hrs}hour(s) {$mins}minutes(s)<end>\n";
			}
			
			$list .= "Options: ";
			if($row->shortform != "Not set") {
				$list .= bot::makeLink("Configure Roll Mode", "/tell <myname> raidconfig rollmode $row->shortform", "chatcmd")." ";
				$list .= bot::makeLink("Set spawntime", "/tell <myname> raidconfig spawntime $row->shortform", "chatcmd")." ";
				$list .= bot::makeLink("Show Loottable", "/tell <myname> raidconfig showloot $row->shortform", "chatcmd")." ";
				$list .= bot::makeLink("Delete this raid(including loottable)", "/tell <myname> raidconfig remraid $row->shortform", "chatcmd");
			} else
				$list .= "<red>None available!<end> Add a shortform of the raid first.";
			
			$list .= "\n\n";
		}
	}
	
	$msg = bot::makeLink("Raid Configuration", $list);
	bot::send($msg, $sender);
} elseif(eregi("^raidconfig showloot (.+)$", $message, $arr)) {
	$raidname = strtolower($arr[1]);
	
	$db->query("SELECT * FROM raids_items_<myname> WHERE `shortform` = '$raidname'");
	if($db->numrows() == 0) {
		$msg = "<red>No Loottable entered for this raid.<end>";
		bot::send($msg, $sender);
		return;
	}
	
	$list = "<header>::::: Loottable for the Raid $raidname :::::<end>\n\n";
	$list .= "Changing the Category of an Item with: <highlight>/tell <myname> raidconfig cat 'AOID' 'Category'<end>\n";
	while($row = $db->fObject()) {
	  	if($row->item_icon != 0)
	  		$list .= "<img src=rdb://{$row->item_icon}>\n";
	
		$list .= "Itemname(AOID): <highlight>{$row->item_name}<end>(<highlight>$row->item_aoid<end>)\n";
		$list .= "Category: <highlight>{$row->cat}<end>\n";
		$list .= "Amount: <highlight>{$row->item_amount}<end>";
		$list .= " (".bot::makeLink("Change this", "/tell <myname> raidconfig amount $raidname $row->item_aoid", "chatcmd").")\n";
		if($row->item_amount > 1 && $row->item_multiloot == 1) {
			$list .= "Multiloot: <green>Enabled<end>";
			$list .= " (".bot::makeLink("Disable", "/tell <myname> raidconfig multiloot 0 $raidname $row->item_aoid", "chatcmd").")\n";
		} elseif($row->item_amount > 1) {
			$list .= "Multiloot: <red>Disabled<end>";
			$list .= " (".bot::makeLink("Enable", "/tell <myname> raidconfig multiloot 1 $raidname $row->item_aoid", "chatcmd").")\n";			
		}
		
		if($row->item_minlvl != 0)
			$list .= "MinLvl: <highlight>{$row->item_minlvl}<end>";
		else
			$list .= "MinLvl: Not set.";

		$list .= " (".bot::makeLink("Change this", "/tell <myname> raidconfig minlvl $raidname $row->item_aoid", "chatcmd").")\n";
		$list .= "Options: ".bot::makeLink("Delete this item", "/tell <myname> raidconfig delitem $row->item_aoid $raidname", "chatcmd");
		$list .= "\n\n";
	}
	$msg = bot::makeLink("Loottable for raid $raidname", $list);
	bot::send($msg, $sender);
} elseif(eregi("^raidconfig minlvl ([a-z0-9]+) ([0-9]+)$", $message, $arr)) {
	$raidname = strtolower($arr[1]);
	$itemid = $arr[2];
	
	$db->query("SELECT * FROM raids_settings_<myname> WHERE `shortform` = '$raidname'");
	if($db->numrows() == 0) {
		$msg = "<red>No Raid registered with the name $raidname<end>.";
		bot::send($msg, $sender);
		return;
	}
	
	$db->query("SELECT * FROM raids_items_<myname> WHERE `shortform` = '$raidname' AND `item_aoid` = $itemid");
	if($db->numrows() == 0) {
		$msg = "<red>No Item found on the raidtable for <highlight>$raidname<end> with the id <highlight>$itemid<end>.";
		bot::send($msg, $sender);
		return;
	}
	
	$row = $db->fObject();
	$list = "<header>::::: Minimum Level configuration for $row->item_name :::::<end>\n\n";
	$list .= "Current Setting: ";
	if($row->item_minlvl == "0")
		$list .= "<highlight>No Level restrictions currently.<end>\n\n";
	else
		$list .= "<highlight>Level Restriction is set to $row->item_minlvl<end>\n\n";
	
	$list .= "<u>Change the minlvl to</u>: ";
	if($row->item_minlvl == "0") {
		$list .= bot::makeLink("No lvl restrictions", "/tell <myname> raidconfig minlvl 0 $itemid $raidname", "chatcmd");	
	} else {
		$list .= "\n<highlight>Set level Restriction<end>\n";
		for($i = 0; $i <= 220; $i+=5) {
		 	if($i != 0)
				$list .= bot::makeLink("Set $i as minlvl", "/tell <myname> raidconfig minlvl $i $itemid $raidname", "chatcmd")."\n";
			else
				$list .= bot::makeLink("Remove the minlvl requirement", "/tell <myname> raidconfig minlvl $i $itemid $raidname", "chatcmd")."\n";
		}
	}
	
	$msg = bot::makeLink("Set Minlvl Restrictions for Item $row->item_name in Raid $raidname", $list);	
	bot::send($msg, $sender); 	
} elseif(eregi("^raidconfig minlvl ([0-9]+) ([0-9]+) ([a-z0-9]+)$", $message, $arr)) {
 	$minlvl = $arr[1];
 	$itemid = $arr[2];
 	$raidname = strtolower($arr[3]);
 	
 	$db->query("SELECT * FROM raids_settings_<myname> WHERE `shortform` = '$raidname'");
	if($db->numrows() == 0) {
		$msg = "<red>No Raid registered with the name $raidname<end>.";
		bot::send($msg, $sender);
		return;
	}
	
	$db->query("SELECT * FROM raids_items_<myname> WHERE `shortform` = '$raidname' AND `item_aoid` = $itemid");
	if($db->numrows() == 0) {
		$msg = "<red>No Item found on the raidtable for <highlight>$raidname<end> with the id <highlight>$itemid<end>.";
		bot::send($msg, $sender);
		return;
	}
	
	if($minlvl > 220) {
		bot::send("<red>You can´t set minlvl higher than $minlvl!<end>", $sender);
		return;
	}
	
	$row = $db->fObject();
	$db->query("UPDATE raids_items_<myname> SET `item_minlvl` = $minlvl WHERE `item_aoid` = $itemid AND `shortform` = '$raidname'");
	$msg = "Minimum level set to <highlight>$minlvl<end> for item <highlight>$row->item_name<end> in the raid <highlight>$raidname<end>.";
	bot::send($msg, $sender);
} elseif(eregi("^raidconfig enable (.+)$", $message, $arr)) {
	$raidname = strtolower($arr[1]);
	
	$db->query("SELECT * FROM raids_settings_<myname> WHERE `shortform` = '$raidname'");
	if($db->numrows() == 0) {
		$msg = "<red>No Raid registered with the name $raidname<end>.";
		bot::send($msg, $sender);
		return;
	}
	$row = $db->fObject();
	if($row->status == 1) {
		$msg = "This raid is already enabled";
	} else {
		$msg = "The raid <highlight>$raidname<end> is now <green>enabled<end>.";
		$db->query("UPDATE raids_settings_<myname> SET `status` = 1 WHERE `shortform` = '$raidname'");
	}
	bot::send($msg, $sender);
} elseif(eregi("^raidconfig disable (.+)$", $message, $arr)) {
	$raidname = strtolower($arr[1]);
	
	$db->query("SELECT * FROM raids_settings_<myname> WHERE `shortform` = '$raidname'");
	if($db->numrows() == 0) {
		$msg = "<red>No Raid registered with the name $raidname<end>.";
		bot::send($msg, $sender);
		return;
	}
	$row = $db->fObject();
	if($row->status == 0) {
		$msg = "This raid is already disabled";
	} else {
		$msg = "The raid <highlight>$shortform<end> is now <red>disabled<end>.";
		$db->query("UPDATE raids_settings_<myname> SET `status` = 0 WHERE `shortform` = '$raidname'");
	}
	bot::send($msg, $sender);
} elseif(eregi("^raidconfig rollmode flat (multiroll|singleroll) (.+)$", $message, $arr)) {
	$raidname = strtolower($arr[2]);
	$rollmode = strtolower($arr[1]);
	
	$db->query("SELECT * FROM raids_settings_<myname> WHERE `shortform` = '$raidname'");
	if($db->numrows() == 0) {
		$msg = "<red>No Raid registered with the name $raidname<end>.";
		bot::send($msg, $sender);
		return;
	}
	$row = $db->fObject();
	if($row->pts == 0 && $row->flat_multiroll == 0 && $rollmode == "singleroll") {
		$msg = "This raid is already flatrolled(All items seperate)";
	} elseif($row->pts == 0 && $row->flat_multiroll == 1 && $rollmode == "multiroll") {
		$msg = "This raid is already flatrolled(All items at once)";
	} elseif($rollmode == "singleroll") {
		$msg = "The raid <highlight>$raidname<end> is now <highlight>Flattrolled<end>(all Items seperate).";
		$db->query("UPDATE raids_settings_<myname> SET `pts` = 0, `flat_multiroll` = 0 WHERE `shortform` = '$raidname'");		
	} elseif($rollmode == "multiroll") {
		$msg = "The raid <highlight>$raidname<end> is now <highlight>Flattrolled<end>(all Items at once).";
		$db->query("UPDATE raids_settings_<myname> SET `pts` = 0, `flat_multiroll` = 1 WHERE `shortform` = '$raidname'");		
	}
	bot::send($msg, $sender);	
} elseif(eregi("^raidconfig rollmode pts ([0-9]+) (.+)$", $message, $arr)) {
	$raidname = strtolower($arr[2]);
	$pts = $arr[1];
	
	$db->query("SELECT * FROM raids_settings_<myname> WHERE `shortform` = '$raidname'");
	if($db->numrows() == 0) {
		$msg = "<red>No Raid registered with the name $raidname<end>.";
		bot::send($msg, $sender);
		return;
	}
	
	if($pts > 25) {
		$msg = "<red>You can set as max only 25pts for the raid reward.<end>";
		bot::send($msg, $sender);
	}
	
	$row = $db->fObject();
	$msg = "All items of the Raid <highlight>$row->raid_name<end> are now rolled with the points system. The reward for this raid is set to <highlight>$pts<end>pts.";
	$db->query("UPDATE raids_settings_<myname> SET `pts` = $pts WHERE `shortform` = '$raidname'");
	bot::send($msg, $sender);	
} elseif(eregi("^raidconfig rollmode (.+)$", $message, $arr)) {
	$raidname = strtolower($arr[1]);
	
	$db->query("SELECT * FROM raids_settings_<myname> WHERE `shortform` = '$raidname'");
	if($db->numrows() == 0) {
		$msg = "<red>No Raid registered with the name $raidname<end>.";
		bot::send($msg, $sender);
		return;
	}
	
	$row = $db->fObject();
	$list = "<header>::::: Roll configuration for $raidname :::::<end>\n\n";
	$list .= "Current Setting: ";
	if($row->pts == 0 && $row->flat_multiroll == 1)
		$list .= "<highlight>All items are flatrolled(all items of this raid at once)<end>\n\n";
	elseif($row->pts == 0 && $row->flat_multiroll == 0)
		$list .= "<highlight>All items are flatrolled(all items seperate)<end>\n\n";	
	else {
		$list .= "<highlight>All items are rolled with the points system. The reward for one raid is currently $row->pts<end>\n\n";
	}
	
	
	$list .= "<u>Change the rollmode to</u>: \n";
	if($row->pts != 0) {
		$list .= bot::makeLink("Flatrolled(all items of this raid at once)", "/tell <myname> raidconfig rollmode flat multiroll $row->shortform", "chatcmd")."\n";
		$list .= bot::makeLink("Flatrolled(all items seperate)", "/tell <myname> raidconfig rollmode flat singleroll $row->shortform", "chatcmd");		
	} else {
	 	$list .= "\n<highlight>Flat rolled<end>\n";
	 	if($row->flat_multiroll == 1)
			$list .= bot::makeLink("Flatrolled(all items seperate)", "/tell <myname> raidconfig rollmode flat singleroll $row->shortform", "chatcmd")."\n";		
	 	else
			$list .= bot::makeLink("Flatrolled(all items of this raid at once)", "/tell <myname> raidconfig rollmode flat multiroll $row->shortform", "chatcmd")."\n";

		$list .= "\n<highlight>Points Rewarded<end>\n";
		for($i = 1; $i <= 15; $i++)
			$list .= bot::makeLink($i."pts as reward", "/tell <myname> raidconfig rollmode pts $i $row->shortform", "chatcmd")."\n";
		
		$list .= "\n<highlight>Bid costs<end>\n";
		for($i = 1; $i <= 5; $i++)
			$list .= bot::makeLink($i."pts as bid cost", "/tell <myname> raidconfig bidcost $i $row->shortform", "chatcmd")."\n";
	}
	
	$msg = bot::makeLink("Roll Configuration for $row->raid_name", $list);	
	bot::send($msg, $sender);
} elseif(eregi("^raidconfig bidcost ([0-9]+) (.+)$", $message, $arr)) {
	$raidname = strtolower($arr[2]);
	$pts = $arr[1];
	
	$db->query("SELECT * FROM raids_settings_<myname> WHERE `shortform` = '$raidname'");
	if($db->numrows() == 0) {
		$msg = "<red>No Raid registered with the name $raidname<end>.";
		bot::send($msg, $sender);
		return;
	}
	
	if($pts > 25) {
		$msg = "You can set as max only 5pts as bid cost.";
		bot::send($msg, $sender);
	}
	
	$row = $db->fObject();
	$msg = "Bidding on an Item at the raid <highlight>$row->raid_name<end> will cost <highlight>$pts<end>.";
	$db->query("UPDATE raids_settings_<myname> SET `bid_cost` = $pts WHERE `shortform` = '$raidname'");
	bot::send($msg, $sender);
} elseif(eregi("^raidconfig addraid (.+)$", $message, $arr)) {
	$raidname = $arr[1];
	
	$db->query("SELECT * FROM raids_settings_<myname> WHERE `raid_name` = '$raidname'");
	if($db->numrows() != 0) {
		$msg = "<red>A raid with that name already exists!<end>";
		bot::send($msg, $sender);
		return;
	}
	
	if(strlen($raidname) > 50) {
		$msg = "<red>The name of the raid can´t be longer than 50characters.<end>";
		bot::send($msg, $sender);
		return;
	}
	
	$msg = "A new raid with the name <highlight>$raidname<end> has been created.";
	bot::send($msg, $sender);
	$db->query("INSERT INTO raids_settings_<myname> VALUES ('$raidname', 0, 0, 'Not set', 0, 0, 0, 1)");
} elseif(eregi("^raidconfig remraid (.+)$", $message, $arr)) {
	$raidname = $arr[1];
	
	$db->query("SELECT * FROM raids_settings_<myname> WHERE `shortform` = '$raidname'");
	if($db->numrows() == 0) {
		$msg = "<red>A raid with that name doesn´t exists!<end>";
		bot::send($msg, $sender);
		return;
	}
	
	$row = $db->fObject();
	$msg = "Deleted the raid $row->raid_name<end> and his loottables.";
	bot::send($msg, $sender);
	$db->query("DELETE FROM raids_settings_<myname> WHERE `shortform` = '$raidname'");
	$db->query("DELETE FROM raids_items_<myname> WHERE `shortform` = '$raidname'");	
} elseif(eregi("^raidconfig addshortform ([a-z0-9]+) (.+)$", $message, $arr)) {
	$shortform = strtolower($arr[1]);
	$raidname = $arr[2];
	
	$db->query("SELECT * FROM raids_settings_<myname> WHERE `raid_name` = '$raidname'");
	if($db->numrows() == 0) {
		$msg = "<red>A raid with that name doesn´t exists!<end>";
		bot::send($msg, $sender);
		return;
	}
	
	if(strlen($shortform) > 20) {
		$msg = "<red>The shortform can´t be longer than 20chars<end>";
		bot::send($msg, $sender);
		return;
	}
	
	$msg = "Shortform for the raid <highlight>$raidname<end> has been set to <highlight>$shortform<end>.";
	bot::send($msg, $sender);
	$db->query("UPDATE raids_settings_<myname> SET `shortform` = '$shortform' WHERE `raid_name` = '$raidname'");
} elseif(eregi("^raidconfig starterpts$", $message, $arr)) {
	$raidname = strtolower($arr[1]);
	
	$list = "<header>::::: Starter Points configuration :::::<end>\n\n";
	$list .= "Current Setting: <highlight>{$this->settings["starter_pts"]}<end>\n\n";
	
	$list .= "<u>Change it to<end>\n";
	for($i = 0; $i <= 20; $i++)
		$list .= bot::makeLink("Starter Points = $i", "/tell <myname> raidconfig starterpts $i", "chatcmd")."\n";
	
	$msg = bot::makeLink("Starter Points Configuration", $list);	
	bot::send($msg, $sender);
} elseif(eregi("^raidconfig additem ([0-9]+) ([a-z0-9]+)$", $message, $arr)) {
 	$itemid = $arr[1];
 	$shortform = strtolower($arr[2]);
 	
	$db->query("SELECT * FROM aodb WHERE `highid` = $itemid");
	if($db->numrows() == 0) {
	    $msg = "No items found with the HighID <highlight>$itemid<end>.";
		bot::send($msg, $sender);
		return;
	}
 	$item = $db->fObject();
	$db->query("SELECT * FROM raids_settings_<myname> WHERE `shortform` = '$shortform'");
	if($db->numrows() == 0) {
		$msg = "<red>A raid with that name doesn´t exists!<end>";
		bot::send($msg, $sender);
		return;
	}
	
	$raid = $db->fObject();
	
	$db->query("SELECT * FROM raids_items_<myname> WHERE `shortform` = '$shortform' AND `item_aoid` = $itemid");
	if($db->numrows() != 0) {
		$msg = "<red>The item $item->name is already in the loottable of this raid!<end>";
		bot::send($msg, $sender);
		return;
	}
	
	$msg = "The Item <highlight>$item->name<end> has been assigned to the raid <highlight>$raid->raid_name<end>.";
	bot::send($msg, $sender);
	$db->query("INSERT INTO raids_items_<myname> VALUES ('$shortform', 'general', \"$item->name\", $item->highid, $item->highql, $item->icon, 1, 0, 'flat', 0)");
} elseif(eregi("^raidconfig additem ([0-9]+)$", $message, $arr)) {
 	$itemid = $arr[1];
 	
	$db->query("SELECT * FROM aodb WHERE `highid` = $itemid");
	if($db->numrows() == 0) {
	    $msg = "No items found with the HighID <highlight>$itemid<end>.";
		bot::send($msg, $sender);
		return;
	}
 	
 	$item = $db->fObject();	
 	$list = "<header>::::: Assign the item to a Loottable of a raid :::::<end>\n\n";
	$list .= "Item that should be registered: <highlight>$row->name<end>\n\n";
 	$list .= "<u>Registered Raids</u>\n";
	$db->query("SELECT * FROM raids_settings_<myname> WHERE `shortform` != 'Not set'");
	if($db->numrows() == 0) {
		$list .= "<red>No Raids registered yet<end>\n";
	} else {
		while($row = $db->fObject()) {
			$list .= "Name(Shortform): <highlight>$row->raid_name<end>(".bot::makeLink("Assign the item to this raid", "/tell <myname> raidconfig additem $itemid $row->shortform", "chatcmd").")";
			$list .= "\n\n";
		}
	}
	
	$msg = bot::makeLink("Assign an item to a Loottable of a raid", $list);
	bot::send($msg, $sender);
} elseif(eregi("^raidconfig additem (.+)$", $message, $arr)) {
	$name = strtolower($arr[1]);
	
	$tmp = explode(" ", $name);
	$first = true;
	foreach($tmp as $key => $value) {
		if($first) {
			$query .= "`name` LIKE \"%$value%\"";
			$first = false;
		} else
			$query .= " AND `name` LIKE '%$value%'";		
	}
	
	$db->query("SELECT * FROM aodb WHERE $query ORDER BY `name` LIMIT 0, {$this->settings["maxitems"]}");
	$num = $db->numrows();
	if($num == 0) {
	    $msg = "No items found. Maybe try fewer keywords.";
		bot::send($msg, $sender);
		return;
	}
	
	$countitems = 0;
	while($row = $db->fObject()) {
		if(!isset($itemlist[$row->name])) {
			$itemlist[$row->name] = array(array("lowid" => $row->lowid, "highid" => $row->highid, "lowql" => $row->lowql, "highql" => $row->highql, "icon" => $row->icon));
			$countitems++;
		} elseif(isset($itemlist[$row->name])) {
		  	if($itemlist[$row->name][0]["lowql"] > $row->lowql) {
			    $itemlist[$row->name][0]["lowql"] = $row->lowql;
			    $itemlist[$row->name][0]["lowid"] = $row->lowid;
			} elseif($itemlist[$row->name][0]["highql"] < $row->highql) {
			    $itemlist[$row->name][0]["highql"] = $row->highql;
			    $itemlist[$row->name][0]["highid"] = $row->highid;		    
			} else {
				$tmp = $itemlist[$row->name];
				$tmp[] = array("lowid" => $row->lowid, "highid" => $row->highid, "lowql" => $row->lowql, "highql" => $row->highql, "icon" => $row->icon);
				$itemlist[$row->name] = $tmp;
				$countitems++;
			}
		}
	}
	
	if($countitems == 0) {
	    $msg = "No items found. Maybe try fewer keywords.";
		bot::send($msg, $sender);
		return;
	}
	
	foreach($itemlist as $name => $item1) {
	 	foreach($item1 as $key => $item) {
			$name = str_replace("\'", "'", $name);
			$name = str_replace("&#58;", ":", $name);
			$name = str_replace("&amp;", "&", $name);
	        $list .= "<img src=rdb://".$item["icon"]."> \n";
	        $list .= bot::makeItem($item["lowid"], $item["highid"], $item["highql"], $name);		  
		
	        if($item["lowql"] != $item["highql"])
		        $list .= " (QL".$item["lowql"]." - ".$item["highql"].")";
	        else
	    	    $list .= " (QL".$item["lowql"].")";
	    }
	    $list .= " (".bot::makeLink("Add this Item", "/tell <myname> raidconfig additem {$item["highid"]}", "chatcmd").")\n\n";
    }
    $list = "<header>::::: Item Search Result :::::<end>\n\n".$list;
    $link = bot::makeLink('Click here to add one of this items', $list);
	
    bot::send($link, $sender);
	
	//Show how many items found		
    $msg = "<highlight>".$countitems."<end> results in total";
    bot::send($msg, $sender);
	      	
	//Show a warning if the maxitems are reached
	if($countitems == $this->settings["maxitems"]) {
	    $msg = "The output has been limited to <highlight>{$this->settings["maxitems"]}<end> items. Specify your search more if your item isn´t listed.";
        bot::send($msg, $sender);
	}
} elseif(eregi("^raidconfig starterpts ([0-9]+)$", $message, $arr)) {
	$pts = $arr[1];
	
	if($pts > 30) {
		$msg = "You can set as max only 30pts as starter points.";
		bot::send($msg, $sender);
	}
	
	$msg = "Starter Points has been set to <highlight>$pts<end>.";
	bot::savesetting("starter_pts", $pts);
	bot::send($msg, $sender);
} elseif(eregi("^raidconfig delitem ([0-9]+) ([0-9a-z]+)$", $message, $arr)) {
	$itemid = $arr[1];
	$raidname = $arr[2];
	
	$db->query("SELECT * FROM raids_items_<myname> WHERE `shortform` = '$raidname' AND `item_aoid` = $itemid");
	if($db->numrows() == 0) {
		$msg = "<red>No item registered with the AOID $itemid on the Raid $raidname!<end>";
		bot::send($msg, $sender);
		return;
	}
	
	$row = $db->fObject();
	$msg = "Deleted the item <highlight>$row->item_name<end>.";
	bot::send($msg, $sender);
	$db->query("DELETE FROM raids_items_<myname> WHERE `shortform` = '$raidname' AND `item_aoid` = $itemid");	
} elseif(eregi("^raidconfig amount ([a-z0-9]+) ([0-9]+)$", $message, $arr)) {
	$raidname = strtolower($arr[1]);
	$itemid = $arr[2];
	
	$db->query("SELECT * FROM raids_settings_<myname> WHERE `shortform` = '$raidname'");
	if($db->numrows() == 0) {
		$msg = "<red>No Raid registered with the name $raidname<end>.";
		bot::send($msg, $sender);
		return;
	}
	
	$db->query("SELECT * FROM raids_items_<myname> WHERE `shortform` = '$raidname' AND `item_aoid` = $itemid");
	if($db->numrows() == 0) {
		$msg = "<red>No Item found on the raidtable for <highlight>$raidname<end> with the id <highlight>$itemid<end>.";
		bot::send($msg, $sender);
		return;
	}
	
	$row = $db->fObject();
	$list = "<header>::::: Set amount of $row->item_name :::::<end>\n\n";
	$list .= "Current Setting: <highlight>$row->item_amount<end>\n\n";

	$list .= "<u>Change to</u>: ";
	$list .= "\n<highlight>Set level Restriction<end>\n";
	for($i = 1; $i <= 20; $i++) {
	 	if($i != $row->item_amount)
			$list .= bot::makeLink("Set amount to $i", "/tell <myname> raidconfig amount $i $itemid $raidname", "chatcmd")."\n";
	}
	
	$msg = bot::makeLink("Set amount for Item $row->item_name in Raid $raidname", $list);	
	bot::send($msg, $sender); 	
} elseif(eregi("^raidconfig amount ([0-9]+) ([0-9]+) ([a-z0-9]+)$", $message, $arr)) {
 	$amount = $arr[1];
 	$itemid = $arr[2];
 	$raidname = strtolower($arr[3]);
 	
 	$db->query("SELECT * FROM raids_settings_<myname> WHERE `shortform` = '$raidname'");
	if($db->numrows() == 0) {
		$msg = "<red>No Raid registered with the name $raidname<end>.";
		bot::send($msg, $sender);
		return;
	}
	
	$db->query("SELECT * FROM raids_items_<myname> WHERE `shortform` = '$raidname' AND `item_aoid` = $itemid");
	if($db->numrows() == 0) {
		$msg = "<red>No Item found on the raidtable for <highlight>$raidname<end> with the id <highlight>$itemid<end>.";
		bot::send($msg, $sender);
		return;
	}
	
	if($amount > 20) {
		bot::send("<red>You can´t set the amount higher than 20!<end>", $sender);
		return;
	}
	
	$row = $db->fObject();
	$db->query("UPDATE raids_items_<myname> SET `item_amount` = $amount WHERE `item_aoid` = $itemid AND `shortform` = '$raidname'");
	$msg = "Amount of the item <highlight>$row->item_name<end> has been set to <highlight>$amount<end> in the raid <highlight>$raidname<end>.";
	bot::send($msg, $sender);
} elseif(eregi("^raidconfig multiloot (0|1) ([0-9]+) ([a-z0-9]+)$", $message, $arr)) {
 	$mode = $arr[1];
 	$itemid = $arr[2];
 	$raidname = strtolower($arr[3]);
 	
 	$db->query("SELECT * FROM raids_settings_<myname> WHERE `shortform` = '$raidname'");
	if($db->numrows() == 0) {
		$msg = "<red>No Raid registered with the name $raidname<end>.";
		bot::send($msg, $sender);
		return;
	}
	
	$db->query("SELECT * FROM raids_items_<myname> WHERE `shortform` = '$raidname' AND `item_aoid` = $itemid");
	if($db->numrows() == 0) {
		$msg = "<red>No Item found on the raidtable for <highlight>$raidname<end> with the id <highlight>$itemid<end>.";
		bot::send($msg, $sender);
		return;
	}
	
	$row = $db->fObject();
	$db->query("UPDATE raids_items_<myname> SET `item_multiloot` = $mode WHERE `item_aoid` = $itemid AND `shortform` = '$raidname'");
	if($mode == 0)
		$mode = "Disabled";
	else
		$mode = "Enabled";
	$msg = "Multiloot has been set to <highlight>$mode<end> for the item <highlight>$row->item_name<end> in the raid <highlight>$raidname<end>.";
	bot::send($msg, $sender);
} elseif(eregi("^raidconfig inport_exportdb$", $message, $arr)) {
 	$list = "<header>::::: Importing or Exporting of the Raidconfiguration :::::<end>\n\n";
 	$list .= "<highlight><u>Import</u><end>:\n";
 	$list .= "<red>Warning. This will delete your current raidconfiguration!<end>\n";
 	$list .= bot::makeLink("Import the default DB", "/tell <myname> raidconfig import raidcfg.sql", "chatcmd")."\n";
 	for($i = 0; $i <= 9; $i++) {
 	 	$file = "raidcfg$i.sql";
		if(file_exists("./sql/$file"))
		 	$list .= bot::makeLink("Import $file", "/tell <myname> raidconfig import $file", "chatcmd")."\n";
	}
 
	$list .= "\n\n<highlight><u>Export</u><end>:\n";
 	for($i = 1; $i <= 9; $i++) {
 	 	$file = "raidcfg$i.sql";
 	 	$list .= bot::makeLink("Export into $file", "/tell <myname> raidconfig export $file", "chatcmd");
		if(file_exists("./sql/$file"))
			$list .= "<red>Existing File will be overridden!<end>";
		$list .= "\n";
	}
	$msg = bot::makeLink("Import/Export of Raidconfigurations", $list);
	bot::send($msg, $sender);
} elseif(eregi("^raidconfig import raidcfg([1-9]*).sql$", $message, $arr)) {
	$file = "raidcfg".$arr[1].".sql";
	if(!file_exists("./sql/$file")) {
		bot::send("<red>The raidconfig file <highlight>$file<end> wasn´t found. Aborting!<end>", $sender);
		return;
	}

	$filearray = file("./sql/$file");
	if(count($filearray) <= 1) {
		bot::send("<red>No valid infos found in the file. Aborting!<end>", $sender);
		return;		
	}
	
	bot::send("Deleting current Raidconfig.", $sender);
	
	$db->query("DELETE FROM raids_settings_<myname>");
	$db->query("DELETE FROM raids_items_<myname>");
	bot::send("Uploading default Raidconfig.", $sender);
	$db->beginTransaction();
	foreach($filearray as $num => $line) {
	 	$line = rtrim($line);
	 	$line = str_replace("<myname>", $this->vars["name"], $line);
		$db->query(rtrim($line));
	}
    $db->Commit();
	bot::send("Uploading successfull.", $sender);
} elseif(eregi("^raidconfig export raidcfg([1-9]).sql$", $message, $arr)) {
	$file = "raidcfg".$arr[1].".sql";
	$fp = fopen("./sql/$file", "w");
	$db->query("SELECT * FROM raids_settings_<myname>");
	while($row = $db->fObject()) {
		$line = "INSERT INTO raids_settings_<myname> VALUES ('$row->raid_name', $row->pts, $row->bid_cost, '$row->shortform', $row->spawntime, $row->new_spawn, $row->status, $row->flat_multiroll);\n";
		fwrite($fp, $line);
	}

	$db->query("SELECT * FROM raids_items_<myname>");
	while($row = $db->fObject()) {
		$line = "INSERT INTO raids_items_<myname> VALUES ('$row->shortform', '$row->cat', '$row->item_name', $row->aoid, $row->item_ql, $row->item_icon, $row->item_amount, $row->item_minlvl, '$row->item_mode', $row->item_multiloot);\n";
		fwrite($fp, $line);
	}

	bot::send("Export successfull.", $sender);
} elseif(eregi("^raidconfig spawntime ([a-z0-9]+)$", $message, $arr)) {
	$raidname = strtolower($arr[1]);
 	
 	$db->query("SELECT * FROM raids_settings_<myname> WHERE `shortform` = '$raidname'");
	if($db->numrows() == 0) {
		$msg = "<red>No Raid registered with the name $raidname<end>.";
		bot::send($msg, $sender);
		return;
	}
	
	$row = $db->fObject();
	$list = "<header>::::: Set Spawntime for $raidname :::::<end>\n\n";
	$list .= "Current Setting: ";
	$list .= "Spawntime: <highlight>";
	if($row->spawntime != 0) {
		$hrs = floor($row->spawntime/3600);
		$mins = ($row->spawntime - ($hrs * 3600)) / 60;
		$list .= "{$hrs}hour(s) {$mins}minutes(s)<end>\n\n";
	} else
		$list .= "Not set.<end>\n\n";
	
	$list .= "<u>Change the spawntime to</u>: \n";
	$list .= bot::makeLink("15minutes", "/tell <myname> raidconfig spawntime $row->shortform 15", "chatcmd")."\n";
	$list .= bot::makeLink("25minutes", "/tell <myname> raidconfig spawntime $row->shortform 25", "chatcmd")."\n";
	$list .= bot::makeLink("30minutes", "/tell <myname> raidconfig spawntime $row->shortform 30", "chatcmd")."\n";
	$list .= bot::makeLink("1hour", "/tell <myname> raidconfig spawntime $row->shortform 60", "chatcmd")."\n";
	$list .= bot::makeLink("1hour 30minutes", "/tell <myname> raidconfig spawntime $row->shortform 90", "chatcmd")."\n";
	$list .= bot::makeLink("2hours", "/tell <myname> raidconfig spawntime $row->shortform 120", "chatcmd")."\n";
	$list .= bot::makeLink("3hours", "/tell <myname> raidconfig spawntime $row->shortform 180", "chatcmd")."\n";
	$list .= bot::makeLink("4hours", "/tell <myname> raidconfig spawntime $row->shortform 240", "chatcmd")."\n";
	$list .= bot::makeLink("5hours", "/tell <myname> raidconfig spawntime $row->shortform 300", "chatcmd")."\n";
	$list .= bot::makeLink("6hours", "/tell <myname> raidconfig spawntime $row->shortform 360", "chatcmd")."\n";
	$list .= bot::makeLink("7hours", "/tell <myname> raidconfig spawntime $row->shortform 420", "chatcmd")."\n";
	$list .= bot::makeLink("8hours", "/tell <myname> raidconfig spawntime $row->shortform 480", "chatcmd")."\n";
	$list .= bot::makeLink("9hours", "/tell <myname> raidconfig spawntime $row->shortform 540", "chatcmd")."\n";
	$list .= bot::makeLink("10hours", "/tell <myname> raidconfig spawntime $row->shortform 600", "chatcmd")."\n";
	$list .= bot::makeLink("11hours", "/tell <myname> raidconfig spawntime $row->shortform 660", "chatcmd")."\n";
	$list .= bot::makeLink("12hours", "/tell <myname> raidconfig spawntime $row->shortform 720", "chatcmd")."\n";
	$list .= bot::makeLink("13hours", "/tell <myname> raidconfig spawntime $row->shortform 780", "chatcmd")."\n";
	$list .= bot::makeLink("14hours", "/tell <myname> raidconfig spawntime $row->shortform 820", "chatcmd")."\n";
	$list .= bot::makeLink("15hours", "/tell <myname> raidconfig spawntime $row->shortform 860", "chatcmd")."\n";
	$list .= bot::makeLink("16hours", "/tell <myname> raidconfig spawntime $row->shortform 920", "chatcmd")."\n";																	
	$list .= bot::makeLink("17hours", "/tell <myname> raidconfig spawntime $row->shortformm 980", "chatcmd")."\n";
	$list .= bot::makeLink("18hours", "/tell <myname> raidconfig spawntime $row->shortform 1040", "chatcmd")."\n";	

	$msg = bot::makeLink("Set Spawntime for $row->raid_name", $list);	
	bot::send($msg, $sender);
} elseif(eregi("^raidconfig spawntime ([a-z0-9]+) ([0-9]+)$", $message, $arr)) {
	$raidname = strtolower($arr[1]);
 	$spawntime = $arr[2];
	
 	$db->query("SELECT * FROM raids_settings_<myname> WHERE `shortform` = '$raidname'");
	if($db->numrows() == 0) {
		$msg = "<red>No Raid registered with the name $raidname<end>.";
		bot::send($msg, $sender);
		return;
	}
	
	$spawntime1 = $spawntime*60;
	$db->query("UPDATE raids_settings_<myname> SET `spawntime` = $spawntime1 WHERE `shortform` = '$raidname'");
	$msg = "The spawntime of the raid <highlight>$raidname<end> has been set to <highlight>$spawntime<end>minutes.";
	bot::send($msg, $sender);
} else
	$syntax_error = true;
?>