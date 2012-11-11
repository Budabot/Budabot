<?php
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
 *		description = 'Browse and search the Org Bank', 
 *		help        = 'bank.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'updatebank',
 *		accessLevel = 'admin', 
 *		description = 'Reloads the bank database from the AO Items Assistant file', 
 *		help        = 'updatebank.txt'
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
	public $settingManager;

	/**
	 * @Setting("bank_file_location")
	 * @Description("Location of the AO Items Assistant csv dump file")
	 * @Visibility("edit")
	 * @Type("text")
	 */
	public $defaultBankFileLocation = "./modules/BANK_MODULE/import.csv";

	/**
	 * @Setting("max_bank_items")
	 * @Description("Number of items shown in search results")
	 * @Visibility("edit")
	 * @Type("number")
	 * @Options("30;40;50;60")
	 */
	public $defaultMaxBankItems = "200";

	/**
	 * Lists all known org banks.
	 *
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
	 * Lists player's all containers from his org bank.
	 *
	 * @HandlesCommand("bank")
	 * @Matches("/^bank browse ([a-z0-9-]+)$/i")
	 */
	public function bankBrowsePlayerCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));

		$blob = '';
		$data = $this->db->query("SELECT DISTINCT container, player FROM bank WHERE player = ? ORDER BY container ASC", $name);
		if (count($data) > 0) {
			forEach ($data as $row) {
				$container_link = $this->text->make_chatcmd($row->container, "/tell <myname> bank browse {$row->player} {$row->container}");
				$blob .= "{$container_link}\n";
			}

			$msg = $this->text->make_blob("Backpacks for $name", $blob);
		} else {
			$msg = "Could not find a bank character named $name";
		}
		$sendto->reply($msg);
	}

	/**
	 * Lists contents of a container from player's org bank.
	 *
	 * @HandlesCommand("bank")
	 * @Matches("/^bank browse ([a-z0-9-]+) (.+)$/i")
	 */
	public function bankBrowseContainerCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$pack = htmlspecialchars_decode($args[2]);
		$limit = $this->settingManager->get('max_bank_items');

		$blob = '';
		$data = $this->db->query("SELECT * FROM bank WHERE player = ? AND container = ? ORDER BY name ASC, ql ASC LIMIT {$limit}", $name, $pack);

		if (count($data) > 0) {
			forEach ($data as $row) {
				$item_link = $this->text->make_item($row->lowid, $row->highid, $row->ql, $row->name);
				$blob .= "{$item_link} ({$row->ql})\n";
			}

			$msg = $this->text->make_blob("Contents of $pack", $blob);
		} else {
			$msg = "Could not find a pack named '{$pack}' on a bank character named '{$name}'";
		}
		$sendto->reply($msg);
	}

	/**
	 * Searches given words from org banks.
	 *
	 * @HandlesCommand("bank")
	 * @Matches("/^bank search (.+)$/i")
	 */
	public function bankSearchCommand($message, $channel, $sender, $sendto, $args) {
		$search = explode(' ', $args[1]);
		$limit = $this->settingManager->get('max_bank_items');

		$where_sql = '';
		forEach ($search as $word) {
			$word = str_replace("'", "''", $word);
			$where_sql .= " AND name LIKE '%{$word}%'";
		}

		$blob = '';
		$data = $this->db->query("SELECT * FROM bank WHERE 1 = 1 {$where_sql} ORDER BY name ASC, ql ASC LIMIT {$limit}");

		if (count($data) > 0) {
			forEach ($data as $row) {
				$item_link = $this->text->make_item($row->lowid, $row->highid, $row->ql, $row->name);
				$blob .= "{$item_link} ({$row->ql}) (<green>{$row->player}<end>, {$row->container})\n";
			}

			$msg = $this->text->make_blob("Bank Search Results for {$args[1]}", $blob);
		} else {
			$msg = "Could not find any bank items when searching for '{$args[1]}'";
		}
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("updatebank")
	 */
	public function updatebankCommand($message, $channel, $sender, $sendto, $args) {
		$lines = file($this->settingManager->get('bank_file_location'));

		if ($lines === false) {
			$msg = "Could not open file: '" . $this->settingManager->get('bank_file_location') . "'";
			$sendto->reply($msg);
			return;
		}

		//remove the header line
		array_shift($lines);

		$this->db->begin_transaction();
		$this->db->exec("DROP TABLE IF EXISTS bank");
		$this->db->exec("CREATE TABLE bank (name varchar(150), lowid int, highid int, ql int, player VARCHAR(20), container VARCHAR(150), container_id INT, location VARCHAR(150))");

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
