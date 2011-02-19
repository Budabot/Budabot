<?php

/*
`module` VARCHAR(50) NOT NULL
`regex` VARCHAR(255)
`file` VARCHAR(255) NOT NULL
`is_core` TINYINT NOT NULL
`cmd` VARCHAR(25) NOT NULL
`tell_status` INT DEFAULT 0
`tell_access_level` INT DEFAULT 0
`guild_status` INT DEFAULT 0
`guild_access_level` INT DEFAULT 0
`priv_status` INT DEFAULT 0
`priv_access_level` INT DEFAULT 0
`description` VARCHAR(50) NOT NULL DEFAULT ''
`verify` INT DEFAULT 1
*/

class Command {

	/**
	 * @name: register
	 * @description: Registers a command
	 */
	public static function register($module, $type, $filename, $command, $admin = 'all', $description = ''){
		$db = DB::get_instance();
		global $chatBot;

		$command = strtolower($command);
		$description = str_replace("'", "''", $description);
		$module = strtoupper($module);
		
		if (!$chatBot->processCommandArgs($type, $admin)) {
			Logger::log('ERROR', 'Core', "invalid args for $module:command($command)");
			return;
		}
		
		//Check if the file exists
		$actual_filename = Util::verify_filename($module . '/' . $filename);
		if ($actual_filename == '') {
			Logger::log('ERROR', 'Core', "Error in registering the File $filename for command $command. The file doesn't exists!");
			return;
		}

		for ($i = 0; $i < count($type); $i++) {
			Logger::log('debug', 'Core', "Adding Command to list:($command) File:($actual_filename) Admin:({$admin[$i]}) Type:({$type[$i]})");
			
			if ($chatBot->existing_commands[$type[$i]][$command] == true) {
				$db->exec("UPDATE cmdcfg_<myname> SET `module` = '$module', `verify` = 1, `file` = '$actual_filename', `description` = '$description' WHERE `cmd` = '$command' AND `type` = '{$type[$i]}'");
			} else {
				if ($chatBot->settings["default_module_status"] == 1) {
					$status = 1;
				} else {
					$status = 0;
				}
				$db->exec("INSERT INTO cmdcfg_<myname> (`module`, `type`, `file`, `cmd`, `admin`, `description`, `verify`, `cmdevent`, `status`) VALUES ('$module', '{$type[$i]}', '$actual_filename', '$command', '{$admin[$i]}', '$description', 1, 'cmd', '$status')");
			}
		}
	}

	/**
	 * @name: activate
	 * @description: Activates a command
	 */
	public static function activate($channel, $filename, $command, $admin = 'all') {
		global $chatBot;
		$db = DB::get_instance();
		
		$command = strtolower($command);
		$admin = strtolower($admin);
		$channel = strtolower($channel);

	  	Logger::log('DEBUG', 'Core', "Activate Command:($command) Admin Type:($admin) File:($filename) Channel:($channel)");

		//Check if the file exists
		$actual_filename = Util::verify_filename($filename);
		if ($actual_filename == '') {
			Logger::log('ERROR', 'Core', "Error in activating the File $filename for command $command. The file doesn't exists!");
			return;
		}

		//Check if the admin status exists
		if (!is_numeric($admin)) {
			if ($admin == "leader") {
				$admin = 1;
			} else if ($admin == "raidleader" || $admin == "rl") {
				$admin = 2;
			} else if ($admin == "mod" || $admin == "moderator") {
				$admin = 3;
			} else if ($admin == "admin") {
				$admin = 4;
			} else if ($admin != "all" && $admin != "guild" && $admin != "guildadmin") {
				Logger::log('ERROR', 'Core', "Error in registering command $command for channel $channel. Unknown Admintype: $admin. Admintype is set to all now.");
				return;
			}
		}

		switch ($channel) {
			case "msg":
				$chatBot->tellCmds[$command]["filename"] = $actual_filename;
				$chatBot->tellCmds[$command]["admin level"] = $admin;
				break;
			case "priv":
				$chatBot->privCmds[$command]["filename"] = $actual_filename;
				$chatBot->privCmds[$command]["admin level"] = $admin;
				break;
			case "guild":
				$chatBot->guildCmds[$command]["filename"] = $actual_filename;
				$chatBot->guildCmds[$command]["admin level"] = $admin;
				break;
		}
	}
	
	/**
	 * @name: deactivate
	 * @description: Deactivates a command
	 */
	public static function deactivate($channel, $filename, $command) {
		global $chatBot;
  		$db = DB::get_instance();

		$command = strtolower($command);
		$channel = strtolower($channel);

	  	Logger::log('DEBUG', 'Core', "Deactivate Command:($command) File:($filename) Channel:($channel)");

		switch ($channel){
			case "msg":
				unset($chatBot->tellCmds[$command]);
				break;
			case "priv":
				unset($chatBot->privCmds[$command]);
				break;
			case "guild":
				unset($chatBot->guildCmds[$command]);
				break;
		}
	}
	
	public static function update_status($type, $module, $cmd, $status) {
		$db = DB::get_instance();
		
		if ($type == 'all' || $type == '' || $type == null) {
			$type_sql = '';
		} else {
			$type_sql = "AND `type` = '$type'";
		}
		
		if ($cmd == '' || $cmd == null) {
			$cmd_sql = '';
		} else {
			$cmd_sql = "AND `cmd` = '$cmd'";
		}
		
		if ($module == '' || $module == null) {
			$module_sql = '';
		} else {
			$module_sql = "AND `module` = '$module'";
		}
	
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd' $module_sql $cmd_sql $type_sql");
		if ($db->numrows == 0) {
			return 0;
		}
		
		$data = $db->fObject('all');
		forEach ($data as $row) {
			if ($status == 1) {
				Command::activate($row->type, $row->filename, $row->cmd, $row->admin);
			} else if ($status == 0) {
				Command::deactivate($row->type, $row->filename, $row->cmd);
			}
		}
		
		return $db->exec("UPDATE cmdcfg_<myname> SET status = '$status' WHERE `cmdevent` = 'cmd' $module_sql $cmd_sql $type_sql");
	}

	/**
	 * @name: loadCommands
	 * @description: Loads the active commands into memory to activate them
	 */
	public static function loadCommands() {
	  	$db = DB::get_instance();

		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `status` = '1' AND `cmdevent` = 'cmd'");
		$data = $db->fObject("all");
		forEach ($data as $row) {
			Command::activate($row->type, $row->file, $row->cmd, $row->admin);
		}
	}
}

?>