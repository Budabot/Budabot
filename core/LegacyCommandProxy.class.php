<?php

class LegacyCommandProxy {
	/** @Inject */
	public $commandManager;

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
			$this->commandManager->register($module, $channel, $this->controller . "." . $handlerName, $command, $admin, $description, $help, $defaultStatus);
		} else {
			$this->commandManager->register($module, $channel, $filename, $command, $admin, $description, $help, $defaultStatus);
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
			$this->commandManager->activate($channel, $this->controller . "." . $handlerName, $command, $admin);
		} else {
			$this->commandManager->activate($channel, $filename, $command, $admin);
		}
	}
}
