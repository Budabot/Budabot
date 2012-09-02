<?php

class Admin {

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $accessLevel;

	/** @Inject */
	public $db;

	/** @Inject */
	public $buddylistManager;

	/** @Inject */
	public $setting;

	/** @Inject */
	public $alts;

	public $admins = array();

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
		forEach ($this->admins as $who => $data) {
			if ($this->admins[$who]["level"] == 4) {
				if ($who != "") {
					$blob.= "<tab>$who ";

					if ($this->accessLevel->checkAccess($who, 'superadmin')) {
						$blob .= "(<orange>Super-administrator<end>) ";
					}

					if ($this->buddylistManager->is_online($who) == 1 && isset($this->chatBot->chatlist[$who])) {
						$blob.="(<green>Online and in chat<end>)";
					} else if ($this->buddylistManager->is_online($who) == 1) {
						$blob.="(<green>Online<end>)";
					} else {
						$blob.="(<red>Offline<end>)";
					}

					$blob.= "\n";
				}
			}
		}

		$blob .= "<highlight>Moderators<end>\n";
		forEach ($this->admins as $who => $data){
			if ($this->admins[$who]["level"] == 3){
				if ($who != "") {
					$blob.= "<tab>$who ";
					if ($this->buddylistManager->is_online($who) == 1 && isset($this->chatBot->chatlist[$who])) {
						$blob.="(<green>Online and in chat<end>)";
					} else if ($this->buddylistManager->is_online($who) == 1) {
						$blob.="(<green>Online<end>)";
					} else {
						$blob.="(<red>Offline<end>)";
					}
					$blob.= "\n";
				}
			}
		}

		$link = Text::make_blob('Bot administrators', $blob);
		$sendto->reply($link);
	}

	public function uploadAdmins() {
		$this->db->exec("CREATE TABLE IF NOT EXISTS admin_<myname> (`name` VARCHAR(25) NOT NULL PRIMARY KEY, `adminlevel` INT)");

		$this->chatBot->vars["SuperAdmin"] = ucfirst(strtolower($this->chatBot->vars["SuperAdmin"]));

		$data = $this->db->query("SELECT * FROM admin_<myname> WHERE `name` = ?", $this->chatBot->vars["SuperAdmin"]);
		if (count($data) == 0) {
			$this->db->exec("INSERT INTO admin_<myname> (`adminlevel`, `name`) VALUES (?, ?)", '4', $this->chatBot->vars["SuperAdmin"]);
		} else {
			$this->db->exec("UPDATE admin_<myname> SET `adminlevel` = ? WHERE `name` = ?", '4', $this->chatBot->vars["SuperAdmin"]);
		}

		$data = $this->db->query("SELECT * FROM admin_<myname>");
		forEach ($data as $row) {
			$this->admins[$row->name]["level"] = $row->adminlevel;
		}
	}

	public function checkAdmins() {
		$data = $this->db->query("SELECT * FROM admin_<myname>");
		forEach ($data as $row) {
			$this->buddylistManager->add($row->name, 'admin');
		}
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

	public function remove($who, $sender, $sendto, $intlevel, $rank) {
		if (!$this->checkExisting($who, $intlevel)) {
			$sendto->reply("<highlight>$who<end> is not $rank.");
			return;
		}

		if (!$this->checkAccessLevel($sender, $who, $sendto)) {
			$sendto->reply("You must have a higher access level than <highlight>$who<end> in order to change his access level.");
			return;
		}

		$this->removeFromLists($who);

		if (!$this->checkAltsInheritAdmin($who)) {
			$sendto->reply("<red>WARNING<end>: alts inheriting admin is enabled, but $who is not a main character.  {$ai->main} is $who's main.  <red>This command did NOT affect either characters' admin privileges.<end>");
		}

		$sendto->reply("<highlight>$who<end> has been removed as $rank.");
		$this->chatBot->sendTell("You have been removed as $rank by <highlight>$sender<end>.", $who);
	}

	public function add($who, $sender, $sendto, $intlevel, $rank) {
		if ($this->chatBot->get_uid($who) == NULL){
			$sendto->reply("Character <highlight>$who<end> does not exist.");
			return;
		}

		if ($this->checkExisting($who, $intlevel)) {
			$sendto->reply("<highlight>$who<end> is already $rank.");
			return;
		}

		if (!$this->checkAccessLevel($sender, $who, $sendto)) {
			$sendto->reply("You must have a higher access level than <highlight>$who<end> in order to change his access level.");
			return;
		}

		if (!$this->checkAltsInheritAdmin($who)) {
			$msg = "<red>Alts inheriting admin is enabled, and $who is not a main character.<end>";
			if ($this->admins[$ai->main]["level"] == $intlevel) {
				$msg .= " <highlight>{$ai->main}<end> is already $rank.";
			} else {
				$msg .= " Try again with <highlight>$who<end>'s main, <highlight>{$ai->main}<end>.";
			}
			$sendto->reply($msg);
			return;
		}

		$action = $this->addToLists($who, $intlevel);

		$sendto->reply("<highlight>$who<end> has been $action to $rank.");
		$this->chatBot->sendTell("You have been $action to $rank by <highlight>$sender<end>.", $who);
	}

	public function removeFromLists($who) {
		unset($this->admins[$who]);
		$this->db->exec("DELETE FROM admin_<myname> WHERE `name` = ?", $who);
		$this->buddylistManager->remove($who, 'admin');
	}

	public function addToLists($who, $intlevel) {
		$action = '';
		if (isset($this->admins[$who])) {
			$this->db->exec("UPDATE admin_<myname> SET `adminlevel` = ? WHERE `name` = ?", $intlevel, $who);
			if ($this->admins[$who]["level"] > $intlevel) {
				$action = "demoted";
			} else {
				$action = "promoted";
			}
		} else {
			$this->db->exec("INSERT INTO admin_<myname> (`adminlevel`, `name`) VALUES (?, ?)", $intlevel, $who);
			$action = "promoted";
		}

		$this->admins[$who]["level"] = $intlevel;
		$this->buddylistManager->add($who, 'admin');

		return $action;
	}

	public function checkExisting($who, $level) {
		if ($this->admins[$who]["level"] != $level) {
			return false;
		} else {
			return true;
		}
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

?>
