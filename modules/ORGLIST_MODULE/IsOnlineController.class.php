<?php
/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'is', 
 *		accessLevel = 'all', 
 *		description = "Checks if a player is online", 
 *		help        = 'isonline.txt'
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
			//if the player is a buddy then
			$online_status = $this->buddylistManager->is_online($name);
			if ($online_status === null) {
				$this->player['playername'] = $name;
				$this->player['sendto'] = $sendto;
				$this->buddylistManager->add($name, 'is_online');
			} else {
				$row = $this->db->queryRow("SELECT * FROM org_members_<myname> WHERE `name` = ?", $name);
				if ($row !== null) {
					if ($row->logged_off != "0") {
						$logged_off = " Logged off at " . $this->util->date($row->logged_off);
					}
				}
				if ($online_status) {
					$status = "<green>online<end>";
				} else {
					$status = "<red>offline<end>".$logged_off;
				}
				$msg = "Character <highlight>$name<end> is $status.";
				$sendto->reply($msg);
			}
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

