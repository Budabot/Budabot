<?php

class CommandAlias {

	/**
	 * @name: load
	 * @description: loads active aliases into memory to activate them
	 */
	public static function load() {
		Logger::log('DEBUG', 'CommandAlias', "Loading enabled command aliases");
	
		$db = DB::get_instance();

		$data = $db->query("SELECT * FROM cmd_alias_<myname> WHERE `status` = '1'");
		forEach ($data as $row) {
			CommandAlias::activate($row->cmd, $row->alias);
		}
	}
	
	public function getEnabledAliases() {
		$db = DB::get_instance();

		return $db->query("SELECT * FROM cmd_alias_<myname> WHERE `status` = '1' ORDER BY alias ASC");
	}
	
	/**
	 * @name: register
	 * @description: Registers a command alias
	 */
	public static function register($module, $command, $alias, $status = 1) {
		$db = DB::get_instance();
		global $chatBot;
	
		$module = strtoupper($module);
		$command = strtolower($command);
		$alias = strtolower($alias);
		
		Logger::log('DEBUG', 'CommandAlias', "Registering alias: '{$alias}' for command: '$command'");
		
		if ($chatBot->existing_cmd_aliases[$alias] == true) {
			$db->exec("UPDATE cmd_alias_<myname> SET `module` = '{$module}', `cmd` = '{$command}' WHERE `alias` = '{$alias}'");
		} else {
			$db->exec("INSERT INTO cmd_alias_<myname> (`module`, `cmd`, `alias`, `status`) VALUES ('{$module}', '{$command}', '{$alias}', '{$status}')");
		}
	}
	
	/**
	 * @name: activate
	 * @description: Activates a command alias
	 */
	public static function activate($command, $alias) {
		global $chatBot;
		
		$alias = strtolower($alias);

	  	Logger::log('DEBUG', 'CommandAlias', "Activate Command Alias command:($command) alias:($alias)");
		
		$chatBot->cmd_aliases[$alias] = $command;
	}
	
	/**
	 * @name: deactivate
	 * @description: Deactivates a command alias
	 */
	public static function deactivate($alias) {
		global $chatBot;

		$alias = strtolower($alias);

	  	Logger::log('DEBUG', 'CommandAlias', "Deactivate Command Alias:($alias)");
		
		unset($chatBot->cmd_aliases[$alias]);
	}
	
	/**
	 * @name: add
	 * @description: Adds a command alias to the db
	 */
	public static function add(&$row) {
		$db = DB::get_instance();
		
		Logger::log('DEBUG', 'CommandAlias', "Adding alias: '{$alias}' for command: '$command'");
		
		$sql = "INSERT INTO cmd_alias_<myname> (`module`, `cmd`, `alias`, `status`) VALUES ('{$row->module}', '{$row->cmd}', '{$row->alias}', '{$row->status}')";
		return $db->exec($sql);
	}
	
	/**
	 * @name: update
	 * @description: Updates a command alias in the db
	 */
	public static function update(&$row) {
		$db = DB::get_instance();

	  	Logger::log('DEBUG', 'CommandAlias', "Updating alias :($row->alias)");
		
		$sql = "UPDATE cmd_alias_<myname> SET `module` = '{$row->module}', `cmd` = '{$row->cmd}', `status` = '{$row->status}' WHERE `alias` = '{$row->alias}'";
		return $db->exec($sql);
	}
	
	public static function get($alias) {
		$db = DB::get_instance();
		
		$alias = strtolower($alias);

		$sql = "SELECT * FROM cmd_alias_<myname> WHERE `alias` = '{$alias}'";
		$data = $db->query($sql);
		if (count($data) == 0) {
			return null;
		} else {
			return $data[0];
		}
	}
	
	public static function get_command_by_alias($alias) {
		$row = CommandAlias::get($alias);
		
		// if alias doesn't exist or is disabled
		if ($row === null || $row->status != 1) {
			return null;
		}
		list($cmd) = explode(' ', $row->cmd, 2);
		return $cmd;
	}
	
	public static function find_aliases_by_command($command) {
		$db = DB::get_instance();
		
		$sql = "SELECT * FROM cmd_alias_<myname> WHERE `cmd` LIKE '{$command}'";
		return $db->query($sql);
	}
}

?>