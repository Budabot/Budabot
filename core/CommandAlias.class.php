<?php

/**
 * @Instance
 */
class CommandAlias {

	/** @Inject */
	public $db;

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $commandManager;

	/** @Logger */
	public $logger;

	public $cmd_aliases = array();

	const ALIAS_HANDLER = "CommandAlias.process";

	/**
	 * @name: load
	 * @description: loads active aliases into memory to activate them
	 */
	public function load() {
		$this->logger->log('DEBUG', "Loading enabled command aliases");

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
		$module = strtoupper($module);
		$command = strtolower($command);
		$alias = strtolower($alias);

		$this->logger->log('DEBUG', "Registering alias: '{$alias}' for command: '$command'");

		if ($this->chatBot->existing_cmd_aliases[$alias] == true) {
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
		$alias = strtolower($alias);

		$this->logger->log('DEBUG', "Activate Command Alias command:($command) alias:($alias)");

		$this->commandManager->activate('msg', self::ALIAS_HANDLER, $alias, 'all');
		$this->commandManager->activate('priv', self::ALIAS_HANDLER, $alias, 'all');
		$this->commandManager->activate('guild', self::ALIAS_HANDLER, $alias, 'all');
		$this->cmd_aliases[$alias] = $command;
	}

	/**
	 * @name: deactivate
	 * @description: Deactivates a command alias
	 */
	public function deactivate($alias) {
		$alias = strtolower($alias);

		$this->logger->log('DEBUG', "Deactivate Command Alias:($alias)");

		$this->commandManager->deactivate('msg', self::ALIAS_HANDLER, $alias);
		$this->commandManager->deactivate('priv', self::ALIAS_HANDLER, $alias);
		$this->commandManager->deactivate('guild', self::ALIAS_HANDLER, $alias);
		unset($this->cmd_aliases[$alias]);
	}

	public function process($message, $channel, $sender, CommandReply $sendto) {
		list($alias, $params) = explode(' ', $message, 2);
		$alias = strtolower($alias);

		// Check if this is an alias for a command
		if (!isset($this->cmd_aliases[$alias])) {
			return false;
		}

		$this->logger->log('DEBUG', "Command alias found command: '{$this->cmd_aliases[$alias]}' alias: '{$alias}'");
		$cmd = $this->cmd_aliases[$alias];
		if ($params) {
			// count number of parameters and don't split more than that so that the
			// last parameter will have whatever is left

			// TODO: figure out highest numbered parameter and use that as $numMatches
			// otherwise this will break if the parameters do not include every number
			// from 1 to MAX -Tyrence
			preg_match_all("/{\\d+}/", $cmd, $matches);
			$numMatches = count(array_unique($matches[0]));
			if ($numMatches == 0) {
				$cmd .= " {0}";
			}

			$aliasParams = explode(' ', $params, $numMatches);

			// add the entire param string as the {0} parameter
			array_unshift($aliasParams, $params);

			// replace parameter placeholders with their values
			for ($i = 0; $i < count($aliasParams); $i++) {
				$cmd = str_replace('{' . $i . '}', $aliasParams[$i], $cmd);
			}
		}
		// if parameter placeholders still exist, then they did not pass enough parameters
		if (preg_match("/{\\d+}/", $cmd)) {
			return false;
		} else {
			$this->commandManager->process($channel, $cmd, $sender, $sendto);
		}
	}

	/**
	 * @name: add
	 * @description: Adds a command alias to the db
	 */
	public function add(&$row) {
		$this->logger->log('DEBUG', "Adding alias: '{$alias}' for command: '$command'");

		$sql = "INSERT INTO cmd_alias_<myname> (`module`, `cmd`, `alias`, `status`) VALUES (?, ?, ?, ?)";
		return $this->db->exec($sql, $row->module, $row->cmd, $row->alias, $row->status);
	}

	/**
	 * @name: update
	 * @description: Updates a command alias in the db
	 */
	public function update(&$row) {
		$this->logger->log('DEBUG', "Updating alias :($row->alias)");

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
