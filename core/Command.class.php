<?php

class Command extends Annotation {

	/** @Inject */
	public $db;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $setting;
	
	/** @Inject */
	public $accessLevel;
	
	/** @Inject */
	public $help;
	
	/** @Inject */
	public $commandAlias;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $subcommand;
	
	/** @Logger */
	public $logger;
	
	public $commands;

	/**
	 * @name: register
	 * @description: Registers a command
	 */
	public function register($module, $channel, $filename, $command, $admin, $description = '', $help = '', $defaultStatus = null) {
		$command = strtolower($command);
		$module = strtoupper($module);
		
		if (!$this->chatBot->processCommandArgs($channel, $admin)) {
			$this->logger->log('ERROR', "Invalid args for $module:command($command). Command not registered.");
			return;
		}
		
		if (preg_match("/\\.php$/i", $filename)) {
			$actual_filename = $this->util->verify_filename($module . '/' . $filename);
			if ($actual_filename == '') {
				$this->logger->log('ERROR', "Error registering file $filename for command $command. The file doesn't exist!");
				return;
			}
		} else {
			list($name, $method) = explode(".", $filename);
			if (!Registry::instanceExists($name)) {
				$this->logger->log('ERROR', "Error registering method $filename for command $command.  Could not find instance '$name'.");
				return;
			}
			$actual_filename = $filename;
		}

		if ($defaultStatus === null) {
			if ($this->chatBot->vars['default_module_status'] == 1) {
				$status = 1;
			} else {
				$status = 0;
			}
		} else {
			$status = $defaultStatus;
		}

		for ($i = 0; $i < count($channel); $i++) {
			$this->logger->log('debug', "Adding Command to list:($command) File:($actual_filename) Admin:({$admin[$i]}) Channel:({$channel[$i]})");
			
			if (isset($this->chatBot->existing_commands[$channel[$i]][$command])) {
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
	public function activate($channel, $filename, $command, $admin = 'all') {
		$command = strtolower($command);
		$admin = strtolower($admin);
		$channel = strtolower($channel);

	  	$this->logger->log('DEBUG', "Activate Command:($command) Admin Type:($admin) File:($filename) Channel:($channel)");

		if (preg_match("/\\.php$/i", $filename)) {
			$actual_filename = $this->util->verify_filename($filename);
			if ($actual_filename == '') {
				$this->logger->log('ERROR', "Error activating file $filename for command $command. The file doesn't exist!");
				return;
			}
		} else {
			list($name, $method) = explode(".", $filename);
			if (!Registry::instanceExists($name)) {
				$this->logger->log('ERROR', "Error activating method $filename for command $command.  Could not find instance '$name'.");
				return;
			}
			$actual_filename = $filename;
		}
		
		$this->commands[$channel][$command]["filename"] = $actual_filename;
		$this->commands[$channel][$command]["admin"] = $admin;
	}
	
	/**
	 * @name: deactivate
	 * @description: Deactivates a command
	 */
	public function deactivate($channel, $filename, $command) {
		$command = strtolower($command);
		$channel = strtolower($channel);

	  	$this->logger->log('DEBUG', "Deactivate Command:($command) File:($filename) Channel:($channel)");

		unset($this->commands[$channel][$command]);
	}
	
	public function update_status($channel, $cmd, $module, $status, $admin) {
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
		
		if ($admin == '' || $admin == null) {
			$adminSql = '';
		} else {
			$adminSql = ", admin = '$admin'";
		}
	
		$data = $this->db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd' $module_sql $cmd_sql $type_sql");
		if (count($data) == 0) {
			return 0;
		}
		
		forEach ($data as $row) {
			if ($status == 1) {
				$this->activate($row->type, $row->file, $row->cmd, $admin);
			} else if ($status == 0) {
				$this->deactivate($row->type, $row->file, $row->cmd);
			}
		}
		
		return $this->db->exec("UPDATE cmdcfg_<myname> SET status = '$status' $adminSql WHERE `cmdevent` = 'cmd' $module_sql $cmd_sql $type_sql");
	}

	/**
	 * @name: loadCommands
	 * @description: Loads the active commands into memory to activate them
	 */
	public function loadCommands() {
		$this->logger->log('DEBUG', "Loading enabled commands");

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
	
	function process($type, $message, $sender, $sendto) {
		$chatBot = $this->chatBot;
		$db = $this->db;
		$setting = $this->setting;
		
		// Admin Code
		list($cmd, $params) = explode(' ', $message, 2);
		$cmd = strtolower($cmd);
		
		// Check if this is an alias for a command
		if (isset($this->commandAlias->cmd_aliases[$cmd])) {
			$this->logger->log('DEBUG', "Command alias found command: '{$this->commandAlias->cmd_aliases[$cmd]}' alias: '{$cmd}'");
			$cmd = $this->commandAlias->cmd_aliases[$cmd];
			if ($params) {
				$message = $cmd . ' ' . $params;
			} else {
				$message = $cmd;
			}
			$this->process($type, $message, $sender, $sendto);
			return;
		}
		
		$admin = $this->commands[$type][$cmd]["admin"];
		$filename = $this->commands[$type][$cmd]["filename"];

		// Check if a subcommands for this exists
		if (isset($this->subcommand->subcommands[$cmd])) {
			forEach ($this->subcommand->subcommands[$cmd] as $row) {
				if ($row->type == $type && preg_match("/^{$row->cmd}$/i", $message)) {
					$admin = $row->admin;
					$filename = $row->file;
				}
			}
		}
		
		// if file doesn't exist
		if ($filename == '') {
			if (($this->setting->get('guild_channel_cmd_feedback') == 0 && $type == 'guild') || (($this->setting->get('private_channel_cmd_feedback') == 0 && $type == 'priv'))) {
				return;
			}
				
			$this->chatBot->send("Error! Unknown command.", $sendto);
			$this->chatBot->spam[$sender] += 20;
			return;
		}

		// if the character doesn't have access
		if ($this->accessLevel->checkAccess($sender, $admin) !== true) {
			// if they've disabled feedback for guild or private channel, just return
			if (($this->setting->get('guild_channel_cmd_feedback') == 0 && $type == 'guild') || (($this->setting->get('private_channel_cmd_feedback') == 0 && $type == 'priv'))) {
				return;
			}
				
			$this->chatBot->send("Error! Access denied.", $sendto);
			$this->chatBot->spam[$sender] += 20;
			return;
		}

		if ($cmd != 'grc' && $this->setting->get('record_usage_stats') == 1) {
			Registry::getInstance('usage')->record($type, $cmd, $sender);
		}
	
		$syntax_error = false;
		$msg = "";
		if (preg_match("/\\.php$/i", $filename)) {
			require $filename;
		} else {
			list($name, $method) = explode(".", $filename);
			$instance = Registry::getInstance($name);
			if ($instance === null) {
				$this->logger->log('ERROR', "Could not find instance for name '$name'");
			} else {
				$arr = $this->checkMatches($instance, $method, $message);
				if ($arr === false) {
					$syntax_error = true;
				} else {
					// methods will return false to indicate a syntax error, so when a false is returned,
					// we set $syntax_error = true, otherwise we set it to false
					$syntax_error = ($instance->$method($message, $type, $sender, $sendto, $arr) !== false ? false : true);
				}
			}
		}
		if ($syntax_error === true) {
			$results = $this->get($cmd, $type);
			$result = $results[0];
			if ($result->help != '') {
				$blob = $this->help->find($result->help, $sender);
				$helpcmd = ucfirst($result->help);
			} else {
				$blob = $this->help->find($cmd, $sender);
				$helpcmd = ucfirst($cmd);
			}
			if ($blob !== false) {
				$msg = $this->text->make_blob("Help ($helpcmd)", $blob);
				$this->chatBot->send($msg, $sendto);
			} else {
				$this->chatBot->send("Error! Invalid syntax for this command.", $sendto);
			}
		}
		$this->chatBot->spam[$sender] += 10;
	}
	
	public function checkMatches($instance, $method, $message) {
		$reflectedMethod = new ReflectionAnnotatedMethod($instance, $method);
		
		$regexes = $this->retrieveRegexes($reflectedMethod);
		
		if (count($regexes) > 0) {
			forEach ($regexes as $regex) {
				if (preg_match($regex, $message, $arr)) {
					return $arr;
				}
			}
			return false;
		} else {
			return true;
		}
	}
	
	public function retrieveRegexes($reflectedMethod) {
		$regexes = array();
		if ($reflectedMethod->hasAnnotation('Matches')) {
			forEach ($reflectedMethod->getAllAnnotations('Matches') as $annotation) {
				$regexes []= $annotation->value;
			}
		}
		if ($reflectedMethod->hasAnnotation('Subcommand')) {
			$subcmd = $reflectedMethod->getAnnotation('Subcommand');
			$regexes []= "/^" . $subcmd->value . "$/is";
		}
		return $regexes;
	}
}

?>