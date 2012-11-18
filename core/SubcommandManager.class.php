<?php

/**
 * @Instance
 */
class SubcommandManager {

	/** @Inject */
	public $db;

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $util;

	/** @Logger */
	public $logger;

	/**
	 * @name: register
	 * @description: Registers a subcommand
	 */
	public function register($module, $channel, $filename, $command, $admin, $parent_command, $description = 'none', $help = '', $defaultStatus = null) {
		$command = strtolower($command);
		$module = strtoupper($module);

		if (!$this->chatBot->processCommandArgs($channel, $admin)) {
			$this->logger->log('ERROR', "Invalid args for $module:subcommand($command)");
			return;
		}

		list($name, $method) = explode(".", $filename);
		if (!Registry::instanceExists($name)) {
			$this->logger->log('ERROR', "Error registering method $filename for subcommand $command.  Could not find instance '$name'.");
			return;
		}

		if ($defaultStatus === null) {
			if ($this->chatBot->vars['default_module_status'] == 1) {
				$status = 1;
			} else {
				$status = 0;
			}
		} else {
			$status = $defaultStatus;
		}

		for ($i = 0; $i < count($channel); $i++) {
			$this->logger->log('DEBUG', "Adding Subcommand to list:($command) File:($filename) Admin:($admin) Channel:({$channel[$i]})");

			if ($this->chatBot->existing_subcmds[$channel[$i]][$command] == true) {
				$sql = "UPDATE cmdcfg_<myname> SET `module` = ?, `verify` = ?, `file` = ?, `description` = ?, `dependson` = ?, `help` = ? WHERE `cmd` = ? AND `type` = ?";
				$this->db->exec($sql, $module, '1', $filename, $description, $parent_command, $help, $command, $channel[$i]);
			} else {
				$sql = "INSERT INTO cmdcfg_<myname> (`module`, `type`, `file`, `cmd`, `admin`, `description`, `verify`, `cmdevent`, `dependson`, `status`, `help`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
				$this->db->exec($sql, $module, $channel[$i], $filename, $command, $admin[$i], $description, '1', 'subcmd', $parent_command, $status, $help);
			}
		}
	}

	/**
	 * @name: loadSubcommands
	 * @description: Loads the active subcommands into memory and activates them
	 */
	public function loadSubcommands() {
		$this->logger->log('DEBUG', "Loading enabled subcommands");

		$this->subcommands = array();

		$data = $this->db->query("SELECT * FROM cmdcfg_<myname> WHERE `status` = '1' AND `cmdevent` = 'subcmd'");
		forEach ($data as $row) {
			$this->subcommands[$row->dependson] []= $row;
		}
	}
}

?>
