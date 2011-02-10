<?php

class Subcommand {

	/**
	 * @name: register
	 * @description: Registers a subcommand
	 */
	public static function register($module, $type, $filename, $command, $admin = 'all', $dependson, $description = 'none') {
		$db = DB::get_instance();
		global $chatBot;

		$command = strtolower($command);
		$description = str_replace("'", "''", $description);
		$module = strtoupper($module);

		Logger::log('debug', 'Core', "Adding $module:subcommand($command) File:($filename) Admin:($admin) Type:($type)");

		if (!$chatBot->processCommandArgs($type, $admin)) {
			Logger::log('ERROR', 'Core', "Invalid args for $module:subcommand($command)");
			return;
		}

		//Check if the file exists
		$actual_filename = $chatBot->verifyFilename($module . '/' . $filename);
		if ($actual_filename == '') {
			Logger::log('ERROR', 'Core', "Error in registering the file $filename for Subcommand $command. The file doesn't exists!");
			return;
		}

		for ($i = 0; $i < count($type); $i++) {
			Logger::log('debug', 'Core', "Adding Subcommand to list:($command) File:($actual_filename) Admin:($admin) Type:({$type[$i]})");
			
			//Check if the admin status exists
			if (!is_numeric($admin[$i])) {
				if ($admin[$i] == "leader") {
					$admin[$i] = 1;
				} else if ($admin[$i] == "raidleader" || $admin[$i] == "rl") {
					$admin = 2;
				} else if ($admin[$i] == "mod" || $admin[$i] == "moderator") {
					$admin[$i] = 3;
				} else if ($admin[$i] == "admin") {
					$admin[$i] = 4;
				} else if ($admin[$i] != "all" && $admin[$i] != "guild" && $admin[$i] != "guildadmin") {
					Logger::log('ERROR', 'Core', "Error in registrating $module:subcommand($command) for channel {$type[$i]}. Reason Unknown Admintype: {$admin[$i]}. Admintype is set to all now.");
					$admin[$i] = "all";
				}
			}

			if ($chatBot->existing_subcmds[$type[$i]][$command] == true) {
				$db->exec("UPDATE cmdcfg_<myname> SET `module` = '$module', `verify` = 1, `file` = '$actual_filename', `description` = '$description', `dependson` = '$dependson' WHERE `cmd` = '$command' AND `type` = '{$type[$i]}'");
			} else {
				$db->exec("INSERT INTO cmdcfg_<myname> (`module`, `type`, `file`, `cmd`, `admin`, `description`, `verify`, `cmdevent`, `dependson`) VALUES ('$module', '{$type[$i]}', '$actual_filename', '$command', '{$admin[$i]}', '$description', 1, 'subcmd', '$dependson')");
			}
		}
	}

	/**
	 * @name: loadSubcommands
	 * @description: Loads the active subcommands into memory and activates them
	 */
	public static function loadSubcommands() {
	  	$db = DB::get_instance();
		global $chatBot;

		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'subcmd'");
		$data = $db->fObject("all");
		forEach ($data as $row) {
			$chatBot->subcommands[$row->file][$row->type]["cmd"] = $row->cmd;
			$chatBot->subcommands[$row->file][$row->type]["admin"] = $row->admin;
		}
	}
}

?>
