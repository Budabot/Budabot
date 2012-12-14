<?php

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command       = 'alts',
 *		accessLevel   = 'member',
 *		description   = 'Alt character handling',
 *		help          = 'alts.txt'
 *	)
 *	@DefineCommand(
 *		command       = 'alts main (.+)',
 *		accessLevel   = 'member',
 *		description   = 'Add yourself as an alt to a main'
 *	)
 *	@DefineCommand(
 *		command       = 'altsadmin',
 *		accessLevel   = 'mod',
 *		description   = 'Alt character handling (admin)',
 *		help          = 'altsadmin.txt'
 *	)
 *	@DefineCommand(
 *		command       = 'altvalidate',
 *		accessLevel   = 'member',
 *		description   = 'Validate alts for admin privileges',
 *		help          = 'altvalidate.txt'
 *	)
 */
class AltsController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $playerManager;

	/** @Inject */
	public $db;

	/**
	 * @Setting("alts_inherit_admin")
	 * @Description("Alts inherit admin privileges from main")
	 * @Visibility("edit")
	 * @Type("options")
	 * @Options("true;false")
	 * @Intoptions("1;0")
	 * @AccessLevel("mod")
	 * @Help("alts_inherit_admin.txt")
	 */
	public $defaultAltsInheritAdmin = "0";

	/**
	 * @Setup
	 * This handler is called on bot startup.
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'alts');
	}

	/**
	 * This command handler adds alt character.
	 *
	 * @HandlesCommand("alts")
	 * @Matches("/^alts add ([a-z0-9- ]+)$/i")
	 */
	public function addAltCommand($message, $channel, $sender, $sendto, $args) {
		/* get all names in an array */
		$names = explode(' ', $args[1]);
	
		$sender = ucfirst(strtolower($sender));
	
		$senderAltInfo = $this->get_alt_info($sender);
		$main = $senderAltInfo->main;
	
		$success = 0;
	
		/* Pop a name from the array until none are left (checking for null) */
		forEach ($names as $name) {
			$name = ucfirst(strtolower($name));
	
			$uid = $this->chatBot->get_uid($name);
			if (!$uid) {
				$msg = "Character <highlight>{$name}<end> does not exist.";
				$sendto->reply($msg);
				continue;
			}
	
			$altInfo = $this->get_alt_info($name);
			if ($altInfo->main == $senderAltInfo->main) {
				// already registered to self
				$msg = "<highlight>$name<end> is already registered to you.";
				$sendto->reply($msg);
				continue;
			}
	
			if (count($altInfo->alts) > 0) {
				// already registered to someone else
				if ($altInfo->main == $name) {
					$msg = "<highlight>$name<end> is already registered as a main with alts.";
				} else {
					$msg = "<highlight>$name<end> is already registered as an of alt of <highlight>{$altInfo->main}<end>.";
				}
				$sendto->reply($msg);
				continue;
			}
	
			$validated = 0;
			if ($senderAltInfo->is_validated($sender)) {
				$validated = 1;
			}
	
			/* insert into database */
			$this->add_alt($senderAltInfo->main, $name, $validated);
			$success++;
	
			// update character information
			$this->playerManager->get_by_name($name);
		}
	
		if ($success > 0) {
			$msg = ($success == 1 ? "Alt" : "$success alts") . " added successfully.";
			$sendto->reply($msg);
		}
	}

	/**
	 * This command handler removes alt character.
	 *
	 * @HandlesCommand("alts")
	 * @Matches("/^alts (rem|del|remove|delete) ([a-z0-9-]+)$/i")
	 */
	public function removeAltCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[2]));
	
		$altInfo = $this->get_alt_info($sender);
	
		if (!array_key_exists($name, $altInfo->alts)) {
			$msg = "<highlight>{$name}<end> is not registered as your alt.";
		} else if (!$altInfo->is_validated($sender) && $altInfo->is_validated($name)) {
			$msg = "You must be on a validated alt to remove another alt that is validated.";
		} else {
			$this->rem_alt($altInfo->main, $name);
			$msg = "<highlight>{$name}<end> has been deleted from your alt list.";
		}
		$sendto->reply($msg);
	}

	/**
	 * This command handler sets main character.
	 *
	 * @HandlesCommand("alts")
	 * @Matches("/^alts setmain ([a-z0-9-]+)$/i")
	 */
	public function setMainCommand($message, $channel, $sender, $sendto, $args) {
		// check if new main exists
		$new_main = ucfirst(strtolower($args[1]));
		$uid = $this->chatBot->get_uid($new_main);
		if (!$uid) {
			$msg = "Character <highlight>{$new_main}<end> does not exist.";
			$sendto->reply($msg);
			return;
		}
	
		$altInfo = $this->get_alt_info($sender);
	
		if (!array_key_exists($new_main, $altInfo->alts)) {
			$msg = "<highlight>{$new_main}<end> must first be registered as your alt.";
			$sendto->reply($msg);
			return;
		}
	
		if (!$altInfo->is_validated($sender)) {
			$msg = "You must run this command from a validated character.";
			$sendto->reply($msg);
			return;
		}
	
		$this->db->begin_transaction();
	
		// remove all the old alt information
		$this->db->exec("DELETE FROM `alts` WHERE `main` = '{$altInfo->main}'");
	
		// add current main to new main as an alt
		$this->add_alt($new_main, $altInfo->main, 1);
	
		// add current alts to new main
		forEach ($altInfo->alts as $alt => $validated) {
			if ($alt != $new_main) {
				$this->add_alt($new_main, $alt, $validated);
			}
		}
	
		$this->db->commit();
	
		$msg = "Your new main is now <highlight>{$new_main}<end>.";
		$sendto->reply($msg);
	}

	/**
	 * This command handler lists alt characters.
	 *
	 * @HandlesCommand("alts")
	 * @Matches("/^alts ([a-z0-9-]+)$/i")
	 * @Matches("/^alts$/i")
	 */
	public function altsCommand($message, $channel, $sender, $sendto, $args) {
		if (isset($args[1])) {
			$showValidateLinks = false;
			$name = ucfirst(strtolower($args[1]));
		} else {
			$showValidateLinks = true;
			$name = $sender;
		}
	
		$altInfo = $this->get_alt_info($name);
		if (count($altInfo->alts) == 0) {
			$msg = "No alts are registered for <highlight>{$name}<end>.";
		} else {
			$msg = $altInfo->get_alts_blob($showValidateLinks);
		}
	
		$sendto->reply($msg);
	}

	/**
	 * This command handler adds yourself as an alt to a main.
	 *
	 * @HandlesCommand("alts main (.+)")
	 * @Matches("/^alts main ([a-z0-9-]+)$/i")
	 */
	public function altsMainCommand($message, $channel, $sender, $sendto, $args) {
		$new_main = $this->get_alt_info($args[1])->main;
	
		$uid = $this->chatBot->get_uid($new_main);
		if (!$uid) {
			$msg = "Character <highlight>$new_main<end> does not exist.";
			$sendto->reply($msg);
			return;
		}
	
		$altInfo = $this->get_alt_info($sender);
	
		if ($altInfo->main == $new_main) {
			$msg = "You are already registered as an alt of <highlight>{$new_main}<end>.";
			$sendto->reply($msg);
			return;
		}
	
		if ($altInfo->main == $sender && count($altInfo->alts) > 0) {
			$msg = "You must not have any alts already registered.";
			$sendto->reply($msg);
			return;
		}
	
		// let them know if they are changing the main for this player
		if ($altInfo->main != $sender) {
			$this->rem_alt($altInfo->main, $sender);
			$msg = "You have been removed as an alt of <highlight>{$altInfo->main}<end>.";
			$sendto->reply($msg);
		}
	
		$this->add_alt($new_main, $sender, 0);
		$msg = "You have been registered as an alt of <highlight>{$new_main}<end>.";
		$sendto->reply($msg);
	}

	/**
	 * This command handler adds a character as alt of an main, requires moderator rights.
	 *
	 * @HandlesCommand("altsadmin")
	 * @Matches("/^altsadmin add ([a-z0-9-]+) ([a-z0-9-]+)$/i")
	 */
	public function altsadminAddCommand($message, $channel, $sender, $sendto, $args) {
		$name_main = ucfirst(strtolower($args[1]));
		$name_alt = ucfirst(strtolower($args[2]));
		$uid_main = $this->chatBot->get_uid($name_main);
		$uid_alt = $this->chatBot->get_uid($name_alt);
	
		if (!$uid_alt) {
			$msg = "Character <highlight>$name_alt<end> does not exist.";
			$sendto->reply($msg);
			return;
		}
		if (!$uid_main) {
			$msg = "Character <highlight>$name_main<end> does not exist.";
			$sendto->reply($msg);
			return;
		}
	
		$mainInfo = $this->get_alt_info($name_main);
		$altinfo = $this->get_alt_info($name_alt);
		if ($altinfo->main == $mainInfo->main) {
			$msg = "Character <highlight>$name_alt<end> is already registered as an alt of <highlight>{$altinfo->main}<end>.";
			$sendto->reply($msg);
			return;
		}
	
		if (count($altInfo->alts) > 0) {
			// already registered to someone else
			if ($altInfo->main == $name) {
				$msg = "<highlight>$name<end> is already registered as a main with alts.";
			} else {
				$msg = "<highlight>$name<end> is already registered as an of alt of {$altInfo->main}.";
			}
			$sendto->reply($msg);
			return;
		}
	
		$this->add_alt($mainInfo->main, $name_alt, 0);
		$msg = "<highlight>$name_alt<end> has been registered as an alt of {$mainInfo->main}.";
		$sendto->reply($msg);
	}

	/**
	 * This command handler removes alt from a main player, requires moderator rights.
	 *
	 * @HandlesCommand("altsadmin")
	 * @Matches("/^altsadmin rem ([a-z0-9-]+) ([a-z0-9-]+)$/i")
	 */
	public function altsadminRemoveCommand($message, $channel, $sender, $sendto, $args) {
		$name_main = ucfirst(strtolower($args[1]));
		$name_alt = ucfirst(strtolower($args[2]));
	
		if ($this->rem_alt($name_main, $name_alt) == 0) {
			$msg = "Character <highlight>$name_alt<end> is not listed as an alt of <highlight>$name_main<end>.";
		} else {
			$msg = "<highlight>$name_alt<end> has been removed from the alt list of <highlight>$name_main<end>.";
		}
		$sendto->reply($msg);
	}

	/**
	 * This command handler validate alts for admin privileges.
	 *
	 * @HandlesCommand("altvalidate")
	 * @Matches("/^altvalidate ([a-z0-9- ]+)$/i")
	 */
	public function altvalidateCommand($message, $channel, $sender, $sendto, $args) {
		$altInfo = $this->get_alt_info($sender);
		$alt = ucfirst(strtolower($args[1]));
	
		if (!$altInfo->is_validated($sender)) {
			$sendto->reply("<highlight>$alt<end> cannot be validated from your current character.");
			return;
		}
	
		// Make sure the character being validated is an alt of the person sending the command
		$isAlt = false;
		forEach ($altInfo->alts as $a => $validated) {
			if ($a == $alt) {
				$isAlt = true;
	
				if ($validated == 1) {
					$sendto->reply("<highlight>$alt<end> is already validated as your alt.");
					return;
				}
			}
		}
	
		if (!$isAlt) {
			$sendto->reply("<highlight>$alt<end> is not registered as your alt.");
		} else {
			$this->db->exec("UPDATE `alts` SET `validated` = ? WHERE `alt` LIKE ? AND `main` LIKE ?", '1', $alt, $altInfo->main);
			$sendto->reply("<highlight>$alt<end> has been validated as your alt.");
		}
	}

	/**
	 * @Event("logOn")
	 * @Description("Reminds players logging in to validate alts")
	 */
	public function checkUnvalidatedAltsEvent($eventObj) {
		if ($this->chatBot->is_ready() && $this->settingManager->get('alts_inherit_admin') == 1) {
			$altInfo = $this->get_alt_info($eventObj->sender);
		
			if ($altInfo->hasUnvalidatedAlts() && $altInfo->is_validated($eventObj->sender)) {
				$msg = "You have unvalidated alts. Please validate them.";
				$this->chatBot->sendTell($msg, $eventObj->sender);
				$this->chatBot->sendTell($altInfo->get_alts_blob(true), $eventObj->sender);
			}
		}
	}

	/**
	 * This method has been implemented from AltsInterface interface.
	 * See the interface's documentation.
	 */
	public function get_alt_info($player) {
		$player = ucfirst(strtolower($player));

		$ai = new AltInfo();

		$sql = "SELECT `alt`, `main`, `validated` FROM `alts` WHERE (`main` LIKE ?) OR (`main` LIKE (SELECT `main` FROM `alts` WHERE `alt` LIKE ?))";
		$data = $this->db->query($sql, $player, $player);

		$isValidated = 0;

		if (count($data) > 0) {
			forEach ($data as $row) {
				$ai->main = $row->main;
				$ai->alts[$row->alt] = $row->validated;
			}
		} else {
			$ai->main = $player;
		}

		return $ai;
	}

	/**
	 * This method adds given @a $alt as @a $main's alt character.
	 */
	private function add_alt($main, $alt, $validated) {
		$main = ucfirst(strtolower($main));
		$alt = ucfirst(strtolower($alt));

		$sql = "INSERT INTO `alts` (`alt`, `main`, `validated`) VALUES (?, ?, ?)";
		return $this->db->exec($sql, $alt, $main, $validated);
	}

	/**
	 * This method removes given @a $alt from being @a $main's alt character.
	 */
	private function rem_alt($main, $alt) {
		$sql = "DELETE FROM `alts` WHERE `alt` LIKE ? AND `main` LIKE ?";
		return $this->db->exec($sql, $alt, $main);
	}
}
