<?php

namespace Budabot\User\Modules;

use Budabot\Core\DB;

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
 *		description = 'Show character info, online status, and name history', 
 *		help        = 'whois.txt',
 *		alias       = 'is'
 *	)
 *	@DefineCommand(
 *		command     = 'lookup',
 *		accessLevel = 'all', 
 *		description = 'Find the charId for a character', 
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
	public $altsController;
	
	/** @Inject */
	public $playerManager;
	
	/** @Inject */
	public $buddylistManager;
	
	private $nameHistoryCache = array();
	
	private $replyInfo = null;
	
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
	
	public function getNameHistory($charid, $rk_num) {
		$sql = "SELECT * FROM name_history WHERE charid = ? AND dimension = ? ORDER BY dt DESC";
		$data = $this->db->query($sql, $charid, $rk_num);

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
			$online = $this->buddylistManager->is_online($name);
			if ($online === null) {
				$this->replyInfo['charname'] = $name;
				$this->replyInfo['sendto'] = $sendto;
				$this->buddylistManager->add($name, 'is_online');
			} else {
				$sendto->reply($this->getOutput($name, $online));
			}
		} else {
			$sendto->reply("Character <highlight>{$name}<end> does not exist.");
		}
	}
	
	public function getOutput($name, $online) {
		$lookupNameLink = $this->text->make_chatcmd("Lookup", "/tell <myname> lookup $name");
		$lookupCharIdLink = $this->text->make_chatcmd("Lookup", "/tell <myname> lookup $uid");
		$whois = $this->playerManager->get_by_name($name);
		if ($whois === null) {
			$blob = "<orange>Note: Could not retrieve detailed info for character.<end>\n\n";
			$blob .= "Name: <highlight>{$name}<end> {$lookupNameLink}\n";
			$blob .= "Character ID: <highlight>{$uid}<end> {$lookupCharIdLink}\n\n";
			$blob .= $this->getNameHistory($uid, $this->chatBot->vars['dimension']);

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
			$blob .= "Status: ";
			if ($online) {
				$blob .= "<green>Online<end>\n";
			} else {
				$blob .= "<red>Offline<end>\n";
			}
			$blob .= "Character ID: <highlight>{$whois->charid}<end> {$lookupCharIdLink}\n\n";

			$blob .= "Source: $whois->source\n\n";

			$blob .= $this->getNameHistory($uid, $this->chatBot->vars['dimension']);

			$msg = $this->playerManager->get_info($whois);
			if ($online) {
				$msg .= " :: <green>Online<end>";
			} else {
				$msg .= " :: <red>Offline<end>";
			}
			$msg .= " :: " . $this->text->make_blob("More Info", $blob, "Detailed Info for {$name}");

			$altInfo = $this->altsController->get_alt_info($name);
			if (count($altInfo->alts) > 0) {
				$msg .= " :: " . $altInfo->get_alts_blob(false, true);
			}
		}
		return $msg;
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Gets online status of character")
	 */
	public function logonEvent($eventObj) {
		$name = $eventObj->sender;
		if ($this->replyInfo !== null && $name == $this->replyInfo['charname']) {
			$this->replyInfo['sendto']->reply($this->getOutput($name, 1));
			$this->buddylistManager->remove($name, 'is_online');
			$this->replyInfo = null;
		}
	}
	
	/**
	 * @Event("logOff")
	 * @Description("Gets offline status of character")
	 */
	public function logoffEvent($eventObj) {
		$name = $eventObj->sender;
		if ($this->replyInfo !== null && $name == $this->replyInfo['charname']) {
			$this->replyInfo['sendto']->reply($this->getOutput($name, 0));
			$this->buddylistManager->remove($name, 'is_online');
			$this->replyInfo = null;
		}
	}
}
