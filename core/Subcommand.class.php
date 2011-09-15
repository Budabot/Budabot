<?php

class Subcommand {

	/**
	 * @name: register
	 * @description: Registers a subcommand
	 */
	public static function register($module, $channel, $filename, $command, $admin = 'all', $parent_command, $description = 'none', $help = '') {
		$db = DB::get_instance();
		global $chatBot;

		$command = strtolower($command);
		$description = str_replace("'", "''", $description);
		$module = strtoupper($module);

		if (!$chatBot->processCommandArgs($channel, $admin)) {
			Logger::log('ERROR', 'Subcommand', "Invalid args for $module:subcommand($command)");
			return;
		}

		//Check if the file exists
		$actual_filename = Util::verify_filename($module . '/' . $filename);
		if ($actual_filename == '') {
			Logger::log('ERROR', 'Subcommand', "Error in registering the file $filename for Subcommand $command. The file doesn't exist!");
			return;
		}
		
		if ($chatBot->vars['default_module_status'] == 1) {
			$status = 1;
		} else {
			$status = 0;
		}

		for ($i = 0; $i < count($channel); $i++) {
			Logger::log('debug', 'Subcommand', "Adding Subcommand to list:($command) File:($actual_filename) Admin:($admin) Channel:({$channel[$i]})");
			
			if ($chatBot->existing_subcmds[$channel[$i]][$command] == true) {
				$db->exec("UPDATE cmdcfg_<myname> SET `module` = '$module', `verify` = 1, `file` = '$actual_filename', `description` = '$description', `dependson` = '$parent_command', `help` = '{$help}' WHERE `cmd` = '$command' AND `type` = '{$channel[$i]}'");
			} else {
				$db->exec("INSERT INTO cmdcfg_<myname> (`module`, `type`, `file`, `cmd`, `admin`, `description`, `verify`, `cmdevent`, `dependson`, `status`, `help`) VALUES ('$module', '{$channel[$i]}', '$actual_filename', '$command', '{$admin[$i]}', '$description', 1, 'subcmd', '$parent_command', $status, '{$help}')");
			}
		}
	}

	/**
	 * @name: loadSubcommands
	 * @description: Loads the active subcommands into memory and activates them
	 */
	public static function loadSubcommands() {
		Logger::log('DEBUG', 'Subcommand', "Loading enabled subcommands");
	
	  	$db = DB::get_instance();
		global $chatBot;

		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'subcmd' AND `status` = 1");
		$data = $db->fObject("all");
		forEach ($data as $row) {
			$chatBot->subcommands[$row->dependson] []= $row;
		}
	}
}

?>
