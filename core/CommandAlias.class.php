<?php

class CommandAlias {

	/**
	 * @name: load
	 * @description: loads active aliases into memory to activate them
	 */
	public static function load() {
		Logger::log('DEBUG', 'CommandAlias', "Loading enabled command aliases");
	
		global $chatBot;
		$db = $chatBot->getInstance('db');

		$data = $db->query("SELECT * FROM cmd_alias_<myname> WHERE `status` = '1'");
		forEach ($data as $row) {
			CommandAlias::activate($row->cmd, $row->alias);
		}
	}
	
	public function getEnabledAliases() {
		global $chatBot;
		$db = $chatBot->getInstance('db');

		return $db->query("SELECT * FROM cmd_alias_<myname> WHERE `status` = '1' ORDER BY alias ASC");
	}
	
	/**
	 * @name: register
	 * @description: Registers a command alias
	 */
	public static function register($module, $command, $alias, $status = 1) {
		global $chatBot;
		$db = $chatBot->getInstance('db');
	
		$module = strtoupper($module);
		$command = strtolower($command);
		$alias = strtolower($alias);
		
		Logger::log('DEBUG', 'CommandAlias', "Registering alias: '{$alias}' for command: '$command'");
		
		if ($chatBot->existing_cmd_aliases[$alias] == true) {
			$sql = "UPDATE cmd_alias_<myname> SET `module` = ?, `cmd` = ? WHERE `alias` = ?";
			$db->exec($sql, $module, $command, $alias);
		} else {
			$sql = "INSERT INTO cmd_alias_<myname> (`module`, `cmd`, `alias`, `status`) VALUES (?, ?, ?, ?)";
			$db->exec($sql, $module, $command, $alias, $status);
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
		global $chatBot;
		$db = $chatBot->getInstance('db');
		
		Logger::log('DEBUG', 'CommandAlias', "Adding alias: '{$alias}' for command: '$command'");
		
		$sql = "INSERT INTO cmd_alias_<myname> (`module`, `cmd`, `alias`, `status`) VALUES (?, ?, ?, ?)";
		return $db->exec($sql, $row->module, $row->cmd, $row->alias, $row->status);
	}
	
	/**
	 * @name: update
	 * @description: Updates a command alias in the db
	 */
	public static function update(&$row) {
		global $chatBot;
		$db = $chatBot->getInstance('db');

	  	Logger::log('DEBUG', 'CommandAlias', "Updating alias :($row->alias)");
		
		$sql = "UPDATE cmd_alias_<myname> SET `module` = ?, `cmd` = ?, `status` = ? WHERE `alias` = ?";
		return $db->exec($sql, $row->module, $row->cmd, $row->status, $row->alias);
	}
	
	public static function get($alias) {
		global $chatBot;
		$db = $chatBot->getInstance('db');
		
		$alias = strtolower($alias);

		$sql = "SELECT * FROM cmd_alias_<myname> WHERE `alias` = ?";
		return $db->queryRow($sql, $alias);
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
		global $chatBot;
		$db = $chatBot->getInstance('db');
		
		$sql = "SELECT * FROM cmd_alias_<myname> WHERE `cmd` LIKE ?";
		return $db->query($sql, $command);
	}
}

?>