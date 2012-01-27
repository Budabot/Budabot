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
	
	/** @Inject */
	public $buddyList;
	
	public $moduleName;

	private $commands = array();
	private $events = array();
	private $subcommands = array();
	
	private $baseDir;
	private $name;
	
	public function __construct($baseDir, $name) {
		$this->baseDir = $baseDir;
		$this->name = $name;
	}
	
	/**
	 * @Setup
	 */
	public function setup() {
		$setting = $this->setting;
		$chatBot = $this->chatBot;
		$db = $this->db;
		$help = $this->help;
		$commandAlias = $this->commandAlias;
		$MODULE_NAME = $this->moduleName;
		
		$command = new CommandProxy($this->name);
		$command->commands = &$this->commands;
		Registry::injectDependencies($command);
		
		$event = new EventProxy($this->name);
		$event->events = &$this->events;
		Registry::injectDependencies($event);
		
		$subcommand = new SubcommandProxy($this->name);
		$subcommand->subcommands = &$this->subcommands;
		Registry::injectDependencies($subcommand);

		require "{$this->baseDir}/{$this->moduleName}/{$this->moduleName}.php";
	}
	
	public function eventHandler($filename, $eventObj) {
		$chatBot = $this->chatBot;
		$db = $this->db;
		$setting = $this->setting;
		$buddyList = $this->buddyList;
		
		$type = $eventObj->type;
		@$channel = $eventObj->channel;
		@$sender = $eventObj->sender;
		@$message = $eventObj->message;
		@$packet_type = $eventObj->packet->type;
		@$args = $eventObj->packet->args;

		require $filename;
	}
	
	public function commandHandler($filename, $message, $channel, $sender, $sendto) {
		$syntax_error = false;

		$setting = $this->setting;
		$chatBot = $this->chatBot;
		$db = $this->db;
		$buddyList = $this->buddyList;
		$type = $channel;

		require $filename;
		
		if ($syntax_error === true) {
			return false;
		}
	}
	
	public function __call($name, $arguments) {
		if (isset($this->events[$name])) {
			return $this->eventHandler($this->events[$name], $arguments[0]);
		} else if (isset($this->commands[$name])) {
			return $this->commandHandler($this->commands[$name], $arguments[0], $arguments[1], $arguments[2], $arguments[3]);
		} else if (isset($this->subcommands[$name])) {
			return $this->commandHandler($this->subcommands[$name], $arguments[0], $arguments[1], $arguments[2], $arguments[3]);
		} else {
			$this->logger->log("ERROR", "No handler found for $name in module $this->module");
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
	
	public $counter = 0;
	
	public $commands = array();
	
	private $controller;
	
	public function __construct($controller) {
		$this->controller = $controller;
	}

	public function register($module, $channel, $filename, $command, $admin, $description, $help = '', $defaultStatus = null) {
		if (preg_match("/\\.php$/i", $filename)) {
			$actual_filename = $this->util->verify_filename($module . '/' . $filename);
			if ($actual_filename == '') {
				$this->logger->log('ERROR', "Error registering file $filename for command $command. The file does not exist!");
				return;
			}
			
			$handlerName = "command_" . ($this->counter++);
			$this->commands[$handlerName] = $actual_filename;
			$this->command->register($module, $channel, $this->controller . "." . $handlerName, $command, $admin, $description, $help, $defaultStatus);
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

			$handlerName = "command_" . ($this->counter++);
			$this->commands[$handlerName] = $actual_filename;
			$this->command->activate($channel, $this->controller . "." . $handlerName, $command, $admin);
		} else {
			$this->command->activate($channel, $filename, $command, $admin);
		}
	}
}

class EventProxy {
	/** @Inject */
	public $event;
	
	/** @Inject */
	public $util;
	
	/** @Logger */
	public $logger;
	
	public $counter = 0;
	
	public $events = array();
	
	private $controller;
	
	public function __construct($controller) {
		$this->controller = $controller;
	}

	public function register($module, $type, $filename, $description = 'none', $help = '', $defaultStatus = null) {
		if (preg_match("/\\.php$/i", $filename)) {
			$actual_filename = $this->util->verify_filename($module . '/' . $filename);
			if ($actual_filename == '') {
				$this->logger->log('ERROR', "Error registering event Type:($type) File:($filename) Module:($module). The file does not exist!");
				return;
			}

			$handlerName = "event_" . ($this->counter++);
			$this->events[$handlerName] = $actual_filename;
			$this->event->register($module, $type, $this->controller . "." . $handlerName, $description, $help, $defaultStatus);
		} else {
			$this->event->register($module, $type, $filename, $description, $help, $defaultStatus);
		}
	}
	
	public function activate($type, $filename) {
		if (preg_match("/\\.php$/i", $filename)) {
			$actual_filename = $this->util->verify_filename($filename);
			if ($actual_filename == '') {
				$this->logger->log('ERROR', "Error activating event Type:($type) File:($filename). The file does not exist!");
				return;
			}

			$handlerName = "event_" . ($this->counter++);
			$this->events[$handlerName] = $actual_filename;
			$this->event->activate($type, $this->controller . "." . $handlerName);
		} else {
			$this->event->activate($type, $filename);
		}
	}
}

class SubcommandProxy {
	/** @Inject */
	public $subcommand;
	
	/** @Inject */
	public $util;
	
	/** @Logger */
	public $logger;
	
	public $counter = 0;
	
	public $subcommands = array();
	
	private $controller;
	
	public function __construct($controller) {
		$this->controller = $controller;
	}

	public function register($module, $channel, $filename, $command, $admin, $parent_command, $description = 'none', $help = '', $defaultStatus = null) {
		if (preg_match("/\\.php$/i", $filename)) {
			$actual_filename = $this->util->verify_filename($module . '/' . $filename);
			if ($actual_filename == '') {
				$this->logger->log('ERROR', "Error in registering the file $filename for Subcommand $command. The file does not exist!");
				return;
			}
			
			$handlerName = "subcommand_" . ($this->counter++);
			$this->subcommands[$handlerName] = $actual_filename;
			$this->subcommand->register($module, $channel, $this->controller . "." . $handlerName, $command, $admin, $parent_command, $description, $help, $defaultStatus);
		} else {
			$this->subcommand->register($module, $channel, $filename, $command, $admin, $parent_command, $description, $help, $defaultStatus);
		}
	}
}

?>