<?php

if (preg_match("/^about$/i", $message)) {
	// nothing to do
	return;
} else if (Whitelist::check($sender) || AccessLevel::check_access($sender, "member") || $sender == ucfirst(strtolower(Setting::get("relaybot")))) {
	// nothing to do
	return;
} else {
	if (!AccessLevel::check_access($sender, Setting::get("tell_req_open"))) {
	  	$msg = "<orange>Error! I am only responding to members.<end>";
	  	$chatBot->send($msg, $sender);
	  	$restricted = true;
	  	return;
	}
	
	//Get character info if minlvl or faction is set
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