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
	
	private $types = array('Nano' => -2, 'Weapon' => '-3', 'Armor' => '-4', 'Utility' => '-6');
	
	/** @Setup */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "skills");
	}

	/**
	 * @HandlesCommand("whatbuffs")
	 * @Matches("/^whatbuffs$/i")
	 */
	public function whatbuffsCommand($message, $channel, $sender, $sendto, $args) {
		$blob = '';
		forEach ($this->types as $type => $typeId) {
			$blob .= $this->text->make_chatcmd(ucfirst($type), "/tell <myname> whatbuffs $type") . "\n";
		}
		$msg = $this->text->makeBlob("WhatBuffs - Choose Type", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("whatbuffs")
	 * @Matches("/^whatbuffs (nano|weapon|armor|utility)$/i")
	 */
	public function whatbuffs2Command($message, $channel, $sender, $sendto, $args) {
		$type = ucfirst(strtolower($args[1]));
		$data = $this->db->query("SELECT name FROM skills WHERE common = 1");
		$blob = '';
		forEach ($data as $row) {
			$blob .= $this->text->make_chatcmd(ucfirst($row->name), "/tell <myname> whatbuffs $type $row->name") . "\n";
		}
		$msg = $this->text->makeBlob("WhatBuffs - Choose Skill", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("whatbuffs")
	 * @Matches("/^whatbuffs (nano|weapon|armor|utility) (.*)$/i")
	 */
	public function whatbuffs3Command($message, $channel, $sender, $sendto, $args) {
		$category = $args[1];
		$skill = $args[2];
		
		$msg = $this->showSearchResults($category, $skill);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("whatbuffs")
	 * @Matches("/^whatbuffs (.*) (nano|weapon|armor|utility)$/i")
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
		
		$data = $this->searchForSkill($skill);
		$count = count($data);
		
		if ($count == 0) {
			$msg = "Could not find any skills matching <highlight>$skill<end>.";
		} else if ($count == 1) {
			$row = $data[0];
			$blob = '';
			forEach ($this->types as $type => $typeId) {
				$blob .= $this->text->make_chatcmd(ucfirst($type), "/tell <myname> whatbuffs $type $row->name") . "\n";
			}
			$msg = $this->text->makeBlob("WhatBuffs - Choose Type for $row->name", $blob);
		} else {
			$blob = '';
			forEach ($data as $row) {
				$blob .= $this->text->make_chatcmd(ucfirst($row->name), "/tell <myname> whatbuffs $row->name") . "\n";
			}
			$msg = $this->text->makeBlob("WhatBuffs - Choose Skill", $blob);
		}
		$sendto->reply($msg);
	}
	
	public function getSearchResults($category, $skill) {
		$typeId = $this->types[$category];
		$postParams = array('submit' => 'search', 'conditions' => "EFF($skill)>=1", 'searchtype' => $typeId);

		$response = $this->http->post("http://auno.org/ao/db.php?cmd=parse-search")->withQueryParams($postParams)->waitAndReturnResponse();
		$newUrl = "http://auno.org" . $response->headers['location'] . "&pagesize=1000";
		$contents = $this->http->post($newUrl)->waitAndReturnResponse()->body;
		
		preg_match_all("|<a href=\"/ao/db.php\?id=(\d+)\">([^>]+)</a>|", $contents, $matches, PREG_SET_ORDER);
		
		if ($category == 'Nano') {
			$result = $this->formatNanos($matches);
		} else {
			$result = $this->formatItems($matches);
		}

		if ($result === null) {
			$msg = "No {$category}s found that buff $skill.";
		} else {
			list($count, $blob) = $result;
			$blob = $this->text->make_chatcmd("See results on Auno.org", "/start $newUrl") . "\n\n" . $blob;
			$blob .= "\nSearch results provided by Auno.org";
			$msg = $this->text->makeBlob("WhatBuffs - $category $skill ($count)", $blob);
		}
		return $msg;
	}
	
	public function searchForSkill($skill) {
		// check for exact match first, in order to disambiguate
		// between Bow and Bow special attack 
		$results = $this->db->query("SELECT name FROM skills WHERE common = 1 AND name LIKE ?", $skill);
		if (count($results) == 1) {
			return $results;
		}
		
		$tmp = explode(" ", $skill);
		list($query, $params) = $this->util->generateQueryFromParams($tmp, 'name');
		
		return $this->db->query("SELECT name FROM skills WHERE common = 1 AND $query", $params);
	}
	
	public function formatItems($matches) {
		$items = array();
		forEach ($matches as $match) {
			$item = $this->itemsController->findById($match[1]);
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
			
			$blob .= $this->text->make_item($item->lowid, $item->highid, $item->highql, $item->name) . " ($ql)\n";
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
		forEach ($matches as $match) {
			$blob .= $this->text->make_chatcmd($match[2], "/tell <myname> nano $match[2]") . "\n";
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
			$msg = $this->getSearchResults($category, $row->name);
		} else {
			$blob = '';
			forEach ($data as $row) {
				$blob .= $this->text->make_chatcmd(ucfirst($row->name), "/tell <myname> whatbuffs $category $row->name") . "\n";
			}
			$msg = $this->text->makeBlob("WhatBuffs - Choose Skill", $blob);
		}
		
		return $msg;
	}
}

?>
