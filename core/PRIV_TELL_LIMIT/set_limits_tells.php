<?php

if (preg_match("/^tminlvl$/i", $message)) {
 	if (Setting::get("tell_req_lvl") == 0) {
 		$msg = "No Level Limit has been set for responding on tells.";
 	} else {
		$msg = "Level Limit for responding on tells is set to Lvl " . Setting::get("tell_req_lvl") . ".";
	}
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^tminlvl ([0-9]+)$/i", $message, $arr)) {
	$minlvl = strtolower($arr[1]);
	
	if ($minlvl > 220 || $minlvl < 0) {
		$msg = "<red>Minimum Level can be only set between 1-220<end>";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	Setting::save("tell_req_lvl", $minlvl);
	
	if ($minlvl == 0) {
		$msg = "Player min level limit has been removed from responding on tells.";
	} else {
 		$msg = "Responding on tells will be done for the Minimumlevel of $minlvl.";
 	}
 	
    $chatBot->send($msg, $sendto);     	
} else if (preg_match("/^topen$/i", $message)) {
 	if (Setting::get("tell_req_open") == "all") {
 		$msg = "No General Limit is set for responding on tells.";
	} else if (Setting::get("tell_req_open") == "org") {
 		$msg = "General Limit for responding on tells is set to Organisation members only.";
	} else {
		$msg = "General Limit for responding on tells is set to Bot members only.";
	}
		
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^topen (org|all|members)$/i", $message, $arr)) {
	$open = strtolower($arr[1]);
	
	Setting::save("tell_req_open", $open);
	
	if ($open == "all") {
		$msg = "General restriction for responding on tells has been removed.";
	} else if ($open == "org") {
 		$msg = "Responding on tells will be done only for Members of your Organisation.";
 	} else {
 		$msg = "Responding on tells will be done only for Members of this Bot.";
 	}
 	
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^tfaction$/i", $message)) {
 	if (Setting::get("tell_req_faction") == "all") {
 		$msg = "No Faction Limit is set for responding on tells.";
	} else {
		$msg = "Faction Limit for responding on tells is set to " . Setting::get("tell_req_faction") . ".";
	}
		
    $chatBot->send($msg, $sendto);	
} else if (preg_match("/^tfaction (omni|clan|neutral|all)$/i", $message, $arr)) {
	$faction = ucfirst(strtolower($arr[1]));
	
	Setting::save("tell_req_faction", $faction);
	
	if ($faction == "all") {
		$msg = "Faction limit removed for tell responces.";
	} else {
 		$msg = "Responding on tells will be done only for players with the Faction $faction.";
 	}
 	
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^tfaction not (omni|clan|neutral)$/i", $message, $arr)) {
	$faction = ucfirst(strtolower($arr[1]));
	
	Setting::save("tell_req_faction", "not ".$faction);
	
	$msg = "Responding on tells will be done for players that are not $faction.";

    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>