<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
*	@DefineCommand(
 *		command     = 'friendlist', 
 *		accessLevel = 'mod', 
 *		description = 'Show buddies on the buddylist', 
 *		help        = 'friendlist.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'addbuddy', 
 *		accessLevel = 'mod', 
 *		description = 'Add a buddy to the buddylist', 
 *		help        = 'friendlist.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'rembuddy', 
 *		accessLevel = 'mod', 
 *		description = 'Remove a buddy from the buddylist', 
 *		help        = 'friendlist.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'rembuddyall', 
 *		accessLevel = 'mod', 
 *		description = 'Remove all buddies from the buddylist', 
 *		help        = 'friendlist.txt'
 *	)
 */
class BuddylistController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $buddylistManager;

	/** @Inject */
	public $text;
	
	/**
	 * @HandlesCommand("friendlist")
	 * @Matches("/^friendlist$/i")
	 * @Matches("/^friendlist (clean)$/i")
	 */
	public function friendlistShowCommand($message, $channel, $sender, $sendto, $args) {
		if (count($args) == 2) {
			$cleanup = true;
		}

		$orphanCount = 0;
		if (count($this->buddylistManager->buddyList) == 0) {
			$msg = "There are no players on the friendlist.";
		} else {
			$count = 0;
			forEach ($this->buddylistManager->buddyList as $key => $value) {
				if (!isset($value['name'])) {
					// skip the buddies that have been added but the server hasn't sent back an update yet
					continue;
				}

				$count++;
				$removed = '';
				if (count($value['types']) == 0) {
					$orphanCount++;
					if ($cleanup) {
						$this->buddylistManager->remove($value['name']);
						$removed = "<red>REMOVED<end>";
					}
				}

				$blob .= $value['name'] . " $removed " . implode(' ', array_keys($value['types'])) . "\n";
			}

			if ($cleanup) {
				$blob .="\n\nRemoved: ($orphanCount)";
			} else {
				$blob .= "\n\nUnknown: ($orphanCount) ";
				if ($orphanCount > 0) {
					$blob .= $this->text->make_chatcmd('Remove Orphans', '/tell <myname> <symbol>friendlist clean');
				}
			}

			if ($cleanup) {
				$sendto->reply("Removed $orphanCount friends from the friendlist.");
			}
			$msg = $this->text->make_blob("Friendlist ($count)", $blob);
		}
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("friendlist")
	 * @Matches("/^friendlist (.*)$/i")
	 */
	public function friendlistSearchCommand($message, $channel, $sender, $sendto, $args) {
		$search = $args[1];

		if (count($this->buddylistManager->buddyList) == 0) {
			$msg = "There are no players on the friendlist.";
		} else {
			$count = 0;
			$blob = "Friendlist Search: '{$search}'\n\n";
			forEach ($this->buddylistManager->buddyList as $key => $value) {
				$removed = '';
				if (preg_match("/$search/i", $value['name'])) {
					$count++;
					$blob .= $value['name'] . " " . implode(' ', array_keys($value['types'])) . "\n";
				}
			}

			if ($count > 0) {
				$msg = $this->text->make_blob("Friendlist Search ($count)", $blob);
			} else {
				$msg = "No friends on the friendlist found containing '$search'";
			}
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("addbuddy")
	 * @Matches("/^addbuddy (.+) (.+)$/i")
	 */
	public function addbuddyCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$type = $args[2];

		if ($this->buddylistManager->add($name, $type)) {
			$msg = "<highlight>$name<end> added to the buddy list successfully.";
		} else {
			$msg = "Could not add <highlight>$name<end> to the buddy list.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("rembuddy")
	 * @Matches("/^rembuddy (.+) (.+)$/i")
	 */
	public function rembuddyCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$type = $args[2];

		if ($this->buddylistManager->remove($name, $type)) {
			$msg = "<highlight>$name<end> removed from the buddy list successfully.";
		} else {
			$msg = "Could not remove <highlight>$name<end> from the buddy list.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("rembuddyall")
	 * @Matches("/^rembuddyall$/i")
	 */
	public function rembuddyallCommand($message, $channel, $sender, $sendto, $args) {
		forEach ($this->buddylistManager->buddyList as $uid => $buddy) {
			$this->chatBot->buddy_remove($uid);
		}

		$msg = "All buddies have been removed from the buddy list.";
		$sendto->reply($msg);
	}
}
