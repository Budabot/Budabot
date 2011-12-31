<?php

class Command extends Annotation {

	/** @Inject */
	public $db;

	/**
	 * @name: register
	 * @description: Registers a command
	 */
	public function register($module, $channel, $filename, $command, $admin, $description = '', $help = ''){
		global $chatBot;

		$command = strtolower($command);
		$module = strtoupper($module);
		
		if (!$chatBot->processCommandArgs($channel, $admin)) {
			Logger::log('ERROR', 'Command', "Invalid args for $module:command($command). Command not registered.");
			return;
		}
		
		if (preg_match("/\\.php$/i", $filename)) {
			$actual_filename = Util::verify_filename($module . '/' . $filename);
			if ($actual_filename == '') {
				Logger::log('ERROR', 'Command', "Error registering file $filename for command $command. The file doesn't exist!");
				return;
			}
		} else {
			list($name, $method) = explode(".", $filename);
			$instance = Registry::getInstance($name);
			if ($instance === null) {
				Logger::log('ERROR', 'Command', "Error registering method $filename for command $command.  Could not find instance '$name'.");
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
			Logger::log('debug', 'Command', "Adding Command to list:($command) File:($actual_filename) Admin:({$admin[$i]}) Channel:({$channel[$i]})");
			
			if (isset($chatBot->existing_commands[$channel[$i]][$command])) {
				$sql = "UPDATE cmdcfg_<myname> SET `module` = ?, `verify` = ?, `file` = ?, `description` = ?, `help` = ? WHERE `cmd` = ? AND `type` = ?";
				$this->db->exec($sql, $module, '1', $actual_filename, $description, $help, $command, $channel[$i]);
			} else {
				$sql = "INSERT INTO cmdcfg_<myname> (`module`, `type`, `file`, `cmd`, `admin`, `description`, `verify`, `cmdevent`, `status`, `help`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
				$this->db->exec($sql, $module, $channel[$i], $actual_filename, $command, $admin[$i], $description, '1', 'cmd', $status, $help);
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

		if (preg_match("/\\.php$/i", $filename)) {
			$actual_filename = Util::verify_filename($filename);
			if ($actual_filename == '') {
				Logger::log('ERROR', 'Command', "Error activating file $filename for command $command. The file doesn't exist!");
				return;
			}
		} else {
			list($name, $method) = explode(".", $filename);
			$instance = Registry::getInstance($name);
			if ($instance === null) {
				Logger::log('ERROR', 'Command', "Error activating method $filename for command $command.  Could not find instance '$name'.");
				return;
			}
			$actual_filename = $filename;
		}
		
		$chatBot->commands[$channel][$command]["filename"] = $actual_filename;
		$chatBot->commands[$channel][$command]["admin"] = $admin;
	}
	
	/**
	 * @name: deactivate
	 * @description: Deactivates a command
	 */
	public function deactivate($channel, $filename, $command) {
		global $chatBot;

		$command = strtolower($command);
		$channel = strtolower($channel);

	  	Logger::log('DEBUG', 'Command', "Deactivate Command:($command) File:($filename) Channel:($channel)");

		unset($chatBot->commands[$channel][$command]);
	}
	
	public function update_status($channel, $module, $cmd, $status) {
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
	
		$data = $this->db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd' $module_sql $cmd_sql $type_sql");
		if (count($data) == 0) {
			return 0;
		}
		
		forEach ($data as $row) {
			if ($status == 1) {
				$this->activate($row->type, $row->filename, $row->cmd, $row->admin);
			} else if ($status == 0) {
				$this->deactivate($row->type, $row->filename, $row->cmd);
			}
		}
		
		return $this->db->exec("UPDATE cmdcfg_<myname> SET status = '$status' WHERE `cmdevent` = 'cmd' $module_sql $cmd_sql $type_sql");
	}

	/**
	 * @name: loadCommands
	 * @description: Loads the active commands into memory to activate them
	 */
	public function loadCommands() {
		Logger::log('DEBUG', 'Command', "Loading enabled commands");

		$data = $this->db->query("SELECT * FROM cmdcfg_<myname> WHERE `status` = '1' AND `cmdevent` = 'cmd'");
		forEach ($data as $row) {
			$this->activate($row->type, $row->file, $row->cmd, $row->admin);
		}
	}
	
	public function get($command, $channel = null) {
		$command = strtolower($command);
		
		if ($channel !== null) {
			$type_sql = "AND type = '{$channel}'";
		}
		
		$sql = "SELECT * FROM cmdcfg_<myname> WHERE `cmd` = ? {$type_sql}";
		return $this->db->query($sql, $command);
	}
}

?>