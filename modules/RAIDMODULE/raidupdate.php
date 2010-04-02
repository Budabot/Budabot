<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Changes the raidname/loot with keeping the raidlist
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 10.10.2006
   ** Date(last modified): 22.11.2006
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

global $raidloot;
global $raidlist;
global $raid_points;
if(eregi("^raidupdate (.+)$", $message, $arr)) {
	if($this->vars["raid_status"] == "") {
		$msg = "<red>No raid is running at the moment.<end>";
		bot::send($msg);
		return;
	}
	
	$raidname = trim($arr[1]);
  	
  	$db->query("SELECT * FROM raids_settings_<myname> WHERE `shortform` = '$raidname' AND `status` = 1");
  	if($db->numrows() == 0) {
		$msg = "<red>No raid settings found for $raidname or it is not enabled yet.<end>";
		bot::send($msg);
		return;		
	}
  	
  	$raid_settings = $db->fObject();
  	
	$this->vars["raid_status"] = $raid_settings->raid_name;
  	$msg = "Raid for <highlight>{$raid_settings->raid_name}<end> has been started.";
  	bot::send($msg);
  	
  	bot::savesetting("topic_time", time());
  	bot::savesetting("topic_setby", $sender);
  	bot::savesetting("topic", $msg);
  	
  	$raid_points = false;
	$this->vars["raid_loot_cat"] = 0;
	$this->vars["raid_loot_index"] = 0;
	$this->vars["raid_loot_pts"] = $raidsettings->pts;
	$this->vars["raid_bid_cost"] = $raidsettings->bid_cost;
	if($raid_settings->pts == 0)
		$this->vars["raid_flat_multiroll"] = $raid_settings->flat_multiroll;
		
	$db->query("SELECT * FROM raids_items_<myname> WHERE `shortform` = '$raidname'");
	if($db->numrows() == 0) {
		$msg = "<red>No items entered for the raid $raidname.<end>";
		bot::send($msg);
		return;				
	}
	
	$i = 0;
	$raidloot = "";
	while($row = $db->fObject()) {
	 	if($row->item_multiloot == 0 && $row->item_amount > 1) {
	 		for($j = 1; $j <= $row->item_amount; $j++) {
				$raidloot[$row->cat][$i]["name"] = $row->item_name;
				$raidloot[$row->cat][$i]["aoid"] = $row->item_aoid;
				$raidloot[$row->cat][$i]["ql"] = $row->item_ql;
				$raidloot[$row->cat][$i]["icon"] = $row->item_icon;
				$raidloot[$row->cat][$i]["amount"] = 1;
				if($raid_settings->pts == 0)
					$raidloot[$row->cat][$i]["mode"] = "flat";
				else
					$raidloot[$row->cat][$i]["mode"] = "pts";			
				$i++;
			}
	 	} else {
			$raidloot[$row->cat][$i]["name"] = $row->item_name;
			$raidloot[$row->cat][$i]["aoid"] = $row->item_aoid;
			$raidloot[$row->cat][$i]["ql"] = $row->item_ql;
			$raidloot[$row->cat][$i]["icon"] = $row->item_icon;
			$raidloot[$row->cat][$i]["amount"] = $row->item_amount;
			if($raid_settings->pts == 0)
				$raidloot[$row->cat][$i]["mode"] = "flat";
			else
				$raidloot[$row->cat][$i]["mode"] = "pts";			
			$i++;
		}
	}
} else
	$syntax_error = true;
?>