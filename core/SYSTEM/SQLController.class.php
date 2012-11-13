<?php

require_once './lib/ReverseFileReader.class.php';

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command       = 'loadsql',
 *		accessLevel   = 'mod',
 *		description   = 'Manually reload an sql file',
 *		help          = 'loadsql.txt'
 *	)
 */
class SQLController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $accessManager;
	
	/** @Inject */
	public $db;

	/** @Inject */
	public $commandManager;

	/** @Inject */
	public $text;

	/**
	 * @Setup
	 * This handler is called on bot startup.
	 */
	public function setup() {
		$name = 'SQLController';
		// don't register, only activate these commands to prevent them from appearing in !config
		forEach (array('msg', 'priv', 'guild') as $channel) {
			$this->commandManager->activate($channel, "$name.executesqlCommand", "executesql", "admin");
			$this->commandManager->activate($channel, "$name.querysqlCommand", "querysql", "admin");
		}
	}
	
	/**
	 * This command handler executes a SQL statement.
	 * Note: This handler has not been not registered, only activated.
	 *
	 * @Matches("/^executesql (.*)$/i")
	 */
	public function executesqlCommand($message, $channel, $sender, $sendto, $args) {
		if (!$this->accessManager->checkAccess($sender, 'superadmin')) {
			$msg = "This command may only be used by the super administrator.";
			$sendto->reply($msg);
			return;
		}

		$sql = htmlspecialchars_decode($args[1]);

		try {
			$num_rows = $this->db->exec($sql);
			$msg = "$num_rows rows affected.";
		} catch (SQLException $e) {
			$msg = $this->text->make_blob("SQL Error", $e->getMessage());
		}
		$sendto->reply($msg);
	}

	/**
	 * This command handler executes a SQL query.
	 * Note: This handler has not been not registered, only activated.
	 *
	 * @Matches("/^querysql (.*)$/si")
	 */
	public function querysqlCommand($message, $channel, $sender, $sendto, $args) {
		if (!$this->accessManager->checkAccess($sender, 'superadmin')) {
			$msg = "This command may only be used by the super administrator.";
			$sendto->reply($msg);
			return;
		}

		$sql = htmlspecialchars_decode($args[1]);

		try {
			$data = $this->db->query($sql);
			$count = count($data);

			$msg = $this->text->make_blob("Results ($count)", print_r($data, true));
		} catch (SQLException $e) {
			$msg = $this->text->make_blob("SQL Error", $e->getMessage());
		}
		$sendto->reply($msg);
	}

	/**
	 * This command handler manually reload an sql file.
	 *
	 * @HandlesCommand("loadsql")
	 * @Matches("/^loadsql (.*) (.*)$/i")
	 */
	public function loadsqlCommand($message, $channel, $sender, $sendto, $args) {
		$module = strtoupper($args[1]);
		$name = strtolower($args[2]);
	
		$this->db->begin_transaction();
	
		$msg = $this->db->loadSQLFile($module, $name, true);
	
		$this->db->commit();
	
		$sendto->reply($msg);
	}
}
