<?php

global $loot;
global $residual;

if (preg_match("/^35$/i", $message)) {

	// clearing loot list
	$loot = "";
	$residual = "";

	// adding apf stuff

	$loot[1]["name"] = "Alpha Program Chip";
	$loot[1]["linky"] = "<a href='itemref://275918/275918/1'>Alpha Program Chip</a>";
	$loot[1]["icon"] = "275970";
	$loot[1]["multiloot"] = 3;

	$loot[2]["name"] = "Beta Program Chip";
	$loot[2]["linky"] = "<a href='itemref://275919/275919/1'>Beta Program Chip</a>";
	$loot[2]["icon"] = "275969";
	$loot[2]["multiloot"] = 3;

	$loot[3]["name"] = "Odd Kyr&#039;Ozch Nanobots";
	$loot[3]["linky"] = "<a href='itemref://275906/275906/1'>Odd Kyr&#039;Ozch Nanobots</a>";
	$loot[3]["icon"] = "11750";
	$loot[3]["multiloot"] = 3;

	$loot[4]["name"] = "Kyr&#039;Ozch Processing Unit";
	$loot[4]["linky"] = "<a href='itemref://275907/275907/1'>Kyr&#039;Ozch Processing Unit</a>";
	$loot[4]["icon"] = "275960";
	$loot[4]["multiloot"] = 3;

	$loot[5]["name"] = "Energy Redistribution Unit";
	$loot[5]["linky"] = "<a href='itemref://257961/257961/250'>Energy Redistribution Unit</a>";
	$loot[5]["icon"] = "257197";

	$loot[6]["name"] = "Visible Light Remodulation Device";
	$loot[6]["linky"] = "<a href='itemref://257964/257964/250'>Visible Light Remodulation Device</a>";
	$loot[6]["icon"] = "235270";

	$loot[7]["name"] = "All Bounties";
	$loot[7]["linky"] = "<a href='itemref://257533/257533/1'>All Bounties</a>";
	$loot[7]["icon"] = "218758";
	
	$loot[8]["name"] = "All ICE";
	$loot[8]["linky"] = "<a href='itemref://257968/257968/1'>All ICE</a>";
	$loot[8]["icon"] = "257196";

	$loot[9]["name"] = "Kyr&#039;Ozch Helmet (2500 Token board)";
	$loot[9]["linky"] = "<a href='itemref://257706/257706/1'>Kyr&#039;Ozch Helmet (2500 Token board)</a>";
	$loot[9]["icon"] = "230855";

	$msg = "Sector 35 loot table was added to the loot list by <highlight>$sender<end>.";

	$chatBot->send($msg);

	// Displaying new list
	if ($this->vars["raid_status"] == "") {
	  	if (is_array($loot)) {
		  	$list = "<header>::::: Sector35 Loot List :::::<end>\n\nUse <symbol>flatroll or <symbol>rollloot to roll.\n\n";
			forEach ($loot as $key => $item) {
				$add = Text::make_link("Add", "/tell <myname> add $key", "chatcmd");
				$rem = Text::make_link("Remove", "/tell <myname> add 0", "chatcmd");
				$added_players = count($item["users"]);
	
				$list .= "<u>Slot #<font color='#FF00AA'>$key</font></u>\n";
			  	if ($item["icon"] != "") {
			  		$list .= "<img src=rdb://{$item["icon"]}>\n";
				}

				if ($item["multiloot"] > 1) {
					$ml = " <yellow>(x".$item["multiloot"].")<end>";
				} else {
					$ml = "";
				}
				
				if ($item["linky"]) {
					$itmnm = $item["linky"];
				} else {
					$itmnm = $item["name"];
				}
	
				$list .= "Item: <orange>$itmnm<end>".$ml."\n";
				if ($item["minlvl"] != "") {
					$list .= "MinLvl set to <highlight>{$item["minlvl"]}<end>\n";
				}

				$list .= "<highlight>$added_players<end> Total ($add/$rem)\n";
			  	$list .= "Players added:";
				if (count($item["users"]) > 0) {
					forEach ($item["users"] as $key => $value) {
					  	$list .= " [<yellow>$key<end>]";
					}
				} else {
					$list .= " None added yet.";
				}
				
				$list .= "\n\n";
			}
			$msg = Text::make_link("Sector35 loot List", $list);
		} else {
			$msg = "No List exists yet.";
		}
	
	} else {
		$chatBot->send("No list available!");
		return;
	}

	$chatBot->send($msg);
}
?>