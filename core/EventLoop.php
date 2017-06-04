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
	
	public function execSingleLoop() {
		$this->chatBot->processAllPackets();

		if ($this->chatBot->isReady()) {
			$this->socketManager->checkMonitoredSockets();
			$this->eventManager->executeConnectEvents();
			$this->timer->executeTimerEvents();
			$this->eventManager->crons();

			usleep(10000);
		}
	}
}
