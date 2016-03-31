<?php

namespace Budabot\User\Modules;

/**
 * Authors: 
 *	- Tyrence (RK2)
 *	- Marebone (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'bank', 
 *		accessLevel = 'guild', 
 *		description = 'Browse and search the bank toons', 
 *		help        = 'bank.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'bank update',
 *		accessLevel = 'admin', 
 *		description = 'Reloads the bank database from the AO Items Assistant file', 
 *		help        = 'bank.txt'
 *	)
 */
class BankController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $db;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $settingManager;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'bank');
		
		$this->settingManager->add($this->moduleName, 'bank_file_location', 'Location of the AO Items Assistant csv dump file', 'edit', 'text', './modules/BANK_MODULE/import.csv');
		$this->settingManager->add($this->moduleName, 'max_bank_items', 'Number of items shown in search results', 'edit', 'number', '50', '20;50;100');
	}

	/**
	 * @HandlesCommand("bank")
	 * @Matches("/^bank browse$/i")
	 */
	public function bankBrowseCommand($message, $channel, $sender, $sendto, $args) {
		$blob = '';
		$data = $this->db->query("SELECT DISTINCT player FROM bank ORDER BY player ASC");
		forEach ($data as $row) {
			$character_link = $this->text->make_chatcmd($row->player, "/tell <myname> bank browse {$row->player}");
			$blob .= $character_link . "\n";
		}

		$msg = $this->text->make_blob('Bank Characters', $blob);
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("bank")
	 * @Matches("/^bank browse ([a-z0-9-]+)$/i")
	 */
	public function bankBrowsePlayerCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));

		$blob = '';
		$data = $this->db->query("SELECT DISTINCT container_id, container, player FROM bank WHERE player = ? ORDER BY container ASC", $name);
		if (count($data) > 0) {
			forEach ($data as $row) {
				$container_link = $this->text->make_chatcmd($row->container, "/tell <myname> bank browse {$row->player} {$row->container_id}");
				$blob .= "{$container_link}\n";
			}

			$msg = $this->text->make_blob("Containers for $name", $blob);
		} else {
			$msg = "Could not find bank character <highlight>$name<end>.";
		}
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("bank")
	 * @Matches("/^bank browse ([a-z0-9-]+) (\d+)$/i")
	 */
	public function bankBrowseContainerCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$containerId = $args[2];
		$limit = $this->settingManager->get('max_bank_items');

		$blob = '';
		$data = $this->db->query("SELECT * FROM bank WHERE player = ? AND container_id = ? ORDER BY name ASC, ql ASC LIMIT {$limit}", $name, $containerId);

		if (count($data) > 0) {
			forEach ($data as $row) {
				$item_link = $this->text->make_item($row->lowid, $row->highid, $row->ql, $row->name);
				$blob .= "{$item_link} ({$row->ql})\n";
			}

			$msg = $this->text->make_blob("Contents of $row->container", $blob);
		} else {
			$msg = "Could not find container with id <highlight>{$containerId}</highlight> on bank character <highlight>{$name}<end>.";
		}
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("bank")
	 * @Matches("/^bank search (.+)$/i")
	 */
	public function bankSearchCommand($message, $channel, $sender, $sendto, $args) {
		$search = htmlspecialchars_decode($args[1]);
		$words = explode(' ', $search);
		$limit = $this->settingManager->get('max_bank_items');
		
		list($where_sql, $params) = $this->util->generateQueryFromParams($words, 'name');

		$blob = '';
		$data = $this->db->query("SELECT * FROM bank WHERE {$where_sql} ORDER BY name ASC, ql ASC LIMIT {$limit}", $params);

		if (count($data) > 0) {
			forEach ($data as $row) {
				$item_link = $this->text->make_item($row->lowid, $row->highid, $row->ql, $row->name);
				$blob .= "{$item_link} ({$row->ql}) (<highlight>{$row->player}<end>, {$row->container})\n";
			}

			$msg = $this->text->make_blob("Bank Search Results for {$args[1]}", $blob);
		} else {
			$msg = "Could not find any search results for <highlight>{$args[1]}<end>.";
		}
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("bank update")
	 * @Matches("/^bank update$/i")
	 */
	public function bankUpdateCommand($message, $channel, $sender, $sendto, $args) {
		$lines = file($this->settingManager->get('bank_file_location'));

		if ($lines === false) {
			$msg = "Could not open file: '" . $this->settingManager->get('bank_file_location') . "'";
			$sendto->reply($msg);
			return;
		}

		//remove the header line
		array_shift($lines);

		$this->db->beginTransaction();
		$this->db->exec("DELETE FROM bank");

		forEach ($lines as $line) {
			// this is the order of columns in the CSV file (AOIA v1.1.3.0):
			// Item Name,QL,Character,Backpack,Location,LowID,HighID,ContainerID,Link
			list($name, $ql, $player, $container, $location, $lowId, $highId, $containerId) = str_getcsv($line);
			if ($location != 'Bank' && $location != 'Inventory') {
				continue;
			}
			if ($container == '') {
				$container = $location;
			}

			$sql = "INSERT INTO bank (name, lowid, highid, ql, player, container, container_id, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
			$this->db->exec($sql, $name, $lowId, $highId, $ql, $player, $container, $containerId, $location);
		}
		$this->db->commit();

		$msg = "The bank database has been updated.";
		$sendto->reply($msg);
	}
}
