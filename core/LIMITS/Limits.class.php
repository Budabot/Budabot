<?php

namespace Budabot\Core;

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
	public $accessManager;

	/** @Inject */
	public $playerManager;
	
	/** @Inject */
	public $playerHistoryManager;
	
	/** @Inject */
	public $util;

	/** @Inject */
	public $whitelist;
	
	/** @Logger */
	public $logger;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->settingManager->add($this->moduleName, "tell_req_lvl", "Minimum level required to send tell to bot", "edit", "number", "0", "0;10;50;100;150;190;205;215");
		$this->settingManager->add($this->moduleName, "tell_req_faction", "Faction required to send tell to bot", "edit", "options", "all", "all;Omni;Neutral;Clan;not Omni;not Neutral;not Clan");
		$this->settingManager->add($this->moduleName, "tell_min_player_age", "Minimum age of player to send tell to bot", "edit", "time", "1s", "1s;7days;14days;1month;2months;6months;1year;2years", '', 'mod', 'limits.txt');
		$this->settingManager->add($this->moduleName, "tell_error_msg_type", "How to show error messages when limit requirements are not met", "edit", "options", "2", "Specific;Generic;None", "2;1;0");
	}
	
	public function check($sender, $message) {
		if (preg_match("/^about$/i", $message)) {
			return true;
		} else if ($this->whitelist->check($sender) || $sender == ucfirst(strtolower($this->settingManager->get("relaybot")))) {
			return true;
		} else if ($this->accessManager->checkAccess($sender, 'mod')) {
			// if mod or higher, grant access automatically
			return true;
		}
	
		$msg = $this->runChecks($sender);
		if ($msg === true) {
			return true;
		} else {
			$this->logger->log('Info', "$sender denied access to bot due to: $msg");
		
			if ($this->settingManager->get('tell_error_msg_type') == 2) {
				$this->chatBot->sendTell($msg, $sender);
			} else if ($this->settingManager->get('tell_error_msg_type') == 1) {
				$msg = "Error! You do not have access to this bot.";
				$this->chatBot->sendTell($msg, $sender);
			} else {
				// else do not send a message
			}
			return false;
		}
	}

	public function runChecks($sender) {
		// if access level is at least member, skip checks
		if ($this->accessManager->checkAccess($sender, 'member') {
			return true;
		}

		if ($this->settingManager->get("tell_req_lvl") != 0 || $this->settingManager->get("tell_req_faction") != "all") {
			// get player info which is needed for following checks
			$whois = $this->playerManager->get_by_name($sender);
			if ($whois === null) {
				return "Error! Unable to get your character info for limit checks. Please try again later.";
			}

			// check minlvl
			if ($this->settingManager->get("tell_req_lvl") != 0 && $this->settingManager->get("tell_req_lvl") > $whois->level) {
				return "Error! You must be at least level <highlight>" . $this->settingManager->get("tell_req_lvl") . "<end>.";
			}

			// check faction limit
			if (($this->settingManager->get("tell_req_faction") == "Omni" || $this->settingManager->get("tell_req_faction") == "Clan" || $this->settingManager->get("tell_req_faction") == "Neutral") && $this->settingManager->get("tell_req_faction") != $whois->faction) {
				return "Error! You must be <highlight>" . $this->settingManager->get("tell_req_faction") . "<end>.";
			} else if ($this->settingManager->get("tell_req_faction") == "not Omni" || $this->settingManager->get("tell_req_faction") == "not Clan" || $this->settingManager->get("tell_req_faction") == "not Neutral") {
				$tmp = explode(" ", $this->settingManager->get("tell_req_faction"));
				if ($tmp[1] == $whois->faction) {
					return "Error! You must not be <highlight>{$tmp[1]}<end>.";
				}
			}
		}
		
		// check player age
		if ($this->settingManager->get("tell_min_player_age") > 1) {
			$history = $this->playerHistoryManager->lookup($sender, $this->chatBot->vars['dimension']);
			if ($history === null) {
				return "Error! Unable to get your character history for limit checks. Please try again later.";
			} else {
				$minAge = time() - $this->settingManager->get("tell_min_player_age");
				$entry = array_pop($history->data);
				// TODO check for rename

				if ($entry->last_changed > $minAge) {
					$timeString = $this->util->unixtime_to_readable($this->settingManager->get("tell_min_player_age"));
					return "Error! You must be at least <highlight>$timeString<end> old.";
				}
			}
		}
		
		return true;
	}
}

?>
