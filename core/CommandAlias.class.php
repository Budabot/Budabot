<?php

class CommandAlias {

	/**
	 * @name: load
	 * @description: loads active aliases into memory to activate them
	 */
	public static function load() {
		$db = DB::get_instance();
		global $chatBot;

		$db->query("SELECT * FROM cmd_alias_<myname> WHERE `status` = '1'");
		$data = $db->fObject("all");
		forEach ($data as $row) {
			CommandAlias::activate($row->cmd, $row->alias);
		}
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
		
		$command = strtolower($command);
		$alias = strtolower($alias);

	  	Logger::log('DEBUG', 'Core', "Activate Command Alias command:($command) alias:($alias)");
		
		$chatBot->cmd_aliases[$alias] = $command;
	}
	
	/**
	 * @name: deactivate
	 * @description: Deactivates a command alias
	 */
	public static function deactivate($command, $alias) {
		global $chatBot;
		
		$command = strtolower($command);
		$alias = strtolower($alias);

	  	Logger::log('DEBUG', 'Core', "Deactivate Command Alias command:($command) alias:($alias)");
		
		$chatBot->cmd_aliases[$alias] = $command;
	}
	
	public static function get($alias) {
		$db = DB::get_instance();

		$sql = "SELECT * FROM cmd_alias_<myname> WHERE `alias` = '{$alias}'";
		$db->query($sql);
		return $db->fObject();
	}
	
	public static function get_command_by_alias($alias) {
		$row = CommandAlias::get($alias);
		if ($row === null) {
			return null;
		}
		list($cmd) = explode(' ', $row->cmd, 2);
		return $cmd;
	}
}

?>