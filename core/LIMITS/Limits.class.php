<?php

class Limits {
	public static function check($sender, $message) {
		global $chatBot;
		$accessLevel = $chatBot->getInstance('accessLevel');
		$setting = $chatBot->getInstance('setting');

		if (preg_match("/^about$/i", $message)) {
			return true;
		} else if (Whitelist::check($sender) || $accessLevel->checkAccess($sender, $setting->get("tell_req_open")) || $sender == ucfirst(strtolower($setting->get("relaybot")))) {
			return true;
		} else {
			// if neither minlvl or faction is set, then check passes
			if ($setting->get("tell_req_lvl") == 0 && $setting->get("tell_req_faction") == "all") {
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
			if ($setting->get("tell_req_lvl") != 0 && $setting->get("tell_req_lvl") > $whois->level) {
				$msg = "<orange>Error! You must be higher than level " . $setting->get("tell_req_lvl") . " to send a tell to this bot.<end>";
				$chatBot->send($msg, $sender);
				return false;
			}

			// check faction limit
			if (($setting->get("tell_req_faction") == "Omni" || $setting->get("tell_req_faction") == "Clan" || $setting->get("tell_req_faction") == "Neutral") && $setting->get("tell_req_faction") != $whois->faction) {
				$msg = "<orange>Error! You must be " . $setting->get("tell_req_faction") . " to send a tell to this bot.<end>";
				$chatBot->send($msg, $sender);
				return false;
			} else if ($setting->get("tell_req_faction") == "not Omni" || $setting->get("tell_req_faction") == "not Clan" || $setting->get("tell_req_faction") == "not Neutral") {
				$tmp = explode(" ", $setting->get("tell_req_faction"));
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
