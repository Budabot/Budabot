<?php

if (preg_match("/^about$/i", $message)) {
	// nothing to do
	return;
} else if (Whitelist::check($sender) || AccessLevel::check_access($sender, "raidleader") || $sender == ucfirst(strtolower(Setting::get("relaybot")))) {
	// nothing to do
	return;
} else if (preg_match("/^join$/i", $message)) {
	if (AccessLevel::check_access($sender, 'member')) {
		return;
	}
	
	//Get character info if minlvl or faction is set
	if (Setting::get("priv_req_lvl") != 0 || Setting::get("priv_req_faction") != "all") {
		$whois = Player::get_by_name($sender);
	   	if ($whois === null) {
		    $msg = "<orange>Error! Unable to get your character info. Please try again later.<end>";
		    $chatBot->send($msg, $sender);
		  	$restricted = true;
		    return;
		}
	}
	
	//Check the Minlvl
	if (Setting::get("priv_req_lvl") != 0 && Setting::get("priv_req_lvl") > $whois->level) {
	  	$msg = "<orange>Error! You need to be at least level " . Setting::get("priv_req_lvl") . " to join this bot.<end>";
	    $chatBot->send($msg, $sender);
	  	$restricted = true;
	    return;
	}
	
	//Check the Faction Limit
	if ((Setting::get("priv_req_faction") == "Omni" || Setting::get("priv_req_faction") == "Clan" || Setting::get("priv_req_faction") == "Neutral") && Setting::get("priv_req_faction") != $whois->faction) {
	  	$msg = "<orange>Error! Only characters who are " . Setting::get("priv_req_faction") . " can join this bot.<end>";
	    $chatBot->send($msg, $sender);
	  	$restricted = true;
	    return;
	} else if (Setting::get("priv_req_faction") == "not Omni" || Setting::get("priv_req_faction") == "not Clan" || Setting::get("priv_req_faction") == "not Neutral") {
		$tmp = explode(" ", Setting::get("priv_req_faction"));
		if ($tmp[1] == $whois->faction) {
			$msg = "<orange>Error! Only characters who are " . Setting::get("priv_req_faction") . " can join this bot.<end>";
		    $chatBot->send($msg, $sender);
		  	$restricted = true;
		    return;
		}
	}

	//Check the Maximum Limit for the Private Channel
	if (Setting::get("priv_req_maxplayers") != 0 && count($chatBot->chatlist) >= Setting::get("priv_req_maxplayers")) {
	  	$msg = "<orange>Error! There are already a maximum number of " . Setting::get("priv_req_maxplayers") . " characters in the bot.<end>";
	    $chatBot->send($msg, $sender);
	  	$restricted = true;
	    return;
	}
} else {
	if (Setting::get("tell_req_open") == "members") {
		//Chek if he is a member of the Bot
	  	$db->query("SELECT * FROM members_<myname> WHERE `name` = '$sender'");
		if ($db->numrows() == 0) {
		  	$msg = "<orange>Error! I am only responding to members of this bot!<end>.";
		  	$chatBot->send($msg, $sender);
  		  	$restricted = true;
		  	return;
		}
	} else if (Setting::get("tell_req_open") == "org" && !isset($chatBot->guildmembers[$sender])) {
		//Check if he is a org Member
	  	$msg = "<orange>Error! I am only responding to members of the org <myguild>.<end>";
	  	$chatBot->send($msg, $sender);
	  	$restricted = true;
	  	return;
	}
	
	//Get his character infos if minlvl or faction is set
	if (Setting::get("tell_req_lvl") != 0 || Setting::get("tell_req_faction") != "all") {
		$whois = Player::get_by_name($sender);
	   	if ($whois === null) {
		    $msg = "<orange>Error! Unable to get your character info. Please try again later.<end>";
		    $chatBot->send($msg, $sender);
		  	$restricted = true;
		    return;
		}
	}
	
	//Check the Minlvl
	if (Setting::get("tell_req_lvl") != 0 && Setting::get("tell_req_lvl") > $whois->level) {
	  	$msg = "<orange>Error! You must be higher than level " . Setting::get("tell_req_lvl") . " to send a tell to this bot.<end>";
	    $chatBot->send($msg, $sender);
   	  	$restricted = true;
	    return;
	}
	
	//Check the Faction Limit
	if ((Setting::get("tell_req_faction") == "Omni" || Setting::get("tell_req_faction") == "Clan" || Setting::get("tell_req_faction") == "Neutral") && Setting::get("tell_req_faction") != $whois->faction) {
	  	$msg = "<orange>Error! You must be " . Setting::get("tell_req_faction") . " to send a tell to this bot.<end>";
	    $chatBot->send($msg, $sender);
	  	$restricted = true;
	    return;
	} else if (Setting::get("tell_req_faction") == "not Omni" || Setting::get("tell_req_faction") == "not Clan" || Setting::get("tell_req_faction") == "not Neutral") {
		$tmp = explode(" ", Setting::get("tell_req_faction"));
		if ($tmp[1] == $whois->faction) {
			$msg = "<orange>Error! You must not be {$tmp[1]} to send a tell to this bot.<end>";
		    $chatBot->send($msg, $sender);
    	  	$restricted = true;
		    return;
		}
	}
}
?>