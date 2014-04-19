<?php

namespace Budabot\User\Modules;

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'is', 
 *		accessLevel = 'member', 
 *		description = "Checks if a player is online", 
 *		help        = 'isonline.txt',
 *		alias		= 'lastseen'
 *	)
 */
class IsOnlineController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $db;

	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $buddylistManager;
	
	/** @Inject */
	public $altsController;
	
	/** @Inject */
	public $util;
	
	private $player = null;

	/**
	 * @HandlesCommand("is")
	 * @Matches("/^is (.+)$/i")
	 */
	public function isOnlineCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$uid = $this->chatBot->get_uid($name);
		if (!$uid) {
			$msg = "Character <highlight>$name<end> does not exist.";
			$sendto->reply($msg);
		} else {
			$online_status = $this->buddylistManager->is_online($name);
			if ($online_status === null) {
				$this->player['playername'] = $name;
				$this->player['sendto'] = $sendto;
				$this->buddylistManager->add($name, 'is_online');
			} else {
				$msg = "Character <highlight>$name<end> is ";
			
				if ($online_status) {
					$msg .= "<green>online<end>.";
				} else {
					$msg .= "<red>offline<end>.";
					
					$altInfo = $this->altsController->get_alt_info($name);
					$onlineAlts = $altInfo->get_online_alts();
					if (count($onlineAlts) > 0) {
						$msg .= " <green>Online<end> alts: " . implode(', ', $onlineAlts) . ".";
					} else {
						$msg .= $this->getLastLogoff($name, $altInfo);
					}
				}

				$sendto->reply($msg);
			}
		}
	}
	
	private function getLastLogoff($name, $altInfo) {
		$namesSql = '';
		forEach ($altInfo->get_all_alts() as $alt) {
			if ($namesSql) {
				$namesSql .= ", ";
			}
			$namesSql .= "'$alt'";
		}
		$row = $this->db->queryRow("SELECT * FROM org_members_<myname> WHERE `name` IN ($namesSql) AND `mode` != 'del' ORDER BY logged_off DESC");

		if ($row !== null) {
			if ($row->logged_off == 0) {
				return " <highlight>$name<end> has never logged on.";
			} else {
				return " Last seen at " . $this->util->date($row->logged_off) . " on <highlight>" . $row->name . "<end>.";
			}
		} else {
			return '';
		}
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Gets online status of player")
	 */
	public function logonEvent($eventObj) {
		$sender = $eventObj->sender;
		if ($this->player !== null && $sender == $this->player['playername']) {
			$status = "<green>online<end>";
			$msg = "Character <highlight>$sender<end> is $status.";
			$this->player['sendto']->reply($msg);
			$this->buddylistManager->remove($sender, 'is_online');
			$this->player = null;
		}
	}
	
	/**
	 * @Event("logOff")
	 * @Description("Gets offline status of player")
	 */
	public function logoffEvent($eventObj) {
		$sender = $eventObj->sender;
		if ($this->player !== null && $sender == $this->player['playername']) {
			$status = "<red>offline<end>";
			$msg = "Character <highlight>$sender<end> is $status.";
			$this->player['sendto']->reply($msg);
			$this->buddylistManager->remove($sender, 'is_online');
			$this->player = null;
		}
	}
}

