<?php

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'whois',
 *		accessLevel = 'all', 
 *		description = 'Show player info and name history', 
 *		help        = 'whois.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'whoisall',
 *		accessLevel = 'all', 
 *		description = 'Show player info for all dimensions', 
 *		help        = 'whois.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'namehistory',
 *		accessLevel = 'all', 
 *		description = 'Show name history of a player', 
 *		help        = 'namehistory.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'lookup',
 *		accessLevel = 'all', 
 *		description = 'Find the charId for a player', 
 *		help        = 'lookup.txt'
 *	)
 */
class WhoisController {

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
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $alts;
	
	/** @Inject */
	public $playerManager;
	
	private $nameHistoryCache = array();
	
	/** @Setup */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "name_history");
	}
	
	/**
	 * @Event("1min")
	 * @Description("Save cache of names and charIds to database")
	 */
	public function saveCharIds($eventObj) {
		if (!empty($this->nameHistoryCache) && !$this->db->in_transaction()) {
			$this->db->begin_transaction();
			forEach ($this->nameHistoryCache as $entry) {
				list($charid, $name) = $entry;
				if ($this->db->get_type() == DB::SQLITE) {
					$this->db->exec("INSERT OR IGNORE INTO name_history (name, charid, dimension, dt) VALUES (?, ?, <dim>, ?)", $name, $charid, time());
				} else { // if ($this->db->get_type() == DB::MYSQL)
					$this->db->exec("INSERT IGNORE INTO name_history (name, charid, dimension, dt) VALUES (?, ?, <dim>, ?)", $name, $charid, time());
				}
			}
			$this->db->commit();

			$this->nameHistoryCache = array();
		}
	}
	
	/**
	 * @Event("allpackets")
	 * @Description("Records names and charIds")
	 */
	public function recordCharIds($eventObj) {
		$packet = $eventObj->packet;
		if (($packet->type == AOCP_CLIENT_NAME || $packet->type == AOCP_CLIENT_LOOKUP) && $this->util->isValidSender($packet->args[0])) {
			$this->nameHistoryCache []= $packet->args;
		}
	}

	/**
	 * @HandlesCommand("namehistory")
	 * @Matches("/^namehistory (.+)$/i")
	 */
	public function namehistoryCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$uid = $this->chatBot->get_uid($name);
		if (!$uid) {
			$msg = "<highlight>$name<end> does not exist.";
			$sendto->reply($msg);
			return;
		}

		$sql = "SELECT * FROM name_history WHERE charid = ? AND dimension = <dim> ORDER BY dt DESC";
		$data = $this->db->query($sql, $uid);
		$count = count($data);

		$blob = '';
		if ($count > 0) {
			forEach ($data as $row) {
				$blob .= "<green>{$row->name}<end> " . $this->util->date($row->dt) . "\n";
			}
			$msg = $this->text->make_blob("Name History for $name ($count)", $blob);
		} else {
			$msg = "No name history available.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("lookup")
	 * @Matches("/^lookup ([0-9]+)$/i")
	 */
	public function lookupIdCommand($message, $channel, $sender, $sendto, $args) {
		$charid = $args[1];
		$data = $this->db->query("SELECT * FROM name_history WHERE charid = ? AND dimension = <dim> ORDER BY dt DESC", $charid);
		$count = count($data);

		$blob = '';
		if ($count > 0) {
			forEach ($data as $row) {
				$link = $this->text->make_chatcmd($row->name, "/tell <myname> lookup $row->name");
				$blob .= "$link " . $this->util->date($row->dt) . "\n";
			}
			$msg = $this->text->make_blob("Name History for $charid ($count)", $blob);
		} else {
			$msg = "No history available for character id <highlight>$charid<end>.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("lookup")
	 * @Matches("/^lookup (.*)$/i")
	 */
	public function lookupNameCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));

		$data = $this->db->query("SELECT * FROM name_history WHERE name LIKE ? AND dimension = <dim> ORDER BY dt DESC", $name);
		$count = count($data);

		$blob = '';
		if ($count > 0) {
			forEach ($data as $row) {
				$link = $this->text->make_chatcmd($row->charid, "/tell <myname> lookup $row->charid");
				$blob .= "$link " . $this->util->date($row->dt) . "\n";
			}
			$msg = $this->text->make_blob("Character Ids for $name ($count)", $blob);
		} else {
			$msg = "No history available for character <highlight>$name<end>.";
		}

		$sendto->reply($msg);
	}
	
	public function getNameHistory($charid, $dimension) {
		$sql = "SELECT * FROM name_history WHERE charid = ? AND dimension = ? ORDER BY dt DESC";
		$data = $this->db->query($sql, $charid, $dimension);

		$blob = "<header2>Name History<end>\n\n";
		if (count($data) > 0) {
			forEach ($data as $row) {
				$blob .= "<green>{$row->name}<end> " . $this->util->date($row->dt) . "\n";
			}
		} else {
			$blob .= "No name history available\n";
		}

		return $blob;
	}
	
	/**
	 * @HandlesCommand("whois")
	 * @Matches("/^whois (.+)$/i")
	 */
	public function whoisNameCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$uid = $this->chatBot->get_uid($name);
		if ($uid) {
			$lookupNameLink = $this->text->make_chatcmd("Lookup", "/tell <myname> lookup $name");
			$lookupCharIdLink = $this->text->make_chatcmd("Lookup", "/tell <myname> lookup $uid");
			$whois = $this->playerManager->get_by_name($name);
			if ($whois === null) {
				$blob = "<orange>Note: Could not retrieve detailed info for character.<end>\n\n";
				$blob .= "Name: <highlight>{$name}<end> {$lookupNameLink}\n";
				$blob .= "Character ID: <highlight>{$uid}<end> {$lookupCharIdLink}\n\n";
				$blob .= "<pagebreak>" . $this->getNameHistory($uid, $this->chatBot->vars['dimension']);

				$msg = $this->text->make_blob("Basic Info for $name", $blob);
			} else {
				$blob = "Name: <highlight>{$whois->firstname} \"{$name}\" {$whois->lastname}<end> {$lookupNameLink}\n";
				if ($whois->guild) {
					$blob .= "Guild: <highlight>{$whois->guild} ({$whois->guild_id})<end>\n";
					$blob .= "Guild Rank: <highlight>{$whois->guild_rank} ({$whois->guild_rank_id})<end>\n";
				}
				$blob .= "Breed: <highlight>{$whois->breed}<end>\n";
				$blob .= "Gender: <highlight>{$whois->gender}<end>\n";
				$blob .= "Profession: <highlight>{$whois->profession} ({$whois->prof_title})<end>\n";
				$blob .= "Level: <highlight>{$whois->level}<end>\n";
				$blob .= "AI Level: <highlight>{$whois->ai_level} ({$whois->ai_rank})<end>\n";
				$blob .= "Faction: <highlight>{$whois->faction}<end>\n";
				$blob .= "Character ID: <highlight>{$whois->charid}<end> {$lookupCharIdLink}\n\n";

				$blob .= "Source: $whois->source\n\n";

				$blob .= "<pagebreak>" . $this->getNameHistory($uid, $this->chatBot->vars['dimension']);

				$blob .= "\n<pagebreak><header2>Options<end>\n\n";

				$blob .= $this->text->make_chatcmd('History', "/tell <myname> history $name") . "\n";
				$blob .= $this->text->make_chatcmd('Online Status', "/tell <myname> is $name") . "\n";
				if ($whois->guild_id != 0) {
					$blob .= $this->text->make_chatcmd('Whoisorg', "/tell <myname> whoisorg $whois->guild_id") . "\n";
					$blob .= $this->text->make_chatcmd('Orglist', "/tell <myname> orglist $whois->guild_id") . "\n";
				}

				$msg = $this->playerManager->get_info($whois) . " :: " . $this->text->make_blob("More Info", $blob, "Detailed Info for {$name}");

				$altInfo = $this->alts->get_alt_info($name);
				if (count($altInfo->alts) > 0) {
					$msg .= " :: " . $altInfo->get_alts_blob(false, true);
				}
			}
		} else {
			$msg = "Character <highlight>{$name}<end> does not exist.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("whoisall")
	 * @Matches("/^whoisall (.+)$/i")
	 */
	public function whoisallCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		for ($i = 1; $i <= 2; $i ++) {
			if ($i == 1) {
				$server = "Atlantean";
			} else if ($i == 2) {
				$server = "Rimor";
			}

			$whois = $this->playerManager->lookup($name, $i);
			if ($whois !== null) {
				$msg = $this->playerManager->get_info($whois);

				$blob = "Name: <highlight>{$whois->firstname} \"{$name}\" {$whois->lastname}<end>\n";
				if ($whois->guild) {
					$blob .= "Guild: <highlight>{$whois->guild} ({$whois->guild_id})<end>\n";
					$blob .= "Guild Rank: <highlight>{$whois->guild_rank} ({$whois->guild_rank_id})<end>\n";
				}
				$blob .= "Breed: <highlight>{$whois->breed}<end>\n";
				$blob .= "Gender: <highlight>{$whois->gender}<end>\n";
				$blob .= "Profession: <highlight>{$whois->profession} ({$whois->prof_title})<end>\n";
				$blob .= "Level: <highlight>{$whois->level}<end>\n";
				$blob .= "AI Level: <highlight>{$whois->ai_level} ({$whois->ai_rank})<end>\n";
				$blob .= "Faction: <highlight>{$whois->faction}<end>\n\n";

				$blob .= "Source: $whois->source\n\n";

				$blob .= "<pagebreak><header2>Options<end>\n\n";

				$blob .= $this->text->make_chatcmd("History", "/tell <myname> history {$name} {$i}") . "\n";

				$msg .= " :: ".$this->text->make_blob("More info", $blob, "Detailed Info for {$name}");
				$msg = "<highlight>Server $server:<end> ".$msg;
			} else {
				$msg = "Server $server: Character <highlight>{$name}<end> does not exist.";
			}

			$sendto->reply($msg);
		}
	}
}
