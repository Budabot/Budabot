<?php

/**
 * @Instance
 */
class SocketManager {
	private $socketNotifiers = array();
	private $monitoredSocketsByType = array();
	
	public function __construct(&$vars){
		$this->monitoredSocketsByType[SocketNotifier::ACTIVITY_READ] = array();
		$this->monitoredSocketsByType[SocketNotifier::ACTIVITY_WRITE] = array();
		$this->monitoredSocketsByType[SocketNotifier::ACTIVITY_ERROR] = array();
	}
	
	public function checkMonitoredSockets() {
		$read = $this->monitoredSocketsByType[SocketNotifier::ACTIVITY_READ];
		$write = $this->monitoredSocketsByType[SocketNotifier::ACTIVITY_WRITE];
		$except = $this->monitoredSocketsByType[SocketNotifier::ACTIVITY_ERROR];
		if ($read || $write || $except) {
			if (0 < stream_select($read, $write, $except, 0)) {
				foreach ($this->socketNotifiers as $notifier) {
					$socket = $notifier->getSocket();
					$type = $notifier->getType();

					if (in_array($socket, $read) && $type & SocketNotifier::ACTIVITY_READ) {
						$notifier->notify(SocketNotifier::ACTIVITY_READ);
					}
					if (in_array($socket, $write) && $type & SocketNotifier::ACTIVITY_WRITE) {
						$notifier->notify(SocketNotifier::ACTIVITY_WRITE);
					}
					if (in_array($socket, $except) && $type & SocketNotifier::ACTIVITY_ERROR) {
						$notifier->notify(SocketNotifier::ACTIVITY_ERROR);
					}
				}
			}
		}
	}
	
	/**
	 * Adds given socket notifier to list of sockets which are
	 * monitored for activity.
	 */
	public function addSocketNotifier($socketNotifier) {
		$this->socketNotifiers []= $socketNotifier;

		// add the socket to each activity category for faster access in the event loop
		if ($socketNotifier->getType() & SocketNotifier::ACTIVITY_READ) {
			$this->monitoredSocketsByType[SocketNotifier::ACTIVITY_READ][] = $socketNotifier->getSocket();
		}
		if ($socketNotifier->getType() & SocketNotifier::ACTIVITY_WRITE) {
			$this->monitoredSocketsByType[SocketNotifier::ACTIVITY_WRITE][] = $socketNotifier->getSocket();
		}
		if ($socketNotifier->getType() & SocketNotifier::ACTIVITY_ERROR) {
			$this->monitoredSocketsByType[SocketNotifier::ACTIVITY_ERROR][] = $socketNotifier->getSocket();
		}
	}

	/**
	 * Removes given socket notifier from list of sockets being monitored.
	 */
	public function removeSocketNotifier($socketNotifier) {
		if (is_object($socketNotifier) == false) {
			return;
		}

		$this->removeOne($this->socketNotifiers, $socketNotifier);

		if ($socketNotifier->getType() & SocketNotifier::ACTIVITY_READ) {
			$this->removeOne($this->monitoredSocketsByType[SocketNotifier::ACTIVITY_READ], $socketNotifier->getSocket());
		}
		if ($socketNotifier->getType() & SocketNotifier::ACTIVITY_WRITE) {
			$this->removeOne($this->monitoredSocketsByType[SocketNotifier::ACTIVITY_WRITE], $socketNotifier->getSocket());
		}
		if ($socketNotifier->getType() & SocketNotifier::ACTIVITY_ERROR) {
			$this->removeOne($this->monitoredSocketsByType[SocketNotifier::ACTIVITY_ERROR], $socketNotifier->getSocket());
		}
	}
	
	private function removeOne(&$array, $value) {
		$key = array_search($value, $array, true);
		if ($key !== false) {
			unset ($array[$key]);
		}
	}
}

?>
