<?php

namespace Budabot\User\Modules;

/**
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'whatbuffs',
 *		accessLevel = 'all',
 *		description = 'Find items that buff an ability or skill',
 *		help        = 'whatbuffs.txt'
 *	)
 */
class WhatBuffsController {
	
	public $moduleName;

	/** @Inject */
	public $http;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $itemsController;
	
	/** @Logger */
	public $logger;
	
	private $types = array('Nano', 'Weapon', 'Armor', 'Utility', 'Tower');
	
	/** @Setup */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "buffs");
	}

	/**
	 * @HandlesCommand("whatbuffs")
	 * @Matches("/^whatbuffs$/i")
	 */
	public function whatbuffsCommand($message, $channel, $sender, $sendto, $args) {
		$blob = '';
		forEach ($this->types as $type) {
			$blob .= $this->text->makeChatcmd($type, "/tell <myname> whatbuffs $type") . "\n";
		}
		$msg = $this->text->makeBlob("WhatBuffs - Choose Type", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("whatbuffs")
	 * @Matches("/^whatbuffs (nano|weapon|armor|utility|tower)$/i")
	 */
	public function whatbuffs2Command($message, $channel, $sender, $sendto, $args) {
		$type = ucfirst(strtolower($args[1]));
		$data = $this->db->query("SELECT skill, COUNT(1) AS num FROM buffs WHERE type = ? GROUP BY skill HAVING num > 0 ORDER BY skill ASC", $type);
		$blob = '';
		forEach ($data as $row) {
			$blob .= $this->text->makeChatcmd(ucfirst($row->skill), "/tell <myname> whatbuffs $type $row->skill") . " ($row->num)\n";
		}
		$msg = $this->text->makeBlob("WhatBuffs - Choose Skill", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("whatbuffs")
	 * @Matches("/^whatbuffs (nano|weapon|armor|utility|tower) (.*)$/i")
	 */
	public function whatbuffs3Command($message, $channel, $sender, $sendto, $args) {
		$category = $args[1];
		$skill = $args[2];
		
		$msg = $this->showSearchResults($category, $skill);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("whatbuffs")
	 * @Matches("/^whatbuffs (.*) (nano|weapon|armor|utility|tower)$/i")
	 */
	public function whatbuffs4Command($message, $channel, $sender, $sendto, $args) {
		$category = $args[2];
		$skill = $args[1];
		
		$msg = $this->showSearchResults($category, $skill);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("whatbuffs")
	 * @Matches("/^whatbuffs (.*)$/i")
	 */
	public function whatbuffs5Command($message, $channel, $sender, $sendto, $args) {
		$skill = $args[1];
		
		$skills = $this->searchForSkill($skill);
		$count = count($skills);
		
		if ($count == 0) {
			$msg = "Could not find any skills matching <highlight>$skill<end>.";
		} else if ($count == 1) {
			$skill = $skills[0]->skill;
			$data = $this->db->query("SELECT type, COUNT(1) AS num FROM buffs WHERE skill = ? GROUP BY type HAVING num > 0 ORDER BY type ASC", $skill);
			$blob = '';
			forEach ($data as $row) {
				$blob .= $this->text->makeChatcmd(ucfirst($row->type), "/tell <myname> whatbuffs $row->type $skill") . " ($row->num)\n";
			}
			$msg = $this->text->makeBlob("WhatBuffs - Choose Type for $skill", $blob);
		} else {
			$blob = '';
			forEach ($skills as $row) {
				$blob .= $this->text->makeChatcmd(ucfirst($row->skill), "/tell <myname> whatbuffs $row->skill") . "\n";
			}
			$msg = $this->text->makeBlob("WhatBuffs - Choose Skill", $blob);
		}
		$sendto->reply($msg);
	}
	
	public function getSearchResults($category, $skill) {
		$data = $this->db->query("SELECT * FROM buffs WHERE skill = ? AND type = ?", $skill, $category);

		if ($category == 'Nano') {
			$result = $this->formatNanos($data);
		} else {
			$result = $this->formatItems($data);
		}

		if ($result === null) {
			$msg = "No {$category}s found that buff $skill.";
		} else {
			list($count, $blob) = $result;
			$blob .= "\nSearch data provided by aoitems.com";
			$msg = $this->text->makeBlob("WhatBuffs - $category $skill ($count)", $blob);
		}
		return $msg;
	}
	
	public function searchForSkill($skill) {
		// check for exact match first, in order to disambiguate
		// between Bow and Bow special attack 
		$results = $this->db->query("SELECT DISTINCT skill FROM buffs WHERE skill LIKE ?", $skill);
		if (count($results) == 1) {
			return $results;
		}
		
		$tmp = explode(" ", $skill);
		list($query, $params) = $this->util->generateQueryFromParams($tmp, 'skill');
		
		return $this->db->query("SELECT DISTINCT skill FROM buffs WHERE $query", $params);
	}
	
	public function formatItems($matches) {
		$items = array();
		forEach ($matches as $row) {
			$item = $this->itemsController->findByName($row->item);
			if ($item !== null) {
				$items []= $item;
			}
		}
		$items = array_unique($items, SORT_REGULAR);
		
		$blob = '';
		forEach ($items as $item) {
			$ql = $item->highql;
			if ($item->lowql != $item->highql) {
				$ql = $item->lowql . " - " . $item->highql;
			}
			
			$blob .= $this->text->makeItem($item->lowid, $item->highid, $item->highql, $item->name) . " ($ql)\n";
		}

		$count = count($items);		
		if ($count > 0) {
			return array($count, $blob);
		} else {
			return null;
		}
	}
	
	public function formatNanos($matches) {
		$blob = '';
		forEach ($matches as $row) {
			$blob .= $this->text->makeChatcmd($row->item, "/tell <myname> nano $row->item") . "\n";
		}

		$count = count($matches);		
		if ($count > 0) {
			return array($count, $blob);
		} else {
			return null;
		}
	}
	
	public function showSearchResults($category, $skill) {
		$category = ucfirst(strtolower($category));
		
		$data = $this->searchForSkill($skill);
		$count = count($data);
		
		if ($count == 0) {
			$msg = "Could not find any skills matching <highlight>$skill<end>.";
		} else if ($count == 1) {
			$row = $data[0];
			$msg = $this->getSearchResults($category, $row->skill);
		} else {
			$blob = '';
			forEach ($data as $row) {
				$blob .= $this->text->makeChatcmd(ucfirst($row->skill), "/tell <myname> whatbuffs $category $row->skill") . "\n";
			}
			$msg = $this->text->makeBlob("WhatBuffs - Choose Skill", $blob);
		}
		
		return $msg;
	}
}
