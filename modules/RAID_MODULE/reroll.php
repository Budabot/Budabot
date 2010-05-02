<?php

global $loot;
global $residual;

if(eregi("^reroll$", $message)) {

	//Check if a residual list exits
  	if(!is_array($residual)) {
	    $msg = "There are no remaining items to re-add.";
	    bot::send($msg);
	    return;
	}
  	
  	// Readd remaining loot
	foreach($residual as $key => $item) {
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
	bot::send($msg);
	if(is_array($loot)) {
		  	$list = "<header>::::: Loot List :::::<end>\n\nUse <symbol>flatroll or <symbol>roll to roll.\n\n";
			foreach($loot as $key => $item) {
				$add = bot::makeLink("Add", "/tell <myname> add $key", "chatcmd");
				$rem = bot::makeLink("Remove", "/tell <myname> add 0", "chatcmd");
				$added_players = count($item["users"]);
	
				$list .= "<u>Slot #<font color='#FF00AA'>$key</font></u>\n";
			  	if($item["icon"] != "")
			  		$list .= "<img src=rdb://{$item["icon"]}>\n";

				if($item["multiloot"]>1){
					$ml = " <yellow>(x".$item["multiloot"].")<end>";
					}
				else{
					$ml = "";
					}
				if($item["linky"]){
					$itmnm = $item["linky"];
					}
				else{
					$itmnm = $item["name"];
					}
	
				$list .= "Item: <orange>$itmnm<end>".$ml."\n";
				if($item["minlvl"] != "")
					$list .= "MinLvl set to <highlight>{$item["minlvl"]}<end>\n";

			
				$list .= "<highlight>$added_players<end> Total ($add/$rem)\n";
			  	$list .= "Players added:";
				if(count($item["users"]) > 0)
					foreach($item["users"] as $key => $value)
					  	$list .= " [<yellow>$key<end>]";
				else
					$list .= " None added yet.";
				
				$list .= "\n\n";
			}
			$msg2 = bot::makeLink("New loot List", $list);
		} else{
			$msg2 = "No List exists yet.";
			}
		bot::send($msg2);
}
?>