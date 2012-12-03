<?php

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
			$this->processAoChatPackets();

			if ($this->isBotReady()) {
				$this->executeSocketEvents();
				$this->executeConnectEvents();
				$this->executeTimerEvents();
				$this->executeCronEvents();

				usleep(10000);
			}
		}
	}

	/**
	 * Stops execution of this event loop.
	 * Does nothing if exec() is not called.
	 */
	public function quit() {
		$this->shouldQuit = true;
	}

	private function isBotReady() {
		return $this->chatBot->is_ready();
	}

	private function processAoChatPackets() {
		$this->chatBot->processAllPackets();
	}

	private function executeTimerEvents() {
		$this->timer->executeTimerEvents();
	}

	private function executeSocketEvents() {
		$this->socketManager->checkMonitoredSockets();
	}

	private function executeCronEvents() {
		$this->eventManager->crons();
	}

	private function executeConnectEvents() {
		$this->eventManager->executeConnectEvents();
	}
}
