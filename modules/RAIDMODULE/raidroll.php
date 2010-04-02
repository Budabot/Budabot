<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Rolls the raidloot
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
if(eregi("^raidroll$", $message, $arr)) {
	if($this->vars["raid_status"] == "") {
		$msg = "You need to start a raid first.";
		bot::send($msg);
		return;
	}
	
 	if(count($raidloot) == 0) {
		bot::send("No Lootlist builded yet.");
	  	return;
	}
	
	if($this->vars["raid_pts"] > 0) {
		if($this->vars["raid_loot_index"] == 0 && $this->vars["raid_loot_cat"] == 0) {
		  	bot::send("Nothing to roll atm.");
		  	return;
		}
		
		$index = $this->vars["raid_loot_index"];
		$cat = $this->vars["raid_loot_cat"];
		
		if(count($raidloot[$cat][$index]["users"]) == 0) {
		  	bot::send("No one bidded on the item.");
			$this->vars["raid_loot_cat"] = 0;
			$this->vars["raid_loot_index"] = 0;
			return;
		}
		
		$roll_list = $raidloot[$cat][$index]["users"];
		arsort($roll_list, SORT_NUMERIC);

		$old_pts = 0;
		foreach($roll_list as $key => $value) {
		  	if($old_pts == 0) {
				$old_pts = $value;
				$winners[] = $key;
			} elseif($old_pts == $value && $raidloot[$cat][$index]["amount"] == 1) {
			  	$winners[] = $key;
			} elseif($raidloot[$cat][$index]["amount"] > 1 && (count($winners) < $raidloot[$cat][$index]["amount"])) {
			  	$winners[] = $key;
			} else
				break;
		}
		
		$db->beginTransaction();

		$info = "<header>::::: Info :::::<end>\n\n";
		$info .= "<img src=rdb://{$raidloot[$cat][$index]["icon"]}>\n";
		if(isset($raidloot[$cat][$index]["aoid"]))
			$info .= bot::makeItem($raidloot[$cat][$index]["aoid"], $raidloot[$cat][$index]["aoid"], $raidloot[$cat][$index]["ql"], $raidloot[$cat][$index]["name"])."\n";
		else
			$info .= "<highlight>{$raidloot[$cat][$index]["name"]}<end>\n";

		$info .= "<u>Winner(s)</u>:\n";
		if(count($winners) == 1) {
			$raidloot[$cat][$index]["amount"] -= 1;
			$info .= "<tab>- <highlight>".$winners[0]."<end>\n";
		  	$msg = "<highlight>".$winners[0]."<end> won the item <highlight>{$raidloot[$cat][$index]["name"]}<end> with a bid of <highlight>{$raidloot[$cat][$index]["users"][$winners[0]]}<end>point(s).";
		  	$db->query("INSERT INTO raids_history_<myname> VALUES ('{$winners[0]}', {$raidloot[$cat][$index]["users"][$winners[0]]}, ".time().", \"{$raidloot[$cat][$index]["name"]}\", {$raidloot[$cat][$index]["icon"]}, {$raidloot[$cat][$index]["aoid"]}, {$raidloot[$cat][$index]["ql"]})");	  	
		} elseif($raidloot[$cat][$index]["amount"] == 1 || $raidloot[$cat][$index]["amount"] == 0) {
		 	$winners = array_flip($winners);
		  	$winner = array_rand($winners, 1);
			$info .= "<tab>- <highlight>".$winner."<end>\n";
		  	$msg = "<highlight>".$winner."<end> won the item <highlight>{$raidloot[$cat][$index]["name"]}<end> with a bid of <highlight>{$raidloot[$cat][$index]["users"][$winner]}<end>point(s).";
			$raidloot[$cat][$index]["amount"] = 0;
			$db->query("INSERT INTO raids_history_<myname> VALUES ('{$winner}', {$raidloot[$cat][$index]["users"][$winner]}, ".time().", \"{$raidloot[$cat][$index]["name"]}\", {$raidloot[$cat][$index]["icon"]}, {$raidloot[$cat][$index]["aoid"]}, {$raidloot[$cat][$index]["ql"]})");
		} else {
			$winners = array_flip($winners);
			if(count($winners) >= $raidloot[$cat][$index]["amount"])
			  	$winner = array_rand($winners, $raidloot[$cat][$index]["amount"]);
			else
				$winner = array_rand($winners, count($winners));
			$msg = "Winner of the <highlight>".count($winners)." {$raidloot[$cat][$index]["name"]}<end> are:";
		  	foreach($winner as $key => $value) {
		  	 	$msg .= " [<highlight>$value<end>]";
 				$info .= "<tab>- <highlight>".$value."<end>\n";
		  	 	$db->query("INSERT INTO raids_history_<myname> VALUES ('{$value}', {$raidloot[$cat][$index]["users"][$value]}, ".time().", \"{$raidloot[$cat][$index]["name"]}\", {$raidloot[$cat][$index]["icon"]}, {$raidloot[$cat][$index]["aoid"]}, {$raidloot[$cat][$index]["ql"]})");
		  	}
		  	$raidloot[$cat][$index]["amount"] -= count($winner);
		}
		
		if(count($winners) != (count($roll_lost) + 1))
			$info .= "\n------------------------------------\nThe following player getting {$this->vars["raid_bid_cost"]}point deducted for bidding:\n";
			
		$temp = array_flip($winners);
		foreach($roll_list as $key => $value) {
		  	if(isset($temp[$key]))
		  		$db->query("UPDATE `points_db_<myname>` SET `points` = `points` - $value - {$this->vars["raid_bid_cost"]} WHERE `name` = '$key'");
		  	else {
		  	 	$info .= "<tab>- <highlight>".$key."<end>\n";
		  		$db->query("UPDATE `points_db_<myname>` SET `points` = `points` - {$this->vars["raid_bid_cost"]} WHERE `name` = '$key'");
		  	}
		}
		
		$db->Commit();
		
		unset($raidloot[$cat][$index]["users"]);
		$this->vars["raid_loot_index"] = 0;
		$this->vars["raid_loot_cat"] = 0;
		$info = bot::makeLink("Info", $info);
		bot::send($msg." (".$info.")");
	} elseif($this->vars["raid_flat_multiroll"] == 1) {
	  	$list = "<header>::::: Win List :::::<end>\n\n";
	  	$db->beginTransaction();
		print_r($raidloot);
		foreach($raidloot as $key => $value) {
		 	foreach($value as $key1 => $value1) {
			 	if($raidloot[$key][$key1]["mode"] == "flat") {
	 				if($raidloot[$key][$key1]["icon"] != 0) {
						$list .= "<img src=rdb://{$raidloot[$key][$key1]["icon"]}>\n";
					}
					if($raidloot[$key][$key1]["aoid"] != 0)
						$list .= "Item: ".bot::makeItem($raidloot[$key][$key1]["aoid"], $raidloot[$key][$key1]["aoid"], $raidloot[$key][$key1]["ql"], $raidloot[$key][$key1]["name"])."\n";
					else
						$list .= "Item: <highlight>{$raidloot[$key][$key1]["name"]}<end>\n";
			  	  	$list .= "Winner(s): ";
				    $users = count($raidloot[$key][$key1]["users"]);
				 	if($users == 0)
				 		$list .= "<highlight>None added.<end>\n\n";
					elseif($raidloot[$cat][$index]["amount"] == 1 || $raidloot[$cat][$index]["amount"] == 0) {
            			$raidloot[$cat][$index]["amount"] = 0;
						$winner = array_rand($raidloot[$key][$key1]["users"], 1);
						$list .= "<highlight>$winner<end>\n\n";
						$db->query("INSERT INTO raids_history_<myname> VALUES ('{$winner}', 'flat', ".time().", \"{$raidloot[$key][$key1]["name"]}\", {$raidloot[$key][$key1]["icon"]}, {$raidloot[$key][$key1]["aoid"]}, {$raidloot[$key][$key1]["ql"]})");		
					} else {
						$winners = array_flip($raidloot[$key][$key1]["users"]);
						if(count($winners) >= $raidloot[$key][$key1]["amount"])
						  	$winner = array_rand($winners, $raidloot[$key][$key1]["amount"]);
						else
							$winner = array_rand($winners, count($winners));
						$msg = "Winner of the <highlight>".count($winners)." {$raidloot[$key][$key1]["name"]}<end> are:";
					  	foreach($winner as $key2 => $value2) {
					  	 	$msg .= " [<highlight>$value2<end>]";
			 				$info .= "<tab>- <highlight>".$value2."<end>\n";
					  	 	$db->query("INSERT INTO raids_history_<myname> VALUES ('{$value2}', {$raidloot[$key][$key1]["users"][$value2]}, ".time().", \"{$raidloot[$key][$key1]["name"]}\", {$raidloot[$key][$key1]["icon"]}, {$raidloot[$key][$key1]["aoid"]}, {$raidloot[$key][$key1]["ql"]})");
					  	}
					  	$raidloot[$key][$key1]["amount"] -= count($winner);
					}
				}
			}
		}
		$db->Commit();
		//Reset loot
		$raidloot = "";
		//Show winner list
		$msg = bot::makeLink("Winner List", $list);
		bot::send($msg);
	} else {
		if($this->vars["raid_loot_index"] == 0 && $this->vars["raid_loot_cat"] == 0) {
		  	bot::send("Nothing to roll atm.");
		  	return;
		}
		
		$index = $this->vars["raid_loot_index"];
		$cat = $this->vars["raid_loot_cat"];
		
		if(count($raidloot[$cat][$index]["users"]) == 0) {
		  	bot::send("No one added to this item.");
			$this->vars["raid_loot_cat"] = 0;
			$this->vars["raid_loot_index"] = 0;
			return;
		}
		$users = $raidloot[$cat][$index]["users"];	
		$info = "<header>::::: Info :::::<end>\n\n";
		$info .= "<img src=rdb://{$raidloot[$cat][$index]["icon"]}>\n";
		if(isset($raidloot[$cat][$index]["aoid"]))
			$info .= bot::makeItem($raidloot[$cat][$index]["aoid"], $raidloot[$cat][$index]["aoid"], $raidloot[$cat][$index]["ql"], $raidloot[$cat][$index]["name"])."\n";
		else
			$info .= "<highlight>{$raidloot[$cat][$index]["name"]}<end>\n";

		$info .= "<u>Winner(s)</u>:\n";
		if(count($users) == 1) {
		 	$winner = array_rand($users, 1);
			$info .= "<tab>- <highlight>".$winner."<end>\n";
		  	$msg = "<highlight>".$winner."<end> won the item <highlight>{$raidloot[$cat][$index]["name"]}<end>.";
		  	$db->query("INSERT INTO raids_history_<myname> VALUES ('{$winner}', 'flat', ".time().", \"{$raidloot[$cat][$index]["name"]}\", {$raidloot[$cat][$index]["icon"]}, {$raidloot[$cat][$index]["aoid"]}, {$raidloot[$cat][$index]["ql"]})");	  	
		} elseif($raidloot[$cat][$index]["amount"] == 1 || $raidloot[$cat][$index]["amount"] == 0) {
		  	$winner = array_rand($users, 1);
			$info .= "<tab>- <highlight>".$winner."<end>\n";
		  	$msg = "<highlight>".$winner."<end> won the item <highlight>{$raidloot[$cat][$index]["name"]}<end>.";
			$db->query("INSERT INTO raids_history_<myname> VALUES ('{$winner}', 'flat', ".time().", \"{$raidloot[$cat][$index]["name"]}\", {$raidloot[$index]["icon"]}, {$raidloot[$index]["aoid"]}, {$raidloot[$index]["ql"]})");
		} else{
			$winners = array_flip($raidloot[$key][$key1]["users"]);
			if(count($winners) >= $raidloot[$key][$key1]["amount"])
			  	$winner = array_rand($winners, $raidloot[$key][$key1]["amount"]);
			else
				$winner = array_rand($winners, count($winners));
			$msg = "Winner of the <highlight>".count($winners)." {$raidloot[$key][$key1]["name"]}<end> are:";
		  	foreach($winner as $key2 => $value2) {
		  	 	$msg .= " [<highlight>$value2<end>]";
 				$info .= "<tab>- <highlight>".$value2."<end>\n";
		  	 	$db->query("INSERT INTO raids_history_<myname> VALUES ('{$value2}', {$raidloot[$key][$key1]["users"][$value2]}, ".time().", \"{$raidloot[$key][$key1]["name"]}\", {$raidloot[$key][$key1]["icon"]}, {$raidloot[$key][$key1]["aoid"]}, {$raidloot[$key][$key1]["ql"]})");
		  	}
		  	$raidloot[$key][$key1]["amount"] -= count($winner);
		}

		$info .= "\n------------------------------------\nThe following player getting were added to this roll:\n";
			
		foreach($users as $key => $value) {
	  	 	$info .= "<tab>- <highlight>".$key."<end>\n";
		}
		
		unset($raidloot[$cat][$index]["users"]);
		$this->vars["raid_loot_index"] = 0;
		$this->vars["raid_loot_cat"] = 0;
		$info = bot::makeLink("Info", $info);
		bot::send($msg." (".$info.")");		
	}
} else
	$syntax_error = true;
?>