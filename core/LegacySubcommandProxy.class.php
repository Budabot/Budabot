<?php

class LegacySubcommandProxy {
	/** @Inject */
	public $subcommandManager;

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
			$this->subcommandManager->register($module, $channel, $this->controller . "." . $handlerName, $command, $admin, $parent_command, $description, $help, $defaultStatus);
		} else {
			$this->subcommandManager->register($module, $channel, $filename, $command, $admin, $parent_command, $description, $help, $defaultStatus);
		}
	}
}
