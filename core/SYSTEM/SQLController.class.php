<?php
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
 *	@DefineCommand(
 *		command       = 'querysql',
 *		accessLevel   = 'superadmin',
 *		description   = 'Run an SQL query and see the results'
 *	)
 *	@DefineCommand(
 *		command       = 'executesql',
 *		accessLevel   = 'superadmin',
 *		description   = 'Execute an SQL statement'
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

	}
	
	/**
	 * @HandlesCommand("executesql")
	 * @Matches("/^executesql (.*)$/i")
	 */
	public function executesqlCommand($message, $channel, $sender, $sendto, $args) {
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
	 * @HandlesCommand("querysql")
	 * @Matches("/^querysql (.*)$/si")
	 */
	public function querysqlCommand($message, $channel, $sender, $sendto, $args) {
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
