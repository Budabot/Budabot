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
	public $buddylistManager;

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

		$command = new LegacyCommandProxy($this->name);
		$command->commands = &$this->commands;
		Registry::injectDependencies($command);

		$event = new LegacyEventProxy($this->name);
		$event->events = &$this->events;
		Registry::injectDependencies($event);

		$subcommand = new LegacySubcommandProxy($this->name);
		$subcommand->subcommands = &$this->subcommands;
		Registry::injectDependencies($subcommand);

		require "{$this->baseDir}/{$this->moduleName}/{$this->moduleName}.php";
	}

	public function eventHandler($filename, $eventObj) {
		$chatBot = $this->chatBot;
		$db = $this->db;
		$setting = $this->setting;
		$buddylistManager = $this->buddylistManager;

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
		$buddylistManager = $this->buddylistManager;
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
