<?php

/**
 * Authors: 
 *	- Derroylo (RK2)
 *  - Marinerecon (RK2)
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'loot',
 *		accessLevel = 'all',
 *		description = 'Show the loot list',
 *		help        = 'flatroll.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'loot .+',
 *		accessLevel = 'rl',
 *		description = 'Modify the loot list',
 *		help        = 'flatroll.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'multiloot',
 *		accessLevel = 'rl',
 *		description = 'Add items with more than one quantity to the loot list',
 *		help        = 'flatroll.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'remloot',
 *		accessLevel = 'rl',
 *		description = 'Remove item from loot list',
 *		help        = 'flatroll.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'reroll',
 *		accessLevel = 'rl',
 *		description = 'Reroll the residual loot list',
 *		help        = 'flatroll.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'flatroll',
 *		accessLevel = 'rl',
 *		description = 'Roll the loot list',
 *		help        = 'flatroll.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'add',
 *		accessLevel = 'all',
 *		description = 'Add a player to a roll slot',
 *		help        = 'add_rem.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'rem',
 *		accessLevel = 'all',
 *		description = 'Remove a player from a roll slot',
 *		help        = 'add_rem.txt'
 *	)
 */
class RaidController {

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
	public $settingManager;
	
	/** @Inject */
	public $commandAlias;
	
	/** @Inject */
	public $text;
	
	private $loot = array();
	private $residual = array();
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->settingManager->add($this->moduleName, "add_on_loot", "Adding to loot show on", "edit", "options", "2", "tells;privatechat;privatechat and tells", '1;2;3', "mod");
		
		$this->commandAlias->register($this->moduleName, "flatroll", "rollloot");
		$this->commandAlias->register($this->moduleName, "flatroll", "result");
		$this->commandAlias->register($this->moduleName, "flatroll", "win");
		
		$this->commandAlias->register($this->moduleName, "loot", "list");
	}
	
	/**
	 * @HandlesCommand("loot")
	 * @Matches("/^loot$/i")
	 */
	public function lootCommand($message, $channel, $sender, $sendto, $args) {
		$msg = $this->get_current_loot_list();
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("loot .+")
	 * @Matches("/^loot clear$/i")
	 */
	public function lootClearCommand($message, $channel, $sender, $sendto, $args) {
		$this->loot = array();
		$this->residual = array();
		$msg = "Loot has been cleared by <highlight>$sender<end>.";
		$this->chatBot->sendPrivate($msg);

		if ($channel != 'priv') {
			$sendto->reply($msg);
		}
	}
	
	/**
	 * @HandlesCommand("loot .+")
	 * @Matches("/^loot ([0-9]+)$/i")
	 */
	public function lootAddByIdCommand($message, $channel, $sender, $sendto, $args) {
		$id = $args[1];

		$sql = "SELECT * FROM raid_loot r LEFT JOIN aodb a ON (r.name = a.name AND r.ql >= a.lowql AND r.ql <= a.highql) WHERE id = ?";
		$row = $this->db->queryRow($sql, $id);

		if ($row === null) {
			$msg = "Could not find item with id <highlight>$id<end> to add.";
			$sendto->reply($msg);
			return;
		}

		$key = $this->getLootItem($row->name);
		if ($key !== null) {
			$item = $this->loot[$key];
			$item->multiloot += $row->multiloot;
		} else {
			if (!empty($this->loot)) {
				$key = count($this->loot) + 1;
			} else {
				$key = 1;
			}
			
			$item = new stdClass;
			
			$item->name = $row->name;
			$item->icon = $row->icon;
			$item->added_by = $sender;
			$item->display = $this->text->make_item($row->lowid, $row->highid, $row->ql, $row->name);
			$item->multiloot = $row->multiloot;
			$item->users = array();
			
			$this->loot[$key] = $item;
		}
		$msg = "<highlight>{$item->name}<end> (x$item->multiloot) will be rolled in Slot <highlight>#$key<end>.";
		$msg .= " To add use <symbol>add $key, or <symbol>rem to remove yourself.";
		$this->chatBot->sendPrivate($msg);
	}

	/**
	 * @HandlesCommand("loot .+")
	 * @Matches("/^loot (.+)$/i")
	 */
	public function lootAddCommand($message, $channel, $sender, $sendto, $args) {
		$input = $args[1];
		$this->addLootItem($input, 1, $sender);
	}
	
	/**
	 * @HandlesCommand("multiloot")
	 * @Matches("/^multiloot ([0-9]+)x? (.+)$/i")
	 */
	public function multilootCommand($message, $channel, $sender, $sendto, $args) {
		$multiloot = $args[1];
		$input = $args[2];
		$this->addLootItem($input, $multiloot, $sender);
	}
	
	public function addLootItem($input, $multiloot, $sender) {
		//Check if the item is a link
		if (preg_match("/^<a href=\"itemref:\/\/([0-9]+)\/([0-9]+)\/([0-9]+)\">(.+)<\/a>(.*)$/i", $input, $arr)) {
			$item_ql = $arr[3];
			$item_highid = $arr[1];
			$item_lowid = $arr[2];
			$item_name = $arr[4];
		} else if (preg_match("/^(.+)<a href=\"itemref:\/\/([0-9]+)\/([0-9]+)\/([0-9]+)\">(.+)<\/a>(.*)$/i", $input, $arr)){
			$item_ql = $arr[4];
			$item_highid = $arr[2];
			$item_lowid = $arr[3];
			$item_name = $arr[5];
		} else {
			$item_name = $input;
		}
		
		// check if there is an icon available
		$row = $this->db->queryRow("SELECT * FROM aodb WHERE `name` LIKE ?", $item_name);
		if ($row !== null) {
			$item_name = $row->name;

			//Save the icon
			$looticon = $row->icon;
			
			//Save the aoid and ql if not set yet
			if (!isset($item_highid)) {
				$item_lowid = $row->lowid;
				$item_highid = $row->highid;
				$item_ql = $row->highql;
			}
		}

		// check if the item is already on the list
		$key = $this->getLootItem($item_name);
		if ($key !== null) {
			$item = $this->loot[$key];
			$item->multiloot += $multiloot;
		} else {
			// get a slot for the item
			if (!empty($this->loot)) {
				$key = count($this->loot) + 1;
			} else {
				$key = 1;
			}
			
			$item = new stdClass;
			
			$item->name = $item_name;
			$item->icon = $looticon;
			$item->added_by = $sender;
			$item->multiloot = $multiloot;
			$item->users = array();

			if (isset($item_highid)) {
				$item->display = $this->text->make_item($item_lowid, $item_highid, $item_ql, $item_name);
			} else {
				$item->display = $item_name;
			}
			$this->loot[$key] = $item;
		}

		$msg = "<highlight>{$item->name}<end> (x$item->multiloot) will be rolled in Slot <highlight>#$key<end>.";
		$msg .= " To add use <symbol>add $key, or <symbol>rem to remove yourself.";
		$this->chatBot->sendPrivate($msg);
	}
	
	/**
	 * @HandlesCommand("remloot")
	 * @Matches("/^remloot ([0-9]+)$/i")
	 */
	public function remlootCommand($message, $channel, $sender, $sendto, $args) {
		$key = $args[1];
		// validate item existance on loot list
		if ($key > 0 && $key <= count($this->loot)) {
			// if removing this item empties the list, clear the loot list properly
			if (count($this->loot) <= 1) {
				$this->loot = array();
				$this->chatBot->sendPrivate("Item in slot <highlight>#".$key."<end> was the last item in the list. The list has been cleared.");
			} else {
				// remove the item by shifting lower items up one slot and remove last slot
				$loop = $key;
				while($loop < count($this->loot)){
					$this->loot[$loop] = $this->loot[$loop+1];
					$loop++;
				}
				unset($this->loot[count($this->loot)]);
				$this->chatBot->sendPrivate("Deleting item in slot <highlight>#".$key."<end>");
			}
		} else {
			$this->chatBot->sendPrivate("There is no item at slot <highlight>#".$key."<end>");
		}
	}
	
	/**
	 * @HandlesCommand("reroll")
	 * @Matches("/^reroll$/i")
	 */
	public function rerollCommand($message, $channel, $sender, $sendto, $args) {
		//Check if a residual list exits
		if (empty($this->residual)) {
			$msg = "There are no remaining items to re-add.";
			$sendto->reply($msg);
			return;
		}

		// Readd remaining loot
		forEach ($this->residual as $key => $item) {
			$this->loot[$key] = $item;
			$this->loot[$key]->added_by = $sender;
		}

		//Reset residual list
		$this->residual = array();
		//Show winner list
		$msg = "All remaining items have been re-added by <highlight>$sender<end>. Check <symbol>list.";
		$this->chatBot->sendPrivate($msg);
		if ($channel != 'priv') {
			$sendto->reply($msg);
		}

		$msg = $this->get_current_loot_list();
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("flatroll")
	 * @Matches("/^flatroll$/i")
	 */
	public function flatrollCommand($message, $channel, $sender, $sendto, $args) {
		global $loot_winners;
		//Check if a loot list exits
		if (empty($this->loot)) {
			$msg = "There is nothing to roll atm.";
			$sendto->reply($msg);
			return;
		}

		srand(((int)((double)microtime() * 1000003))); // get a good seed

		$list = '';
		//Roll the loot
		$resnum = 1;
		forEach ($this->loot as $key => $item) {
			$list .= "Item: <orange>{$item->name}<end>\n";
			$list .= "Winner(s): ";
			$users = count($item->users);
			if ($users == 0) {
				$list .= "<highlight>None added.<end>\n\n";
				$this->residual[$resnum] = $item;
				$resnum++;
			} else {
				if ($item->multiloot > 1) {
					if ($item->multiloot > count($item->users)) {
						$arrolnum = count($item->users);
					} else {
						$arrolnum = $item->multiloot;
					}

					for ($i = 0; $i < $arrolnum; $i++) {
						$winner = array_rand($item->users, 1);
						unset($item->users[$winner]);
						$list .= "<red>$winner<end> ";
					}

					if ($arrolnum < $item->multiloot) {
						$newmultiloot = $item->multiloot - $arrolnum;
						$this->residual[$resnum] = $item;
						$this->residual[$resnum]->multiloot = $newmultiloot;
						$resnum++;
					}
				} else {
					$winner = array_rand($item->users, 1);
					$list .= "<red>$winner<end>";
				}
				$list .= "\n\n";
			}
		}

		//Reset loot
		$this->loot = array();

		//Show winner list
		$msg = $this->text->make_blob("Winner List", $list);
		if (!empty($this->residual)) {
			$msg .= " (There are item(s) left to be rolled. To re-add, type <symbol>reroll)";
		}

		$this->chatBot->sendPrivate($msg);
		if ($channel != 'priv') {
			$sendto->reply($msg);
		}
	}
	
	/**
	 * @HandlesCommand("add")
	 * @Matches("/^add ([0-9]+)$/i")
	 */
	public function addCommand($message, $channel, $sender, $sendto, $args) {
		$slot = $args[1];
		$found = false;
		if (count($this->loot) > 0) {
			//Check if the slot exists
			if (!isset($this->loot[$slot])) {
				$msg = "The slot you are trying to add in does not exist.";
				$this->chatBot->sendTell($msg, $sender);
				return;
			}

			//Remove the player from other slots if set
			$found = false;
			forEach ($this->loot as $key => $item) {
				if ($this->loot[$key]->users[$sender] == true) {
					unset($this->loot[$key]->users[$sender]);
					$found = true;
				}
			}

			//Add the player to the choosen slot
			$this->loot[$slot]->users[$sender] = true;

			if ($found == false) {
				$msg = "$sender has added to <highlight>\"{$this->loot[$slot]->name}\"<end>.";
			} else {
				$msg = "$sender has moved to <highlight>\"{$this->loot[$slot]->name}\"<end>.";
			}

			$this->chatBot->sendPrivate($msg);
		} else {
			$this->chatBot->sendTell("No loot list available.", $sender);
		}
	}
	
	/**
	 * @HandlesCommand("rem")
	 * @Matches("/^rem$/i")
	 */
	public function remCommand($message, $channel, $sender, $sendto, $args) {
		if (count($this->loot) > 0) {
			forEach ($this->loot as $key => $item) {
				if ($this->loot[$key]->users[$sender] == true) {
					unset($this->loot[$key]->users[$sender]);
				}
			}

			$msg = "$sender has been removed from all rolls.";
			$this->chatBot->sendPrivate($msg, 'priv');
		} else {
			$this->chatBot->sendTell("There is nothing to remove you from.", $sender);
		}
	}
	
	public function get_current_loot_list() {
		if (!empty($this->loot)) {
			$list = "Use <symbol>flatroll to roll.\n\n";
			forEach ($this->loot as $key => $item) {
				$add = $this->text->make_chatcmd("Add", "/tell <myname> add $key");
				$rem = $this->text->make_chatcmd("Remove", "/tell <myname> rem");
				$added_players = count($item->users);

				$list .= "<header2>Slot #$key<end>\n";
				if ($item->icon != "") {
					$list .= $this->text->make_image($item->icon) . "\n";
				}

				if ($item->multiloot > 1) {
					$ml = " <highlight>(x".$item->multiloot.")<end>";
				} else {
					$ml = "";
				}

				$list .= "Item: {$item->display}".$ml."\n";

				$list .= "<highlight>$added_players<end> Total ($add/$rem)\n";
				$list .= "Players added:";
				if (count($item->users) > 0) {
					forEach ($item->users as $key => $value) {
						$list .= " [<yellow>$key<end>]";
					}
				} else {
					$list .= " None added yet.";
				}

				$list .= "\n\n";
			}
			$msg = $this->text->make_blob("Loot List", $list);
		} else {
			$msg = "No loot list exists yet.";
		}

		return $msg;
	}

	public function add_raid_to_loot_list($raid, $category) {
		// clear current loot list
		$this->loot = array();
		$count = 1;

		$sql = "SELECT * FROM raid_loot r LEFT JOIN aodb a ON (r.name = a.name AND r.ql >= a.lowql AND r.ql <= a.highql) WHERE raid = ? AND category = ?";
		$data = $this->db->query($sql, $raid, $category);

		if (count($data) == 0) {
			return false;
		}

		forEach ($data as $row) {
			$item = $this->text->make_item($row->lowid, $row->highid, $row->ql, $row->name);
			if (empty($row->comment)) {
				$row->display = $item;
			} else {
				$row->display = $item . " ($row->comment)";
			}
			$row->users = array();
			$this->loot[$count] = $row;
			$count++;
		}

		return true;
	}
	
	public function getLootItem($name) {
		forEach ($this->loot as $key => $item) {
			if ($item->name == $name){
				return $key;
			}
		}
		return null;
	}
}

?>
