<?php

/**
 * @Instance
 */
class AdminController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $commandManager;

	/** @Inject */
	public $eventManager;

	/** @Inject */
	public $help;

	/** @Inject */
	public $adminManager;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $buddylistManager;
	
	/** @Inject */
	public $accessLevel;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $setting;
	
	/** @Inject */
	public $alts;

	/**
	 * @Setup
	 * This handler is called on bot startup.
	 */
	public function setup() {
		$className = get_class($this);
		$this->commandManager->activate("msg", "$className.addCommand", "addadmin", "superadmin");
		$this->commandManager->activate("priv", "$className.addCommand", "addadmin", "superadmin");
		$this->commandManager->activate("guild", "$className.addCommand", "addadmin", "superadmin");

		$this->commandManager->activate("msg", "$className.removeCommand", "remadmin", "superadmin");
		$this->commandManager->activate("priv", "$className.removeCommand", "remadmin", "superadmin");
		$this->commandManager->activate("guild", "$className.removeCommand", "remadmin", "superadmin");

		$this->commandManager->activate("msg", "$className.addCommand", "addmod", "admin");
		$this->commandManager->activate("priv", "$className.addCommand", "addmod", "admin");
		$this->commandManager->activate("guild", "$className.addCommand", "addmod", "admin");

		$this->commandManager->activate("msg", "$className.removeCommand", "remmod", "admin");
		$this->commandManager->activate("priv", "$className.removeCommand", "remmod", "admin");
		$this->commandManager->activate("guild", "$className.removeCommand", "remmod", "admin");

		$this->commandManager->activate("msg", "$className.adminlistCommand", "adminlist", 'all');
		$this->commandManager->activate("priv", "$className.adminlistCommand", "adminlist", 'all');
		$this->commandManager->activate("guild", "$className.adminlistCommand", "adminlist", 'all');

		$this->eventManager->activate("connect", "$className.checkAdmins");
		
		$this->adminManager->uploadAdmins();

		$this->help->register($this->moduleName, "admin", "admin.txt", "mod", "Mod/admin help file");
	}
	
	public function addCommand($message, $channel, $sender, $sendto) {
		if (preg_match("/^addadmin (.+)$/i", $message, $arr)){
			$who = ucfirst(strtolower($arr[1]));
			$intlevel = 4;
			$rank = 'an administrator';

			$this->add($who, $sender, $sendto, $intlevel, $rank);
		} else if (preg_match("/^addmod (.+)$/i", $message, $arr)){
			$who = ucfirst(strtolower($arr[1]));
			$intlevel = 3;
			$rank = 'a moderator';

			$this->add($who, $sender, $sendto, $intlevel, $rank);
		} else {
			return false;
		}
	}
	
	public function removeCommand($message, $channel, $sender, $sendto) {
		if (preg_match("/^remadmin (.+)$/i", $message, $arr)){
			$who = ucfirst(strtolower($arr[1]));
			$intlevel = 4;
			$rank = 'an administrator';

			$this->remove($who, $sender, $sendto, $intlevel, $rank);
		} else if (preg_match("/^remmod (.+)$/i", $message, $arr)){
			$who = ucfirst(strtolower($arr[1]));
			$intlevel = 3;
			$rank = 'a moderator';

			$this->remove($who, $sender, $sendto, $intlevel, $rank);
		} else {
			return false;
		}
	}
	
	public function adminlistCommand($message, $channel, $sender, $sendto) {
		if (!preg_match("/^adminlist$/i", $message)) {
			return false;
		}

		$blob .= "<highlight>Administrators<end>\n";
		forEach ($this->adminManager->admins as $who => $data) {
			if ($this->adminManager->admins[$who]["level"] == 4) {
				if ($who != "") {
					$blob .= "<tab>$who ";

					if ($this->accessLevel->checkAccess($who, 'superadmin')) {
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

		$blob .= "<highlight>Moderators<end>\n";
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
	
	public function checkAdmins() {
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
			$msg = "<red>Alts inheriting admin is enabled, and $who is not a main character.<end>";
			if ($this->adminManager->admins[$ai->main]["level"] == $intlevel) {
				$msg .= " <highlight>{$ai->main}<end> is already $rank.";
			} else {
				$msg .= " Try again with <highlight>$who<end>'s main, <highlight>{$ai->main}<end>.";
			}
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
			$sendto->reply("<red>WARNING<end>: alts inheriting admin is enabled, but $who is not a main character.  {$ai->main} is $who's main.  <red>This command did NOT affect either characters' admin privileges.<end>");
		}

		$sendto->reply("<highlight>$who<end> has been removed as $rank.");
		$this->chatBot->sendTell("You have been removed as $rank by <highlight>$sender<end>.", $who);
	}
	
	public function checkAltsInheritAdmin($who) {
		$ai = $this->alts->get_alt_info($who);
		if ($this->setting->get("alts_inherit_admin") == 1 && $ai->main != $who) {
			return false;
		} else {
			return true;
		}
	}
	
	public function checkAccessLevel($actor, $actee) {
		$senderAccessLevel = $this->accessLevel->getAccessLevelForCharacter($actor);
		$whoAccessLevel = $this->accessLevel->getSingleAccessLevel($actee);
		if ($this->accessLevel->compareAccessLevels($whoAccessLevel, $senderAccessLevel) >= 0) {
			return false;
		} else {
			return true;
		}
	}
}
