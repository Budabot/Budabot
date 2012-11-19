<?php

/**
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command       = 'adminlist',
 *		accessLevel   = 'all',
 *		description   = 'Shows the list of administrators and moderators',
 *		help          = 'admin.txt',
 *		alias         = 'admins',
 *		defaultStatus = '1'
 *	)
 *	@DefineCommand(
 *		command       = 'addadmin',
 *		accessLevel   = 'superadmin',
 *		description   = 'Add an administrator',
 *		help          = 'admin.txt',
 *		defaultStatus = '1'
 *	)
 *	@DefineCommand(
 *		command       = 'remadmin',
 *		accessLevel   = 'superadmin',
 *		description   = 'Remove an administrator',
 *		help          = 'admin.txt',
 *		defaultStatus = '1'
 *	)
 *	@DefineCommand(
 *		command       = 'addmod',
 *		accessLevel   = 'admin',
 *		description   = 'Add a moderator',
 *		help          = 'admin.txt',
 *		defaultStatus = '1'
 *	)
 *	@DefineCommand(
 *		command       = 'remmod',
 *		accessLevel   = 'admin',
 *		description   = 'Remove a moderator',
 *		help          = 'admin.txt',
 *		defaultStatus = '1'
 *	)
 */
class AdminController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $adminManager;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $buddylistManager;
	
	/** @Inject */
	public $accessManager;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $settingManager;
	
	/** @Inject */
	public $alts;

	/**
	 * @Setup
	 * This handler is called on bot startup.
	 */
	public function setup() {
		$className = get_class($this);
		
		$this->adminManager->uploadAdmins();
	}
	
	/**
	 * @HandlesCommand("addadmin")
	 * @Matches("/^addadmin (.+)$/i")
	 */
	public function addAdminCommand($message, $channel, $sender, $sendto, $args) {
		$who = ucfirst(strtolower($args[1]));
		$intlevel = 4;
		$rank = 'an administrator';

		$this->add($who, $sender, $sendto, $intlevel, $rank);
	}
	
	/**
	 * @HandlesCommand("addmod")
	 * @Matches("/^addmod (.+)$/i")
	 */
	public function addModeratorCommand($message, $channel, $sender, $sendto, $args) {
		$who = ucfirst(strtolower($args[1]));
		$intlevel = 3;
		$rank = 'a moderator';

		$this->add($who, $sender, $sendto, $intlevel, $rank);
	}
	
	/**
	 * @HandlesCommand("remadmin")
	 * @Matches("/^remadmin (.+)$/i")
	 */
	public function removeAdminCommand($message, $channel, $sender, $sendto, $args) {
		$who = ucfirst(strtolower($args[1]));
		$intlevel = 4;
		$rank = 'an administrator';

		$this->remove($who, $sender, $sendto, $intlevel, $rank);
	}
	
	/**
	 * @HandlesCommand("remmod")
	 * @Matches("/^remmod (.+)$/i")
	 */
	public function removeModeratorCommand($message, $channel, $sender, $sendto, $args) {
		$who = ucfirst(strtolower($args[1]));
		$intlevel = 3;
		$rank = 'a moderator';

		$this->remove($who, $sender, $sendto, $intlevel, $rank);
	}
	
	/**
	 * @HandlesCommand("adminlist")
	 * @Matches("/^adminlist$/i")
	 */
	public function adminlistCommand($message, $channel, $sender, $sendto) {
		if (!preg_match("/^adminlist$/i", $message)) {
			return false;
		}

		$blob .= "<header2>Administrators<end>\n";
		forEach ($this->adminManager->admins as $who => $data) {
			if ($this->adminManager->admins[$who]["level"] == 4) {
				if ($who != "") {
					$blob .= "<tab>$who ";

					if ($this->accessManager->checkAccess($who, 'superadmin')) {
						$blob .= "(<orange>Super-administrator<end>) ";
					}

					if ($this->buddylistManager->is_online($who) == 1 && isset($this->chatBot->chatlist[$who])) {
						$blob .= "(<green>Online and in chat<end>)";
					} else if ($this->buddylistManager->is_online($who) == 1) {
						$blob .= "(<green>Online<end>)";
					} else {
						$blob .= "(<red>Offline<end>)";
					}

					$blob.= "\n";
				}
			}
		}

		$blob .= "<header2>Moderators<end>\n";
		forEach ($this->adminManager->admins as $who => $data){
			if ($this->adminManager->admins[$who]["level"] == 3){
				if ($who != "") {
					$blob .= "<tab>$who ";
					if ($this->buddylistManager->is_online($who) == 1 && isset($this->chatBot->chatlist[$who])) {
						$blob .= "(<green>Online and in chat<end>)";
					} else if ($this->buddylistManager->is_online($who) == 1) {
						$blob .= "(<green>Online<end>)";
					} else {
						$blob .= "(<red>Offline<end>)";
					}
					$blob.= "\n";
				}
			}
		}

		$link = $this->text->make_blob('Bot administrators', $blob);
		$sendto->reply($link);
	}
	
	/**
	 * @Event("connect")
	 * @Description("Add administrators and moderators to the buddy list")
	 * @DefaultStatus("1")
	 */
	public function checkAdminsEvent($eventObj) {
		$data = $this->db->query("SELECT * FROM admin_<myname>");
		forEach ($data as $row) {
			$this->buddylistManager->add($row->name, 'admin');
		}
	}
	
	public function add($who, $sender, $sendto, $intlevel, $rank) {
		if ($this->chatBot->get_uid($who) == NULL){
			$sendto->reply("Character <highlight>$who<end> does not exist.");
			return;
		}

		if ($this->adminManager->checkExisting($who, $intlevel)) {
			$sendto->reply("<highlight>$who<end> is already $rank.");
			return;
		}

		if (!$this->checkAccessLevel($sender, $who, $sendto)) {
			$sendto->reply("You must have a higher access level than <highlight>$who<end> in order to change his access level.");
			return;
		}

		if (!$this->checkAltsInheritAdmin($who)) {
			$msg = "<red>WARNING<end>: alts_inherit_admin is enabled, but $who is not a main.  This command did NOT affect $who's access level.";
			$sendto->reply($msg);
			return;
		}

		$action = $this->adminManager->addToLists($who, $intlevel);

		$sendto->reply("<highlight>$who<end> has been $action to $rank.");
		$this->chatBot->sendTell("You have been $action to $rank by <highlight>$sender<end>.", $who);
	}
	
	public function remove($who, $sender, $sendto, $intlevel, $rank) {
		if (!$this->adminManager->checkExisting($who, $intlevel)) {
			$sendto->reply("<highlight>$who<end> is not $rank.");
			return;
		}

		if (!$this->checkAccessLevel($sender, $who, $sendto)) {
			$sendto->reply("You must have a higher access level than <highlight>$who<end> in order to change his access level.");
			return;
		}

		$this->adminManager->removeFromLists($who);

		if (!$this->checkAltsInheritAdmin($who)) {
			$msg = "<red>WARNING<end>: alts_inherit_admin is enabled, but $who is not a main.  This command did NOT affect $who's access level.";
			$sendto->reply($msg);
		}

		$sendto->reply("<highlight>$who<end> has been removed as $rank.");
		$this->chatBot->sendTell("You have been removed as $rank by <highlight>$sender<end>.", $who);
	}
	
	public function checkAltsInheritAdmin($who) {
		$ai = $this->alts->get_alt_info($who);
		if ($this->settingManager->get("alts_inherit_admin") == 1 && $ai->main != $who) {
			return false;
		} else {
			return true;
		}
	}
	
	public function checkAccessLevel($actor, $actee) {
		$senderAccessLevel = $this->accessManager->getAccessLevelForCharacter($actor);
		$whoAccessLevel = $this->accessManager->getSingleAccessLevel($actee);
		if ($this->accessManager->compareAccessLevels($whoAccessLevel, $senderAccessLevel) >= 0) {
			return false;
		} else {
			return true;
		}
	}
}
