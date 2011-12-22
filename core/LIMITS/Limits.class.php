<?php

class Limits {
	public static function check($sender, $message) {
		global $chatBot;

		if (preg_match("/^about$/i", $message)) {
			return true;
		} else if (Whitelist::check($sender) || AccessLevel::check_access($sender, Setting::get("tell_req_open")) || $sender == ucfirst(strtolower(Setting::get("relaybot")))) {
			return true;
		} else {
			// if neither minlvl or faction is set, then check passes
			if (Setting::get("tell_req_lvl") == 0 && Setting::get("tell_req_faction") == "all") {
				return true;
			}

			// get player info which is needed for following checks
			$whois = Player::get_by_name($sender);
			if ($whois === null) {
				$msg = "<orange>Error! Unable to get your character info. Please try again later.<end>";
				$chatBot->send($msg, $sender);
				return false;
			}

			// check minlvl
			if (Setting::get("tell_req_lvl") != 0 && Setting::get("tell_req_lvl") > $whois->level) {
				$msg = "<orange>Error! You must be higher than level " . Setting::get("tell_req_lvl") . " to send a tell to this bot.<end>";
				$chatBot->send($msg, $sender);
				return false;
			}

			// check faction limit
			if ((Setting::get("tell_req_faction") == "Omni" || Setting::get("tell_req_faction") == "Clan" || Setting::get("tell_req_faction") == "Neutral") && Setting::get("tell_req_faction") != $whois->faction) {
				$msg = "<orange>Error! You must be " . Setting::get("tell_req_faction") . " to send a tell to this bot.<end>";
				$chatBot->send($msg, $sender);
				return false;
			} else if (Setting::get("tell_req_faction") == "not Omni" || Setting::get("tell_req_faction") == "not Clan" || Setting::get("tell_req_faction") == "not Neutral") {
				$tmp = explode(" ", Setting::get("tell_req_faction"));
				if ($tmp[1] == $whois->faction) {
					$msg = "<orange>Error! You must not be {$tmp[1]} to send a tell to this bot.<end>";
					$chatBot->send($msg, $sender);
					return false;
				}
			}
			return true;
		}
	}
}

?>
