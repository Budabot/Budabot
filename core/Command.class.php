<?php

class Command {

	/**
	 * @name: register
	 * @description: Registers a command
	 */
	public static function register($module, $channel, $filename, $command, $admin, $description = '', $help = ''){
		$db = DB::get_instance();
		global $chatBot;

		$command = strtolower($command);
		$module = strtoupper($module);
		
		if (!$chatBot->processCommandArgs($channel, $admin)) {
			Logger::log('ERROR', 'Command', "Invalid args for $module:command($command). Command not registered.");
			return;
		}
		
		//Check if the file exists
		$actual_filename = Util::verify_filename($module . '/' . $filename);
		if ($actual_filename == '') {
			Logger::log('ERROR', 'Command', "Error registering file $filename for command $command. The file doesn't exist!");
			return;
		}
		
		if ($chatBot->vars['default_module_status'] == 1) {
			$status = 1;
		} else {
			$status = 0;
		}

		for ($i = 0; $i < count($channel); $i++) {
			Logger::log('debug', 'Command', "Adding Command to list:($command) File:($actual_filename) Admin:({$admin[$i]}) Channel:({$channel[$i]})");
			
			if (isset($chatBot->existing_commands[$channel[$i]][$command])) {
				$sql = "UPDATE cmdcfg_<myname> SET `module` = ?, `verify` = ?, `file` = ?, `description` = ?, `help` = ? WHERE `cmd` = ? AND `type` = ?";
				$db->exec($sql, $module, '1', $actual_filename, $description, $help, $command, $channel[$i]);
			} else {
				$sql = "INSERT INTO cmdcfg_<myname> (`module`, `type`, `file`, `cmd`, `admin`, `description`, `verify`, `cmdevent`, `status`, `help`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
				$db->exec($sql, $module, $channel[$i], $actual_filename, $command, $admin[$i], $description, '1', 'cmd', $status, $help);
			}
		}
	}

	/**
	 * @name: activate
	 * @description: Activates a command
	 */
	public static function activate($channel, $filename, $command, $admin = 'all') {
		global $chatBot;
		
		$command = strtolower($command);
		$admin = strtolower($admin);
		$channel = strtolower($channel);

	  	Logger::log('DEBUG', 'Command', "Activate Command:($command) Admin Type:($admin) File:($filename) Channel:($channel)");

		//Check if the file exists
		$actual_filename = Util::verify_filename($filename);
		if ($actual_filename == '') {
			Logger::log('ERROR', 'Command', "Error activating file $filename for command $command. The file doesn't exist!");
			return;
		}
		
		$chatBot->commands[$channel][$command]["filename"] = $actual_filename;
		$chatBot->commands[$channel][$command]["admin"] = $admin;
	}
	
	/**
	 * @name: deactivate
	 * @description: Deactivates a command
	 */
	public static function deactivate($channel, $filename, $command) {
		global $chatBot;

		$command = strtolower($command);
		$channel = strtolower($channel);

	  	Logger::log('DEBUG', 'Command', "Deactivate Command:($command) File:($filename) Channel:($channel)");

		unset($chatBot->commands[$channel][$command]);
	}
	
	public static function update_status($channel, $module, $cmd, $status) {
		$db = DB::get_instance();
		
		if ($channel == 'all' || $channel == '' || $channel == null) {
			$type_sql = '';
		} else {
			$type_sql = "AND `type` = '$channel'";
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
	
		$data = $db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd' $module_sql $cmd_sql $type_sql");
		if (count($data) == 0) {
			return 0;
		}
		
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
		Logger::log('DEBUG', 'Command', "Loading enabled commands");

	  	$db = DB::get_instance();

		$data = $db->query("SELECT * FROM cmdcfg_<myname> WHERE `status` = '1' AND `cmdevent` = 'cmd'");
		forEach ($data as $row) {
			Command::activate($row->type, $row->file, $row->cmd, $row->admin);
		}
	}
	
	public static function get($command, $channel = null) {
		$db = DB::get_instance();
		
		$command = strtolower($command);
		
		if ($channel !== null) {
			$type_sql = "AND type = '{$channel}'";
		}
		
		$sql = "SELECT * FROM cmdcfg_<myname> WHERE `cmd` = ? {$type_sql}";
		return $db->query($sql, $command);
	}
}

?>