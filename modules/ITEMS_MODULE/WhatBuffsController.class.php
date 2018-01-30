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
	public $commandAlias;
	
	/** @Inject */
	public $itemsController;
	
	/** @Logger */
	public $logger;
	
	/** @Setup */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "item_buffs");
		$this->db->loadSQLFile($this->moduleName, "skills");
		$this->db->loadSQLFile($this->moduleName, "item_types");
	}

	/**
	 * @HandlesCommand("whatbuffs")
	 * @Matches("/^whatbuffs$/i")
	 */
	public function whatbuffsCommand($message, $channel, $sender, $sendto, $args) {
		$blob = '';
		$data = $this->db->query("SELECT DISTINCT item_type FROM item_types ORDER BY item_type ASC");
		forEach ($data as $row) {
			$blob .= $this->text->makeChatcmd($row->item_type, "/tell <myname> whatbuffs $row->item_type") . "\n";
		}
		$blob .= "\nItem Extraction Info provided by Unk";
		$msg = $this->text->makeBlob("WhatBuffs - Choose Type", $blob);
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("whatbuffs")
	 * @Matches("/^whatbuffs ([a-z]+) (.+)$/i")
	 */
	public function whatbuffs3Command($message, $channel, $sender, $sendto, $args) {
		$type = $args[1];
		$skill = $args[2];

		if ($this->verifySlot($type)) {
			$msg = $this->showSearchResults($type, $skill);
		} else {
			$msg = "Could not find any items of type <highlight>$type<end>.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("whatbuffs")
	 * @Matches("/^whatbuffs ([a-z]+)$/i")
	 */
	public function whatbuffs2Command($message, $channel, $sender, $sendto, $args) {
		$type = ucfirst(strtolower($args[1]));
		
		if ($this->verifySlot($type)) {
			$sql = "
				SELECT s.name AS skill, COUNT(1) AS num
				FROM aodb
				JOIN item_types i ON aodb.highid = i.item_id
				JOIN item_buffs b ON aodb.highid = b.item_id
				JOIN skills s ON b.attribute_id = s.id
				WHERE i.item_type = ?
				GROUP BY skill
				HAVING num > 0
				ORDER BY skill ASC";
			$data = $this->db->query($sql, $type);
			$blob = '';
			forEach ($data as $row) {
				$blob .= $this->text->makeChatcmd(ucfirst($row->skill), "/tell <myname> whatbuffs $type $row->skill") . " ($row->num)\n";
			}
			$blob .= "\nItem Extraction Info provided by Unk";
			$msg = $this->text->makeBlob("WhatBuffs - Choose Skill", $blob);
		} else {
			$msg = "Could not find any items of type <highlight>$type<end>.";
		}
		$sendto->reply($msg);
	}
	
	public function getSearchResults($category, $skill) {
		$sql = "
			SELECT aodb.*, b.amount
			FROM aodb
			JOIN item_types i ON aodb.highid = i.item_id
			JOIN item_buffs b ON aodb.highid = b.item_id
			JOIN skills s ON b.attribute_id = s.id
			WHERE i.item_type = ? AND s.id = ?
			ORDER BY amount DESC";
		$data = $this->db->query($sql, $category, $skill->id);

		$result = $this->formatItems($data);

		if ($result === null) {
			$msg = "No items found of type <highlight>$category<end> that buff <highlight>$skill->name<end>.";
		} else {
			list($count, $blob) = $result;
			//$newUrl = "https://aoitems.com/search/acriteria:2-$skillId-1/";
			//$blob = $this->text->makeChatcmd("See results on aoitems.com", "/start $newUrl") . "\n\n" . $blob;
			$blob .= "\nItem Extraction Info provided by Unk";
			$msg = $this->text->makeBlob("WhatBuffs - $category $skill->name ($count)", $blob);
		}
		return $msg;
	}

	public function verifySlot($type) {
		$type = ucfirst(strtolower($type));
		$row = $this->db->queryRow("SELECT 1 FROM item_types WHERE item_type = ? LIMIT 1", $type);
		return $row !== null;
	}
	
	public function searchForSkill($skill) {
		// check for exact match first, in order to disambiguate
		// between Bow and Bow special attack 
		$results = $this->db->query("SELECT DISTINCT id, name FROM skills WHERE name LIKE ?", $skill);
		if (count($results) == 1) {
			return $results;
		}
		
		$tmp = explode(" ", $skill);
		list($query, $params) = $this->util->generateQueryFromParams($tmp, 'name');
		
		return $this->db->query("SELECT DISTINCT id, name FROM skills WHERE $query", $params);
	}
	
	public function formatItems($items) {
		$blob = '';
		forEach ($items as $item) {
			$blob .= $this->text->makeItem($item->lowid, $item->highid, $item->highql, $item->name) . " ($item->amount)\n";
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
			$blob .= $this->text->makeChatcmd($row->item, "/tell <myname> nano $row->item") . " ($item->amount)\n";
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
			$msg = $this->getSearchResults($category, $row);
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
