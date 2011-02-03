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
	 * @name: activate
	 * @description: Activates a command
	 */
	function activate($channel, $filename, $command, $admin = 'all') {
		global $chatBot;
		$db = db::get_instance();
		
		$command = strtolower($command);
		$admin = strtolower($admin);
		$channel = strtolower($channel);

	  	Logger::log('debug', 'Core', "Activate Command:($command) Admin Type:($admin) File:($filename) Channel:($channel)");

		//Check if the file exists
		$actual_filename = bot::verifyFilename($filename);
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
	 * @description: Deactivates an command
	 */
	function deactivate($channel, $filename, $command) {
		global $chatBot;
  		$db = db::get_instance();

		$command = strtolower($command);
		$channel = strtolower($channel);

	  	Logger::log('debug', 'Core', "Deactivate Command:($command) File:($filename) Channel:($channel)");

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
}

?>