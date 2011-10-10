<?php

if (preg_match("/^limits$/i", $message)) {
	$blob = "<header> :::::: Limits on using the Bot :::::: <end>\n\n";
	$blob .= "The bot offers limits that apply to the private channel(like faction or level limit) and responding to tells. Click behind a setting on Change this to set it to a new value.\n\n";
	$blob .= "<u>Responding to Tells</u>\n";
	
	if (Setting::get("tell_req_faction") == "all") {
		$blob .= "Faction: <highlight>No Limit<end>";
	} else {
		$blob .= "Faction: <highlight>" . Setting::get("tell_req_faction") . "<end>";
	}
	$blob .= " (" . Text::make_chatcmd("Change this", "/tell <myname> limits tell faction").")\n";
	
	if (Setting::get("tell_req_lvl") == 0) {
		$blob .= "Level: <highlight>No Limit<end>";
	} else {
		$blob .= "Level: <highlight>" . Setting::get("tell_req_lvl") . "<end>";
	}
	$blob .= " (" .Text::make_chatcmd("Change this", "/tell <myname> limits tell minlvl").")\n";
	
	if (Setting::get("tell_req_open") == "all") {
		$blob .= "General: <highlight>No general Limit<end>";
	} else if (Setting::get("tell_req_open") == "org") {
		$blob .= "General: <highlight>Responding only to Players that are in the Organistion <myguild><end>";
	} else {
		$blob .= "General: <highlight>Responding only to players that are Members of this Bot<end>";
	}
	$blob .= " (" . Text::make_chatcmd("Change this", "/tell <myname> limits tell open").")\n";

	$blob .= "\n<u>Privatgroup Invites</u>\n";
	$blob .= "Faction: <highlight>" . Setting::get("priv_req_faction") . "<end>";
	$blob .= " (" . Text::make_chatcmd("Change this", "/tell <myname> limits priv faction").")\n";
	
	if (Setting::get("priv_req_lvl") == 0) {
		$blob .= "Level: <highlight>No Limit<end>";
	} else {
		$blob .= "Level: <highlight>" . Setting::get("priv_req_lvl") . "<end>";
	}
	$blob .= " (" . Text::make_chatcmd("Change this", "/tell <myname> limits priv minlvl").")\n";

	if (Setting::get("priv_req_maxplayers") == 0) {
		$blob .= "Player Limit: <highlight>No Limit<end>";
	} else {
		$blob .= "Player Limit: <highlight>" . Setting::get("priv_req_maxplayers") . "<end>";
	}
	$blob .= " (" . Text::make_chatcmd("Change this", "/tell <myname> limits priv maxplayers").")\n";

	$msg = Text::make_blob("Limits for privGroup and Tells", $blob);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^limits (priv|tell) faction$/i", $message, $arr)) {
 	$blob = "<header> :::::: Faction Limit :::::: <end>\n\n";
 	if ($arr[1] == "priv") {
 	 	if (Setting::get("priv_req_faction") == "all") {
			$blob .= "Current Setting: <highlight>No Limit<end>\n\n";
		} else {
			$blob .= "Current Setting: <highlight>" . Setting::get("priv_req_faction") . "<end>\n\n";
		}
 	} else {
 	 	if (Setting::get("tell_req_faction") == "all") {
			$blob .= "Current Setting: <highlight>No Limit<end>\n\n";
		} else {
			$blob .= "Current Setting: <highlight>" . Setting::get("tell_req_faction") . "<end>\n\n";
		}
	}
	$blob .= "Change it to:\n";
	$blob .= Text::make_chatcmd("No Faction Limit", "/tell <myname> limits {$arr[1]} faction all")."\n\n";	
	$blob .= Text::make_chatcmd("Omni only", "/tell <myname> limits {$arr[1]} faction omni")."\n";
	$blob .= Text::make_chatcmd("Clan only", "/tell <myname> limits {$arr[1]} faction clan")."\n";	
	$blob .= Text::make_chatcmd("Neutral only", "/tell <myname> limits {$arr[1]} faction neutral")."\n\n";
	$blob .= Text::make_chatcmd("Not Clan", "/tell <myname> limits {$arr[1]} faction not clan")."\n";
	$blob .= Text::make_chatcmd("Not Neutral", "/tell <myname> limits {$arr[1]} faction not neutral")."\n";
	$blob .= Text::make_chatcmd("Not Omni", "/tell <myname> limits {$arr[1]} faction not omni")."\n";
	$msg = Text::make_blob("Faction Limit", $blob);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^limits (priv|tell) faction (omni|clan|neutral|all)$/i", $message, $arr)) {
	$faction = ucfirst(strtolower($arr[2]));
	$channel = strtolower($arr[1]);
	
	if ($channel == "priv") {
		Setting::save("priv_req_faction", $faction);
	} else {
		Setting::save("tell_req_faction", $faction);
	}
	
	if ($channel == "priv" && $faction == "all") {
		$msg = "Faction limit removed from private channel invites.";
	} else if ($channel == "priv") {
		$msg = "Private channel Invites are accepted only from the Faction $faction.";
	} else if ($channel == "tell" && $faction == "all") {
		$msg = "Faction limit removed for tell responces.";
	} else if ($channel == "tell") {
 		$msg = "Responding on tells will be done only for players with the Faction $faction.";
 	}
 	$chatBot->send($msg, $sendto);
} else if (preg_match("/^limits (priv|tell) faction not (omni|clan|neutral)$/i", $message, $arr)) {
	$faction = ucfirst(strtolower($arr[2]));
	$channel = strtolower($arr[1]);
	
	if ($channel == "priv") {
		Setting::save("priv_req_faction", "not ".$faction);
	} else {
		Setting::save("tell_req_faction", "not ".$faction);
	}
	
	if ($channel == "priv") {
		$msg = "Private channel invites are accepted only from player that are not $faction.";
	} else if ($channel == "tell") {
 		$msg = "Responding on tells will be done for players that are not $faction.";
 	}
 	$chatBot->send($msg, $sendto);
} else if (preg_match("/^limits (priv|tell) minlvl$/i", $message, $arr)) {
 	$blob = "<header> :::::: Level Limit :::::: <end>\n\n";
 	if ($arr[1] == "priv") {
  	 	if (Setting::get("priv_req_lvl") == 0) {
			$blob .= "Current Setting: <highlight>No Limit<end>\n\n";
		} else {
			$blob .= "Current Setting: <highlight>" . Setting::get("priv_req_lvl") . "<end>\n\n";
		}
 	} else {
  	 	if (Setting::get("tell_req_lvl") == 0) {
			$blob .= "Current Setting: <highlight>No Limit<end>\n\n";
		} else {
			$blob .= "Current Setting: <highlight>" . Setting::get("tell_req_lvl") . "<end>\n\n";
		}
	}
	$blob .= "Change it to:\n";
	$blob .= Text::make_chatcmd("No Level limit", "/tell <myname> limits {$arr[1]} minlvl 0")."\n\n";	
	for ($i = 5; $i <= 220; $i += 5) {
		$blob .= Text::make_chatcmd("Level limit $i", "/tell <myname> limits {$arr[1]} minlvl $i")."\n";
	}

	$msg = Text::make_blob("Level Limit", $blob);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^limits (priv|tell) minlvl ([0-9]+)$/i", $message, $arr)) {
	$minlvl = strtolower($arr[2]);
	$channel = strtolower($arr[1]);
	
	if ($minlvl > 220 || $minlvl < 0) {
		$msg = "<red>Minimum Level can be only set between 1-220<end>";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	if ($channel == "priv") {
		Setting::save("priv_req_lvl", $minlvl);
	} else {
		Setting::save("tell_req_lvl", $minlvl);
	}
	
	if ($channel == "priv" && $minlvl == 0) {
		$msg = "Player min level limit has been removed from private channel invites.";
	} else if ($channel == "priv") {
		$msg = "Private channel Invites are accepted from the level $minlvl and above.";
	} else if ($channel == "tell" && $minlvl == 0) {
		$msg = "Player min level limit has been removed from responding on tells.";
	} else if ($channel == "tell") {
 		$msg = "Responding on tells will be done for the Minimumlevel of $minlvl.";
 	}
 	$chatBot->send($msg, $sendto);
} else if (preg_match("/^limits tell open$/i", $message)) {
 	$blob = "<header> :::::: General Limit :::::: <end>\n\n";
 	$blob .= "Current Setting: <highlight>";

	if (Setting::get("tell_req_open") == "all") {
		$blob .= "No general Limit";
	} else if (Setting::get("tell_req_open") == "org") {
		$blob .= "Responding only to Players that are in the Organistion <myguild>";
	} else {
		$blob .= "Responding only to players that are Members of this Bot";
	}

	$blob .= "<end>\n\nChange it to:\n";
	$blob .= Text::make_chatcmd("No General limit", "/tell <myname> limits tell open all")."\n\n";
	$blob .= Text::make_chatcmd("Only for Members of your Organisation", "/tell <myname> limits tell open org")."\n";
	$blob .= Text::make_chatcmd("Only for Members of the Bot", "/tell <myname> limits tell open members")."\n\n";

	$msg = Text::make_blob("General Limit", $blob);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^limits tell open (all|org|members)$/i", $message, $arr)) {
	$open = strtolower($arr[1]);
	
	Setting::save("tell_req_open", $open);
	
	if ($open == "all") {
		$msg = "General restriction for responding on tells has been removed.";
	} else if ($open == "org") {
 		$msg = "Responding on tells will be done only for Members of your Organisation.";
 	} else if ($open == "members") {
 		$msg = "Responding on tells will be done only for Members of this Bot.";
 	}
 	$chatBot->send($msg, $sendto);
} else if (preg_match("/^limits priv maxplayers$/i", $message, $arr)) {
 	$blob = "<header> :::::: Limit of Players in the Bot :::::: <end>\n\n";
	if (Setting::get("priv_req_maxplayers") == 0) {
		$blob .= "Current Setting: <highlight>No Limit<end>\n\n";
	} else {
		$blob .= "Current Setting: <highlight>" . Setting::get("priv_req_maxplayers") . "<end>\n\n";
	}

	$blob .= "Change it to:\n";
	$blob .= Text::make_chatcmd("No Limit of Players", "/tell <myname> limits priv maxplayers 0")."\n\n";	
	for ($i = 6; $i <= 120; $i += 6) {
		$blob .= Text::make_chatcmd("Set Maximum allowed Players in the Bot to $i", "/tell <myname> limits priv maxplayers $i")."\n";
	}

	$msg = Text::make_blob("Limit of Players in the Bot", $blob);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^limits priv maxplayers ([0-9]+)$/i", $message, $arr)) {
	$maxplayers = strtolower($arr[1]);
	
	Setting::save("priv_req_maxplayers", $maxplayers);
	
	if ($maxplayers == 0) {
		$msg = "The Limit of the Amount of players in the private channel has been removed.";
	} else {
		$msg = "The Limit of the Amount of players in the private channel has been set to $maxplayers.";
	} 
 	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>