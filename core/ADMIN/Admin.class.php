<?php

class Admin {

	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $accessLevel;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $buddylist;
	
	/** @Inject */
	public $setting;

	public function removeCommand($message, $channel, $sender, $sendto) {
		if (preg_match("/^remadmin (.+)$/i", $message, $arr)){
			$who = ucfirst(strtolower($arr[1]));
			$intlevel = 4;
			$$rank = 'an administrator';

			$this->remove($who, $sender, $sendto, $intlevel, $$rank);
		} else if (preg_match("/^remmod (.+)$/i", $message, $arr)){
			$who = ucfirst(strtolower($arr[1]));
			$intlevel = 3;
			$$rank = 'a moderator';

			$this->remove($who, $sender, $sendto, $intlevel, $$rank);
		} else if (preg_match("/^remrl (.+)$/i", $message, $arr)){
			$who = ucfirst(strtolower($arr[1]));
			$intlevel = 2;
			$$rank = 'a raidleader';

			$this->remove($who, $sender, $sendto, $intlevel, $$rank);
		} else {
			return false;
		}
	}
	
	public function addCommand($message, $channel, $sender, $sendto) {
		if (preg_match("/^addadmin (.+)$/i", $message, $arr)){
			$who = ucfirst(strtolower($arr[1]));
			$intlevel = 4;
			$$rank = 'an administrator';

			$this->add($who, $sender, $sendto, $intlevel, $$rank);
		} else if (preg_match("/^addmod (.+)$/i", $message, $arr)){
			$who = ucfirst(strtolower($arr[1]));
			$intlevel = 3;
			$$rank = 'a moderator';

			$this->add($who, $sender, $sendto, $intlevel, $$rank);
		} else if (preg_match("/^addrl (.+)$/i", $message, $arr)){
			$who = ucfirst(strtolower($arr[1]));
			$intlevel = 2;
			$$rank = 'a raidleader';

			$this->add($who, $sender, $sendto, $intlevel, $$rank);
		} else {
			return false;
		}
	}
	
	public function remove($who, $sender, $sendto, $intlevel, $rank) {
		if (!$this->checkExisting($who, $intlevel)) {
			$this->chatBot->send("<highlight>$who<end> is not $rank.", $sendto);
			return;
		}
		
		if (!$this->checkAccessLevel($sender, $who, $sendto)) {
			$this->chatBot->send("You must have a higher access level than <highlight>$who<end> in order to change his access level.", $sendto);
			return;
		}

		$this->removeFromLists($who);			

		if (!$this->checkAltsInheritAdmin($who)) {
			$this->chatBot->send("<red>WARNING<end>: alts inheriting admin is enabled, but $who is not a main character.  {$ai->main} is $who's main.  <red>This command did NOT affect either characters' admin privileges.<end>", $sendto);
		}

		$this->chatBot->send("<highlight>$who<end> has been removed as $rank.", $sendto);
		$this->chatBot->send("You have been removed as $rank by <highlight>$sender<end>.", $who);
	}
	
	public function add($who, $sender, $sendto, $intlevel, $rank) {
		if ($this->chatBot->get_uid($who) == NULL){
			$this->chatBot->send("The character <highlight>$who<end> does not exist.", $sendto);
			return;
		}
		
		if ($this->checkExisting($who, $intlevel)) {
			$this->chatBot->send("<highlight>$who<end> is already $rank.", $sendto);
			return;
		}

		if (!$this->checkAccessLevel($sender, $who, $sendto)) {
			$this->chatBot->send("You must have a higher access level than <highlight>$who<end> in order to change his access level.", $sendto);
			return;
		}
		
		if (!$this->checkAltsInheritAdmin($who)) {
			$msg = "<red>Alts inheriting admin is enabled, and $who is not a main character.<end>";
			if ($this->chatBot->admins[$ai->main]["level"] == $intlevel) {
				$msg .= " <highlight>{$ai->main}<end> is already $rank.";
			} else {
				$msg .= " Try again with <highlight>$who<end>'s main, <highlight>{$ai->main}<end>.";
			}
			$this->chatBot->send($msg, $sendto);
			return;
		}

		$action = $this->addToLists($who, $intlevel);
		
		$this->chatBot->send("<highlight>$who<end> has been $action to $rank.", $sendto);
		$this->chatBot->send("You have been $action to $rank by <highlight>$sender<end>.", $who);
	}
	
	public function removeFromLists($who) {
		unset($this->chatBot->admins[$who]);
		$this->db->exec("DELETE FROM admin_<myname> WHERE `name` = ?", $who);
		$this->buddylist->remove($who, 'admin');
	}
	
	public function addToLists($who, $intlevel) {
		$action = '';
		if (isset($this->chatBot->admins[$who])) {
			$this->db->exec("UPDATE admin_<myname> SET `adminlevel` = ? WHERE `name` = ?", $intlevel, $who);
			if ($this->chatBot->admins[$who]["level"] > $intlevel) {
				$action = "demoted";
			} else {
				$action = "promoted";
			}
		} else {
			$this->db->exec("INSERT INTO admin_<myname> (`adminlevel`, `name`) VALUES (?, ?)", $intlevel, $who);
			$action = "promoted";
		}
	
		$this->chatBot->admins[$who]["level"] = $intlevel;
		$this->buddylist->add($who, 'admin');
		
		return $action;
	}
	
	public function checkExisting($who, $level) {
		if ($this->chatBot->admins[$who]["level"] != $level) {
			return false;
		} else {
			return true;
		}
	}
	
	public function checkAltsInheritAdmin($who) {
		$ai = Alts::get_alt_info($who);
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