<?php

namespace budabot\user\modules;

use \budabot\core\AutoInject;

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'accept', 
 *		accessLevel = 'mod', 
 *		description = "Accept a private channel invitation from another player", 
 *		help        = 'accept.txt'
 *	)
 */
class ExternalPrivateChannelController extends AutoInject {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->settingManager->add($this->moduleName, "auto_accept_list", "List of players to accept private channel invites from", "noedit", "text", "");
	}

	/**
	 * @HandlesCommand("accept")
	 * @Matches("/^accept (.+)/i")
	 */
	public function acceptCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		if (!$this->chatBot->get_uid($name)) {
			$msg = "Character <highlight>$name<end> does not exist.";
		} else {
			$this->chatBot->privategroup_join($name);
			$msg = "Accepted private channel invitation from <highlight>$name<end>.";
		}
		$sendto->reply($msg);
	}

	/**
	 * @Event("extjoinprivrequest")
	 * @Description("Accept private channel invites from players on the auto accept list")
	 */
	public function privateChannelJoinRequestEvent($eventObj) {
		$players = explode(";", $this->setting->auto_accept_list);
		if (in_array($eventObj->sender, $players)) {
			$this->chatBot->privategroup_join($eventObj->sender);
		}
	}
	
	public function addPlayerToAutoAcceptList($name) {
		$name = ucfirst(strtolower($name));
		$players = explode(";", $this->setting->auto_accept_list);
		$index = array_search($eventObj->sender, $players);
		if ($index === false) {
			$players []= $eventObj->sender;
			$this->setting->auto_accept_list = implode(";", $players);
		}
	}
	
	public function removePlayerFromAutoAcceptList($name) {
		$name = ucfirst(strtolower($name));
		$players = explode(";", $this->setting->auto_accept_list);
		$index = array_search($eventObj->sender, $players);
		if ($index !== false) {
			unset($players[$index]);
			$this->setting->auto_accept_list = implode(";", $players);
		}
	}
}

