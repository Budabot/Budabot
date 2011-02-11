<?php

/*
   ** Author: Chachy (RK2), based on code for Pande Loot Bot by Marinerecon (RK2)
   ** Description: DB Loot Module
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 09.01.2009
   ** Date(last modified): 09.02.2009
   **
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

//:::: DB Armor / Programs / NCUs ::::
$dbloot[1]["name"] = "Enhanced Dustbrigade Combat Chestpiece";
$dbloot[1]["img"] = "32162";
$dbloot[1]["ref"] = "269993";
$dbloot[2]["name"] = "Enhanced Dustbrigade Spirit-tech Chestpiece";
$dbloot[2]["img"] = "32162";
$dbloot[2]["ref"] = "269994";
$dbloot[3]["name"] = "Enhanced Dustbrigade Sleeves";
$dbloot[3]["img"] = "13233";
$dbloot[3]["ref"] = "269996";
$dbloot[4]["name"] = "Enhanced Dustbrigade Notum Gloves";
$dbloot[4]["img"] = "21871";
$dbloot[4]["ref"] = "270394";
$dbloot[5]["name"] = "Enhanced Dustbrigade Chemist Gloves";
$dbloot[5]["img"] = "21871";
$dbloot[5]["ref"] = "270393";
$dbloot[6]["name"] = "Enhanced Dustbrigade Covering";
$dbloot[6]["img"] = "155108";
$dbloot[6]["ref"] = "269997";
$dbloot[7]["name"] = "Enhanced Dustbrigade Flexible Boots";
$dbloot[7]["img"] = "31746";
$dbloot[7]["ref"] = "270392";
$dbloot[8]["name"] = "Enhanced Safeguarded NCU Memory Unit (str/sta)";
$dbloot[8]["img"] = "119134";
$dbloot[8]["ref"] = "269986";
$dbloot[9]["name"] = "Enhanced Safeguarded NCU Memory Unit (agi/sen)";
$dbloot[9]["img"] = "119134";
$dbloot[9]["ref"] = "269987";
$dbloot[10]["name"] = "Enhanced Safeguarded NCU Memory Unit (int/psy)";
$dbloot[10]["img"] = "119134";
$dbloot[10]["ref"] = "269985";
$dbloot[11]["name"] = "Protected Safeguarded NCU Memory Unit (evades)";
$dbloot[11]["img"] = "269989";
$dbloot[11]["ref"] = "269990";
$dbloot[12]["name"] = "Master Melee Program (Alappaa Pad Upgrade)";
$dbloot[12]["img"] = "269948";
$dbloot[12]["ref"] = "269961";
$dbloot[13]["name"] = "Master Combat Program (Alappaa Pad Upgrade)";
$dbloot[13]["img"] = "269949";
$dbloot[13]["ref"] = "269960";
$dbloot[14]["name"] = "Master Nano Technology Program (Alappaa Pad Upgrade)";
$dbloot[14]["img"] = "269950";
$dbloot[14]["ref"] = "269959";
//:::: DB2 Loot ::::
$dbloot[15]["name"] = "Basic Infused Dust Brigade Bracer";
$dbloot[15]["img"] = "84062";
$dbloot[15]["ref"] = "274541";
$dbloot[16]["name"] = "Dust Brigade Notum Infuser (DB Bracer/Alb Item Upgrades)";
$dbloot[16]["img"] = "218768";
$dbloot[16]["ref"] = "274552";
$dbloot[17]["name"] = "All Molybdenum-Matrix of Xan (2 Black, 6 White Kegern/Jathos Upgrades)";
$dbloot[17]["img"] = "272534";
$dbloot[17]["ref"] = "272458";
$dbloot[18]["name"] = "Dust Brigade Engineer Pistol";
$dbloot[18]["img"] = "264787";
$dbloot[18]["ref"] = "274559";
$dbloot[19]["name"] = "Dust Brigade Solar Notum Infuser (Engineer Solar Pistol Upgrades)";
$dbloot[19]["img"] = "218768";
$dbloot[19]["ref"] = "274558";

global $loot;
global $residual;
global $raidloot;
global $vote;

if (preg_match("/^dbloot ([0-9]+)$/i", $message, $arr)) {
	$val = $arr[1];
	$itemname = $dbloot[$val]["name"];
	$dontadd = 0;
	forEach ($loot as $key => $item) {
		if ($item["name"] == $itemname) {
			$loot[$key]["multiloot"] = $item["multiloot"]+1;
			$total = $item["multiloot"]+1;
			$dontadd = 1;
			$slot = $key;
		}
	}

	if ($dontadd == 0) {
		if (is_array($loot)) {
			if (count($loot) < 31) {
				$nextloot = count($loot) + 1;
			} else {
				bot::send("You can only roll 30 items max at one time!", $sendto);
				return;
			}
		} else {
			$nextloot = 1;
		}
		
		$loot[$nextloot]["name"] = $dbloot[$val]["name"];
		$loot[$nextloot]["linky"] = Text::make_item($dbloot[$val]["ref"], $dbloot[$val]["ref"], 300, $dbloot[$val]["name"]);
		$loot[$nextloot]["icon"] = $dbloot[$val]["img"];
		$loot[$nextloot]["multiloot"] = 1;
		bot::send("<highlight>".$itemname."<end> will be rolled in Slot <highlight>#".$nextloot, 'priv');
	} else {
		bot::send("<highlight>".$itemname."<end> will be rolled in Slot <highlight>#".$slot."<end> as multiloot. Total: <yellow>".$total."<end>", 'priv');
	}
	bot::send("To add use !add ".$nextloot.", or !add 0 to remove yourself", 'priv');
} else if (preg_match("/^db1$/i", $message)) {
	$list = "<header>::::: DB1 Loot :::::<end>\n\n\n";
	$loop = 1;
	while ($loop <= 14) {
		$addlink = Text::make_link("Add to Loot List", "/tell <myname> dbloot ".$loop, "chatcmd");
		$ref = $dbloot[$loop]["ref"];
		$list .= Text::make_item($ref, $ref, 300, "<img src=rdb://{$dbloot[$loop]["img"]}>");
		$list .= "\nItem: <highlight>".$dbloot[$loop]["name"]."<end>\n".$addlink."\n\n";
		$loop++;
	}
	$msg = Text::make_link("DB1 Loot", $list);
	bot::send($msg, $sendto);
} else if (preg_match("/^db2$/i", $message)) {
	//List DB Armor choices
	$list = "<header>::::: DB2 Armor :::::<end>\n\n\n";
	$loop = 1;
	while ($loop <= 7) {
		$addlink = Text::make_link("Create Loot List", "/tell <myname> db2loot ".$loop, "chatcmd");
		$ref = $dbloot[$loop]["ref"];
		$list .=  Text::make_item($ref, $ref, 300, "<img src=rdb://{$dbloot[$loop]["img"]}>");
		$list .= "\nItem: <highlight>".$dbloot[$loop]["name"]."<end>\n".$addlink."\n\n";
		$loop++;
	}
	$msg = Text::make_link("DB2 Armor", $list);
	bot::send($msg, $sendto);
} else if (preg_match("/^db2loot ([0-9]+)$/i", $message, $arr)) {
	$val = $arr[1];

	//Clearing loot list
	$loot = "";
	$residual = "";

	//Add armor item in first loot slot
	$nextloot = 1;

	$loot[$nextloot]["name"] = $dbloot[$val]["name"];
	$loot[$nextloot]["linky"] =  Text::make_item($dbloot[$val]["ref"], $dbloot[$val]["ref"], 300, $dbloot[$val]["name"]);
	$loot[$nextloot]["icon"] = $dbloot[$val]["img"];

	//Set up standard loot
	$nextloot++;
	$val = 15;
	while ($val <= 19) {
		$loot[$nextloot]["name"] = $dbloot[$val]["name"];
		$loot[$nextloot]["linky"] = Text::make_item($dbloot[$val]["ref"], $dbloot[$val]["ref"], 300, $dbloot[$val]["name"]);
		$loot[$nextloot]["icon"] = $dbloot[$val]["img"];
		$nextloot++;
		$val++;
	}
	$loot[3]["multiloot"] = 2;

	bot::send("DB2 loot table was added to the loot list by <highlight>$sender<end>.", 'priv');
} else {
	$syntax_error = true;
}

?>