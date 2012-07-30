<?php

class Limits {

	/** @Inject */
	public $setting;

	/** @Inject */
	public $accessLevel;

	/** @Inject */
	public $player;

	/** @Inject */
	public $whitelist;

	public function check($sender, $message) {
		$chatBot = Registry::getInstance('chatBot');

		if (preg_match("/^about$/i", $message)) {
			return true;
		} else if ($this->whitelist->check($sender) || $sender == ucfirst(strtolower($this->setting->get("relaybot")))) {
			return true;
		} else {
			// if neither minlvl or faction is set, then check passes
			if ($this->setting->get("tell_req_lvl") == 0 &&
					$this->setting->get("tell_req_faction") == "all" &&
					$this->setting->get("tell_req_open") == "all") {

				return true;
			}
			
			// check access level
			if (!$this->accessLevel->checkAccess($sender, $this->setting->get("tell_req_open"))) {
				$msg = "<orange>Error! You must have an access level of at least '" . $this->setting->get("tell_req_open") . "' to send a tell to this bot.<end>";
				$chatBot->sendTell($msg, $sender);
				return false;
			}

			// get player info which is needed for following checks
			$whois = $this->player->get_by_name($sender);
			if ($whois === null) {
				$msg = "<orange>Error! Unable to get your character info. Please try again later.<end>";
				$chatBot->sendTell($msg, $sender);
				return false;
			}

			// check minlvl
			if ($this->setting->get("tell_req_lvl") != 0 && $this->setting->get("tell_req_lvl") > $whois->level) {
				$msg = "<orange>Error! You must be higher than level " . $this->setting->get("tell_req_lvl") . " to send a tell to this bot.<end>";
				$chatBot->sendTell($msg, $sender);
				return false;
			}

			// check faction limit
			if (($this->setting->get("tell_req_faction") == "Omni" || $this->setting->get("tell_req_faction") == "Clan" || $this->setting->get("tell_req_faction") == "Neutral") && $this->setting->get("tell_req_faction") != $whois->faction) {
				$msg = "<orange>Error! You must be " . $this->setting->get("tell_req_faction") . " to send a tell to this bot.<end>";
				$chatBot->sendTell($msg, $sender);
				return false;
			} else if ($this->setting->get("tell_req_faction") == "not Omni" || $this->setting->get("tell_req_faction") == "not Clan" || $this->setting->get("tell_req_faction") == "not Neutral") {
				$tmp = explode(" ", $this->setting->get("tell_req_faction"));
				if ($tmp[1] == $whois->faction) {
					$msg = "<orange>Error! You must not be {$tmp[1]} to send a tell to this bot.<end>";
					$chatBot->sendTell($msg, $sender);
					return false;
				}
			}
			return true;
		}
	}
}

?>
