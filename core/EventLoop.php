<?php

namespace Budabot\Core;

class EventLoop {

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $eventManager;

	/** @Inject */
	public $socketManager;

	/** @Inject */
	public $timer;

	private $shouldQuit = false;

	/**
	 * This method starts the event loop which processes any packets from ao
	 * chat server and handles events.
	 * The call blocks execution until quit() is called.
	 */
	public function exec() {
		$this->shouldQuit = false;

		while (!$this->shouldQuit) {
			$this->execSingleLoop();
		}
	}
	
	public function execSingleLoop() {
		$this->chatBot->processAllPackets();

		if ($this->chatBot->is_ready()) {
			$this->socketManager->checkMonitoredSockets();
			$this->eventManager->executeConnectEvents();
			$this->timer->executeTimerEvents();
			$this->eventManager->crons();

			usleep(10000);
		}
	}

	/**
	 * Stops execution of this event loop.
	 * Does nothing if exec() is not called.
	 */
	public function quit() {
		$this->shouldQuit = true;
	}
}
