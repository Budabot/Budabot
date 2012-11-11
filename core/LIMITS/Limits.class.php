<?php
/**
 * Authors:
 *  - Tyrence (RK2)
 *
 * @Instance
 */
class Limits {
	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $settingManager;
	
	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $accessLevel;

	/** @Inject */
	public $playerManager;

	/** @Inject */
	public $whitelist;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->settingManager->add($this->moduleName, "tell_req_lvl", "Minimum level required to send tell to bot", "edit", "number", "0", "0;10;50;100;150;190;205;215");
		$this->settingManager->add($this->moduleName, "tell_req_faction", "Faction required to send tell to bot", "edit", "options", "all", "all;Omni;Neutral;Clan;not Omni;not Neutral;not Clan");
		$this->settingManager->add($this->moduleName, "tell_req_open", "General requirements to send tell to bot", "edit", "options", "all", "all;member;guild;rl;mod");
	}

	public function check($sender, $message) {
		if (preg_match("/^about$/i", $message)) {
			return true;
		} else if ($this->whitelist->check($sender) || $sender == ucfirst(strtolower($this->settingManager->get("relaybot")))) {
			return true;
		} else if ($this->accessLevel->checkAccess($sender, 'mod')) {
			// if mod or higher, grant access automatically
			return true;
		} else {
			// if neither minlvl, faction, or access level is set, then check passes
			if ($this->settingManager->get("tell_req_lvl") == 0 &&
					$this->settingManager->get("tell_req_faction") == "all" &&
					$this->settingManager->get("tell_req_open") == "all") {

				return true;
			}
			
			// check access level
			if (!$this->accessLevel->checkAccess($sender, $this->settingManager->get("tell_req_open"))) {
				$msg = "<orange>Error! You must have an access level of at least '" . $this->settingManager->get("tell_req_open") . "' to send a tell to this bot.<end>";
				$this->chatBot->sendTell($msg, $sender);
				return false;
			}

			// get player info which is needed for following checks
			$whois = $this->playerManager->get_by_name($sender);
			if ($whois === null) {
				$msg = "<orange>Error! Unable to get your character info. Please try again later.<end>";
				$this->chatBot->sendTell($msg, $sender);
				return false;
			}

			// check minlvl
			if ($this->settingManager->get("tell_req_lvl") != 0 && $this->settingManager->get("tell_req_lvl") > $whois->level) {
				$msg = "<orange>Error! You must be higher than level " . $this->settingManager->get("tell_req_lvl") . " to send a tell to this bot.<end>";
				$this->chatBot->sendTell($msg, $sender);
				return false;
			}

			// check faction limit
			if (($this->settingManager->get("tell_req_faction") == "Omni" || $this->settingManager->get("tell_req_faction") == "Clan" || $this->settingManager->get("tell_req_faction") == "Neutral") && $this->settingManager->get("tell_req_faction") != $whois->faction) {
				$msg = "<orange>Error! You must be " . $this->settingManager->get("tell_req_faction") . " to send a tell to this bot.<end>";
				$this->chatBot->sendTell($msg, $sender);
				return false;
			} else if ($this->settingManager->get("tell_req_faction") == "not Omni" || $this->settingManager->get("tell_req_faction") == "not Clan" || $this->settingManager->get("tell_req_faction") == "not Neutral") {
				$tmp = explode(" ", $this->settingManager->get("tell_req_faction"));
				if ($tmp[1] == $whois->faction) {
					$msg = "<orange>Error! You must not be {$tmp[1]} to send a tell to this bot.<end>";
					$this->chatBot->sendTell($msg, $sender);
					return false;
				}
			}
			return true;
		}
	}
}

?>
