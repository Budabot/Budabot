<?php

/**
 * @Instance
 */
class Timer {
	/**
	 * @internal
	 * Array of waiting timer events.
	 */
	private $timerEvents = array();

	public function executeTimerEvents() {
		// execute timer events
		while (count($this->timerEvents) > 0 && $this->timerEvents[0]->time <= time()) {
			$timerEvent = array_shift($this->timerEvents);
			$timerEvent->callCallback();
		}
	}

	/**
	 * Calls given callback asynchronously after $delay seconds.
	 *
	 * The callback has following signature:
	 * <code>
	 * function callback(...)
	 * </code>
	 *  * ... - optional values which are same as given as arguments to this method.
	 *
	 * Example usage:
	 * <code>
	 * $this->util->callLater(5, function($message) {
	 *     print $message;
	 * }, 'Hello World');
	 * </code>
	 * Prints 'Hello World' after 5 seconds.
	 *
	 * @param integer  $delay time in seconds to delay the call
	 * @param callback $callback callback which is called after timeout
	 * @internal param $ ... any additional parameters are passed to the callback
	 * @return TimerEvent
	 */
	public function callLater($delay, $callback) {
		$additionalArgs = func_get_args();
		array_shift($additionalArgs); // remove $delay
		array_shift($additionalArgs); // remove $callback
		return $this->addTimerEvent($delay, $callback, $additionalArgs);
	}

	/**
	 * @internal
	 */
	public function abortEvent( $event ) {
		$key = array_search($event, $this->timerEvents, true);
		if ($key !== false) {
			unset($this->timerEvents[$key]);
			$this->sortEventsByTime();
		}
	}

	/**
	 * @internal
	 */
	public function restartEvent( $event ) {
		$key = array_search($event, $this->timerEvents, true);
		if ($key === false) {
			$this->timerEvents []= $event;
		}
		$this->sortEventsByTime();
	}

	/**
	 * Adds new timer event.
	 * $callback will be called with arguments $args array after $delay seconds.
	 */
	private function addTimerEvent($delay, $callback, $args) {
		$event = new TimerEvent($this, $delay, $callback, $args);
		$this->restartEvent($event);
		$this->sortEventsByTime();
		return $event;
	}

	private function sortEventsByTime() {
		usort($this->timerEvents, function($a, $b) {
			if ($a->time == $b->time) {
				return 0;
			}
			return ($a->time < $b->time) ? -1 : 1;
		});
	}
}

?>
