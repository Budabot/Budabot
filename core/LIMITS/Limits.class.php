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
	public $setting;
	
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
		$this->setting->add($this->moduleName, "tell_req_lvl", "Tells Min Level", "edit", "number", "0", "0;10;50;100;150;190;205;215", "", "mod", "tell_req_lvl.txt");
		$this->setting->add($this->moduleName, "tell_req_faction", "Tell Faction Limit", "edit", "options", "all", "all;Omni;Neutral;Clan;not Omni;not Neutral;not Clan", "", "mod", "tell_req_faction.txt");
		$this->setting->add($this->moduleName, "tell_req_open", "Tell General Limit", "edit", "options", "all", "all;member;guild;rl;mod", "", "mod", "tell_req_open.txt");
	}

	public function check($sender, $message) {
		if (preg_match("/^about$/i", $message)) {
			return true;
		} else if ($this->whitelist->check($sender) || $sender == ucfirst(strtolower($this->setting->get("relaybot")))) {
			return true;
		} else if ($this->accessLevel->checkAccess($sender, 'mod')) {
			// if mod or higher, grant access automatically
			return true;
		} else {
			// if neither minlvl, faction, or access level is set, then check passes
			if ($this->setting->get("tell_req_lvl") == 0 &&
					$this->setting->get("tell_req_faction") == "all" &&
					$this->setting->get("tell_req_open") == "all") {

				return true;
			}
			
			// check access level
			if (!$this->accessLevel->checkAccess($sender, $this->setting->get("tell_req_open"))) {
				$msg = "<orange>Error! You must have an access level of at least '" . $this->setting->get("tell_req_open") . "' to send a tell to this bot.<end>";
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
			if ($this->setting->get("tell_req_lvl") != 0 && $this->setting->get("tell_req_lvl") > $whois->level) {
				$msg = "<orange>Error! You must be higher than level " . $this->setting->get("tell_req_lvl") . " to send a tell to this bot.<end>";
				$this->chatBot->sendTell($msg, $sender);
				return false;
			}

			// check faction limit
			if (($this->setting->get("tell_req_faction") == "Omni" || $this->setting->get("tell_req_faction") == "Clan" || $this->setting->get("tell_req_faction") == "Neutral") && $this->setting->get("tell_req_faction") != $whois->faction) {
				$msg = "<orange>Error! You must be " . $this->setting->get("tell_req_faction") . " to send a tell to this bot.<end>";
				$this->chatBot->sendTell($msg, $sender);
				return false;
			} else if ($this->setting->get("tell_req_faction") == "not Omni" || $this->setting->get("tell_req_faction") == "not Clan" || $this->setting->get("tell_req_faction") == "not Neutral") {
				$tmp = explode(" ", $this->setting->get("tell_req_faction"));
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
