<?php

global $loot;
global $residual;

if (preg_match("/^reroll$/i", $message)) {

	//Check if a residual list exits
  	if (!is_array($residual)) {
	    $msg = "There are no remaining items to re-add.";
	    $sendto->reply($msg);
	    return;
	}
  	
  	// Readd remaining loot
	forEach ($residual as $key => $item) {
		$loot[$key]["name"] = $item["name"];
		$loot[$key]["icon"] = $item["icon"];
		$loot[$key]["linky"] = $item["linky"];
		$loot[$key]["multiloot"] = $item["multiloot"];
		$loot[$key]["added_by"] = $sender;
	}

	//Reset residual list
	$residual = "";
	//Show winner list
	$msg = "All remaining items have been re-added by <highlight>$sender<end>. Check <symbol>list.";
	$chatBot->send($msg, 'priv');
	if ($type != 'priv') {
		$sendto->reply($msg);
	}
	if (is_array($loot)) {
		$list = "\n\nUse <symbol>flatroll to roll.\n\n";
		forEach ($loot as $key => $item) {
			$add = Text::make_chatcmd("Add", "/tell <myname> add $key");
			$rem = Text::make_chatcmd("Remove", "/tell <myname> rem");
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
		$msg2 = Text::make_blob("Loot List", $list);
	} else {
		$msg2 = "No List exists yet.";
	}
	$sendto->reply($msg2);
} else {
	$syntax_error = true;
}

?>