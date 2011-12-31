<?php

class CommandAlias {

	/** @Inject */
	public $db;

	/**
	 * @name: load
	 * @description: loads active aliases into memory to activate them
	 */
	public function load() {
		Logger::log('DEBUG', 'CommandAlias', "Loading enabled command aliases");

		$data = $this->db->query("SELECT * FROM cmd_alias_<myname> WHERE `status` = '1'");
		forEach ($data as $row) {
			$this->activate($row->cmd, $row->alias);
		}
	}
	
	public function getEnabledAliases() {
		return $this->db->query("SELECT * FROM cmd_alias_<myname> WHERE `status` = '1' ORDER BY alias ASC");
	}
	
	/**
	 * @name: register
	 * @description: Registers a command alias
	 */
	public function register($module, $command, $alias, $status = 1) {
		global $chatBot;
	
		$module = strtoupper($module);
		$command = strtolower($command);
		$alias = strtolower($alias);
		
		Logger::log('DEBUG', 'CommandAlias', "Registering alias: '{$alias}' for command: '$command'");
		
		if ($chatBot->existing_cmd_aliases[$alias] == true) {
			$sql = "UPDATE cmd_alias_<myname> SET `module` = ?, `cmd` = ? WHERE `alias` = ?";
			$this->db->exec($sql, $module, $command, $alias);
		} else {
			$sql = "INSERT INTO cmd_alias_<myname> (`module`, `cmd`, `alias`, `status`) VALUES (?, ?, ?, ?)";
			$this->db->exec($sql, $module, $command, $alias, $status);
		}
	}
	
	/**
	 * @name: activate
	 * @description: Activates a command alias
	 */
	public function activate($command, $alias) {
		global $chatBot;
		
		$alias = strtolower($alias);

	  	Logger::log('DEBUG', 'CommandAlias', "Activate Command Alias command:($command) alias:($alias)");
		
		$chatBot->cmd_aliases[$alias] = $command;
	}
	
	/**
	 * @name: deactivate
	 * @description: Deactivates a command alias
	 */
	public function deactivate($alias) {
		global $chatBot;

		$alias = strtolower($alias);

	  	Logger::log('DEBUG', 'CommandAlias', "Deactivate Command Alias:($alias)");
		
		unset($chatBot->cmd_aliases[$alias]);
	}
	
	/**
	 * @name: add
	 * @description: Adds a command alias to the db
	 */
	public function add(&$row) {
		Logger::log('DEBUG', 'CommandAlias', "Adding alias: '{$alias}' for command: '$command'");
		
		$sql = "INSERT INTO cmd_alias_<myname> (`module`, `cmd`, `alias`, `status`) VALUES (?, ?, ?, ?)";
		return $this->db->exec($sql, $row->module, $row->cmd, $row->alias, $row->status);
	}
	
	/**
	 * @name: update
	 * @description: Updates a command alias in the db
	 */
	public function update(&$row) {
	  	Logger::log('DEBUG', 'CommandAlias', "Updating alias :($row->alias)");
		
		$sql = "UPDATE cmd_alias_<myname> SET `module` = ?, `cmd` = ?, `status` = ? WHERE `alias` = ?";
		return $this->db->exec($sql, $row->module, $row->cmd, $row->status, $row->alias);
	}
	
	public function get($alias) {
		$alias = strtolower($alias);

		$sql = "SELECT * FROM cmd_alias_<myname> WHERE `alias` = ?";
		return $this->db->queryRow($sql, $alias);
	}
	
	public function get_command_by_alias($alias) {
		$row = $this->get($alias);
		
		// if alias doesn't exist or is disabled
		if ($row === null || $row->status != 1) {
			return null;
		}
		list($cmd) = explode(' ', $row->cmd, 2);
		return $cmd;
	}
	
	public function find_aliases_by_command($command) {
		$sql = "SELECT * FROM cmd_alias_<myname> WHERE `cmd` LIKE ?";
		return $this->db->query($sql, $command);
	}
}

?>