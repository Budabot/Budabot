<?php

namespace Budabot\Core\Modules;

/**
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command       = 'adminlist',
 *		accessLevel   = 'all',
 *		description   = 'Shows the list of administrators and moderators',
 *		help          = 'adminlist.txt',
 *		alias         = 'admins',
 *		defaultStatus = '1'
 *	)
 *	@DefineCommand(
 *		command       = 'admin',
 *		accessLevel   = 'superadmin',
 *		description   = 'Add or remove an administrator',
 *		help          = 'admin.txt',
 *		defaultStatus = '1'
 *	)
 *	@DefineCommand(
 *		command       = 'mod',
 *		accessLevel   = 'admin',
 *		description   = 'Add or remove a moderator',
 *		help          = 'mod.txt',
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
	public $altsController;

	/**
	 * @Setup
	 * This handler is called on bot startup.
	 */
	public function setup() {
		$this->adminManager->uploadAdmins();

		$this->commandAlias->register($this->moduleName, "admin add", "addadmin");
		$this->commandAlias->register($this->moduleName, "admin rem", "remadmin");
		$this->commandAlias->register($this->moduleName, "mod add", "addmod");
		$this->commandAlias->register($this->moduleName, "mod rem", "remmod");
	}
	
	/**
	 * @HandlesCommand("admin")
	 * @Matches("/^admin add (.+)$/i")
	 */
	public function adminAddCommand($message, $channel, $sender, $sendto, $args) {
		$who = ucfirst(strtolower($args[1]));
		$intlevel = 4;
		$rank = 'an administrator';

		$this->add($who, $sender, $sendto, $intlevel, $rank);
	}
	
	/**
	 * @HandlesCommand("mod")
	 * @Matches("/^mod add (.+)$/i")
	 */
	public function modAddCommand($message, $channel, $sender, $sendto, $args) {
		$who = ucfirst(strtolower($args[1]));
		$intlevel = 3;
		$rank = 'a moderator';

		$this->add($who, $sender, $sendto, $intlevel, $rank);
	}
	
	/**
	 * @HandlesCommand("admin")
	 * @Matches("/^admin rem (.+)$/i")
	 */
	public function adminRemoveCommand($message, $channel, $sender, $sendto, $args) {
		$who = ucfirst(strtolower($args[1]));
		$intlevel = 4;
		$rank = 'an administrator';

		$this->remove($who, $sender, $sendto, $intlevel, $rank);
	}
	
	/**
	 * @HandlesCommand("mod")
	 * @Matches("/^mod rem (.+)$/i")
	 */
	public function modRemoveCommand($message, $channel, $sender, $sendto, $args) {
		$who = ucfirst(strtolower($args[1]));
		$intlevel = 3;
		$rank = 'a moderator';

		$this->remove($who, $sender, $sendto, $intlevel, $rank);
	}
	
	/**
	 * @HandlesCommand("adminlist")
	 * @Matches("/^adminlist$/i")
	 * @Matches("/^adminlist all$/i")
	 */
	public function adminlistCommand($message, $channel, $sender, $sendto) {
		if (strtolower($message) == "adminlist all") {
			$showOfflineAlts = true;
		} else {
			$showOfflineAlts = false;
		}

		$blob .= "<header2>Administrators<end>\n";
		forEach ($this->adminManager->admins as $who => $data) {
			if ($this->adminManager->admins[$who]["level"] == 4) {
				if ($who != "") {
					$blob .= "<tab>$who";
					if ($this->accessManager->checkAccess($who, 'superadmin')) {
						$blob .= " (<highlight>Super-administrator<end>) ";
					}
					$blob .= $this->getOnlineStatus($who) . "\n" . $this->getAltAdminInfo($who, $showOfflineAlts);
				}
			}
		}
		$blob .= $this->getGuildAdmins('admin', $showOfflineAlts);

		$blob .= "<header2>Moderators<end>\n";
		forEach ($this->adminManager->admins as $who => $data){
			if ($this->adminManager->admins[$who]["level"] == 3){
				if ($who != "") {
					$blob .= "<tab>$who" . $this->getOnlineStatus($who) . "\n" . $this->getAltAdminInfo($who, $showOfflineAlts);
				}
			}
		}
		$blob .= $this->getGuildAdmins('mod', $showOfflineAlts);

		$link = $this->text->makeBlob('Bot administrators', $blob);
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
	
	private function getOnlineStatus($who) {
		if ($this->buddylistManager->isOnline($who) == 1 && isset($this->chatBot->chatlist[$who])) {
			return " (<green>Online and in chat<end>)";
		} else if ($this->buddylistManager->isOnline($who) == 1) {
			return " (<green>Online<end>)";
		} else {
			return " (<red>Offline<end>)";
		}
	}
	
	private function getAltAdminInfo($who, $showOfflineAlts) {
		$blob = '';
		if ($this->settingManager->get("alts_inherit_admin") == 1) {
			$altInfo = $this->altsController->getAltInfo($who);
			if ($altInfo->main == $who) {
				forEach ($altInfo->alts as $alt => $validated) {
					if ($validated == 1 && ($showOfflineAlts || $this->buddylistManager->isOnline($alt))) {
						$blob .= "<tab><tab>$alt" . $this->getOnlineStatus($alt) . "\n";
					}
				}
			}
		}
		return $blob;
	}
	
	private function getGuildAdmins($accessLevel, $showOfflineAlts) {
		$blob = '';
		if ($this->settingManager->get('guild_admin_access_level') == $accessLevel) {
			// grab all guild members with this rank
			$sql = "SELECT * FROM players WHERE guild_id = ? AND guild_rank_id <= ? ORDER BY name ASC";
			$players = $this->db->query($sql, $this->chatBot->vars["my_guild_id"], $this->settingManager->get('guild_admin_rank'));
			forEach ($players as $player) {
				if (!isset($this->adminManager->admins[$player->name]) && $this->accessManager->getAccessLevelForCharacter($player->name) == $accessLevel) {
					$blob .= "<tab>{$player->name}" . $this->getOnlineStatus($player->name) . " [Guild Admin]\n" . $this->getAltAdminInfo($player->name, $showOfflineAlts);
				}
			}
		}
		return $blob;
	}
	
	public function add($who, $sender, $sendto, $intlevel, $rank) {
		if ($this->chatBot->get_uid($who) == null){
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
		$ai = $this->altsController->getAltInfo($who);
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
