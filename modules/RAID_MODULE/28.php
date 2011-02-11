<?php

global $loot;
global $residual;

if(preg_match("/^28$/i", $message)) {

	// clearing loot list
	$loot = "";
	$residual = "";

	// adding apf stuff

	$loot[1]["name"] = "Crystaline Matrix";
	$loot[1]["linky"] = "<a href='itemref://275912/275912/1'>Crystaline Matrix</a>";
	$loot[1]["icon"] = "275964";
	$loot[1]["multiloot"] = 3;

	$loot[2]["name"] = "Kyr&#039;Ozch Circuitry";
	$loot[2]["linky"] = "<a href='itemref://275914/275914/1'>Kyr&#039;Ozch Circuitry</a>";
	$loot[2]["icon"] = "275966";
	$loot[2]["multiloot"] = 3;

	$loot[3]["name"] = "Inertial Adjustment Processing Unit";
	$loot[3]["linky"] = "<a href='itemref://257959/257959/250'>Inertial Adjustment Processing Unit</a>";
	$loot[3]["icon"] = "11618";

	$loot[4]["name"] = "Notum Amplification Coil";
	$loot[4]["linky"] = "<a href='itemref://257963/257963/250'>Notum Amplification Coil</a>";
	$loot[4]["icon"] = "257195";

	$loot[5]["name"] = "All Bounties";
	$loot[5]["linky"] = "<a href='itemref://257533/257533/1'>All Bounties</a>";
	$loot[5]["icon"] = "218758";
	
	$loot[6]["name"] = "All ICE";
	$loot[6]["linky"] = "<a href='itemref://257968/257968/1'>All ICE</a>";
	$loot[6]["icon"] = "257196";

	$loot[7]["name"] = "Kyr&#039;Ozch Helmet (2500 Token board)";
	$loot[7]["linky"] = "<a href='itemref://257706/257706/1'>Kyr&#039;Ozch Helmet (2500 Token board)</a>";
	$loot[7]["icon"] = "230855";

	$msg = "Sector 28 loot table was added to the loot list by <highlight>$sender<end>.";

	bot::send($msg);

	// Displaying new list
	if ($this->vars["raid_status"] == "") {
	  	if (is_array($loot)) {
		  	$list = "<header>::::: Sector28 Loot List :::::<end>\n\nUse <symbol>flatroll or <symbol>roll to roll.\n\n";
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
			$msg = Text::make_link("Sector28 loot List", $list);
		} else {
			$msg = "No List exists yet.";
		}
	
	} else {
		bot::send("No list available!");
		return;
	}

	bot::send($msg);
}
?>