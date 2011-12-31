<?php

class Subcommand extends Annotation {

	/** @Inject */
	public $db;

	/**
	 * @name: register
	 * @description: Registers a subcommand
	 */
	public function register($module, $channel, $filename, $command, $admin = 'all', $parent_command, $description = 'none', $help = '') {
		$chatBot = Registry::getInstance('chatBot');

		$command = strtolower($command);
		$module = strtoupper($module);

		if (!$chatBot->processCommandArgs($channel, $admin)) {
			Logger::log('ERROR', 'Subcommand', "Invalid args for $module:subcommand($command)");
			return;
		}

		if (preg_match("/\\.php$/i", $filename)) {
			$actual_filename = Util::verify_filename($module . '/' . $filename);
			if ($actual_filename == '') {
				Logger::log('ERROR', 'Subcommand', "Error in registering the file $filename for Subcommand $command. The file doesn't exist!");
				return;
			}
		} else {
			list($name, $method) = explode(".", $filename);
			$instance = Registry::getInstance($name);
			if ($instance === null) {
				Logger::log('ERROR', 'Command', "Error registering method $filename for subcommand $command.  Could not find instance '$name'.");
				return;
			}
			$actual_filename = $filename;
		}

		if ($chatBot->vars['default_module_status'] == 1) {
			$status = 1;
		} else {
			$status = 0;
		}

		for ($i = 0; $i < count($channel); $i++) {
			Logger::log('debug', 'Subcommand', "Adding Subcommand to list:($command) File:($actual_filename) Admin:($admin) Channel:({$channel[$i]})");

			if ($chatBot->existing_subcmds[$channel[$i]][$command] == true) {
				$sql = "UPDATE cmdcfg_<myname> SET `module` = ?, `verify` = ?, `file` = ?, `description` = ?, `dependson` = ?, `help` = ? WHERE `cmd` = ? AND `type` = ?";
				$this->db->exec($sql, $module, '1', $actual_filename, $description, $parent_command, $help, $command, $channel[$i]);
			} else {
				$sql = "INSERT INTO cmdcfg_<myname> (`module`, `type`, `file`, `cmd`, `admin`, `description`, `verify`, `cmdevent`, `dependson`, `status`, `help`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
				$this->db->exec($sql, $module, $channel[$i], $actual_filename, $command, $admin[$i], $description, '1', 'subcmd', $parent_command, $status, $help);
			}
		}
	}

	/**
	 * @name: loadSubcommands
	 * @description: Loads the active subcommands into memory and activates them
	 */
	public function loadSubcommands() {
		Logger::log('DEBUG', 'Subcommand', "Loading enabled subcommands");
	
	  	$chatBot = Registry::getInstance('chatBot');

		$data = $this->db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'subcmd' AND `status` = 1");
		forEach ($data as $row) {
			$chatBot->subcommands[$row->dependson] []= $row;
		}
	}
}

?>
