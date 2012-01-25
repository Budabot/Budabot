<?php

class LegacyController {
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $setting;
	
	/** @Inject */
	public $help;
	
	/** @Inject */
	public $commandAlias;
	
	public $moduleName;

	private $commands = array();
	
	public function loadLegacyModule($baseDir, $name) {
		$setting = $this->setting;
		$chatBot = $this->chatBot;
		$db = $this->db;
		$help = $this->help;
		$commandAlias = $this->commandAlias;
		$MODULE_NAME = $this->moduleName;
		
		$command = new CommandProxy($name . ".commandHandler");
		Registry::injectDependencies($command);
		
		$event = new EventProxy();
		Registry::injectDependencies($event);
		
		$subcommand = new SubcommandProxy();
		Registry::injectDependencies($subcommand);

		require "{$baseDir}/{$this->moduleName}/{$this->moduleName}.php";
		
		$this->commands = $command->commands;
	}

	public function commandHandler($message, $channel, $sender, $sendto) {
		list($cmd, $params) = explode(" ", $message, 2);
		$syntax_error = false;

		$setting = $this->setting;
		$chatBot = $this->chatBot;
		$db = $this->db;
		$type = $channel;

		require $commands[$cmd];
		
		if ($syntax_error === true) {
			return false;
		}
	}
}

class CommandProxy {
	/** @Inject */
	public $command;
	
	/** @Inject */
	public $util;
	
	/** @Logger */
	public $logger;
	
	private $commandHandler;

	public $commands = array();
	
	public function __construct($commandHandler) {
		$this->commandHandler = $commandHandler;
	}

	public function register($module, $channel, $filename, $command, $admin, $description, $help = '', $defaultStatus = null) {
		if (preg_match("/\\.php$/i", $filename)) {
			$actual_filename = $this->util->verify_filename($module . '/' . $filename);
			if ($actual_filename == '') {
				$this->logger->log('ERROR', "Error registering file $filename for command $command. The file does not exist!");
				return;
			}
			$this->commands[$command] = $actual_filename;
			$this->command->register($module, $channel, $this->commandHandler, $command, $admin, $description, $help, $defaultStatus);
		} else {
			$this->command->register($module, $channel, $filename, $command, $admin, $description, $help, $defaultStatus);
		}
	}
	
	public function activate($channel, $filename, $command, $admin = 'all') {
		if (preg_match("/\\.php$/i", $filename)) {
			$actual_filename = $this->util->verify_filename($filename);
			if ($actual_filename == '') {
				$this->logger->log('ERROR', "Error activating file $filename for command $command. The file does not exist!");
				return;
			}
			$this->commands[$command] = $actual_filename;
			$this->command->activate($channel, $this->commandHandler, $command, $admin);
		} else {
			$this->command->activate($channel, $filename, $command, $admin);
		}
	}
}

class EventProxy {
	/** @Inject */
	public $event;

	public function register($module, $type, $filename, $description = 'none', $help = '', $defaultStatus = null) {
		//$this->event->register($module, $type, $filename, $description, $help, $defaultStatus);
	}
	
	public function activate($type, $filename) {
		//$this->event->activate($type, $filename);
	}
}

class SubcommandProxy {
	/** @Inject */
	public $subcommand;

	public function register($module, $channel, $filename, $command, $admin, $parent_command, $description = 'none', $help = '', $defaultStatus = null) {
		//$this->subcommand->register($module, $channel, $filename, $command, $admin, $parent_command, $description, $help, $defaultStatus);
	}
}

?>