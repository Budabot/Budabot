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
			call_user_func_array($timerEvent->callback, $timerEvent->args);
		}
	}
	
	/**
	 * Calls given callback asyncronously after $delay seconds.
	 *
	 * The callback has following signature:
	 * <code>
	 * function callback($data)
	 * </code>
	 *  * $data - optional value which is same as given as argument to this method.
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
	 * @param ... any additional parameters are passed to the callback
	 */
	public function callLater($delay, $callback) {
		$additionalArgs = func_get_args();
		array_shift($additionalArgs); // remove $delay
		array_shift($additionalArgs); // remove $callback
		$this->addTimerEvent($delay, $callback, $additionalArgs);
	}

	/**
	 * @internal
	 *
	 * Adds new timer event.
	 * $callback will be called with arguments $args array after $delay seconds.
	 * 
	 * You shouldn't call this method, use callLater() instead.
	 */
	public function addTimerEvent($delay, $callback, $args) {
		$timerEvent = new stdClass;
		$timerEvent->callback = $callback;
		$timerEvent->args = $args;
		$timerEvent->time = time() + intval($delay);

		$this->timerEvents []= $timerEvent;
		usort($this->timerEvents, create_function('$a, $b',
			'if ($a->time == $b->time) {
				return 0;
			}
			return ($a->time < $b->time) ? -1 : 1;'
		));
	}
}

?>
