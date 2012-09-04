<?php

class LegacyEventProxy {
	/** @Inject */
	public $eventManager;

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
			$this->eventManager->register($module, $type, $this->controller . "." . $handlerName, $description, $help, $defaultStatus);
		} else {
			$this->eventManager->register($module, $type, $filename, $description, $help, $defaultStatus);
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
			$this->eventManager->activate($type, $this->controller . "." . $handlerName);
		} else {
			$this->eventManager->activate($type, $filename);
		}
	}
}
