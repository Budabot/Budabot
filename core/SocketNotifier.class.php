<?php

/**
 * The SocketNotifier class provides a way to be notified when some
 * activity happens in a socket.
 *
 * You can add instance of SocketNotifier to Budabot's event loop with method
 * SocketManager::addSocketNotifier() and remove it with
 * SocketManager::removeSocketNotifier().
 *
 * When some activity happens in the given socket the event loop will call the
 * given callback to notify of the activity.
 */
class SocketNotifier {
	private $socket;
	private $type;
	private $callback;

	const ACTIVITY_READ = 1;  // there is
	const ACTIVITY_WRITE = 2;
	const ACTIVITY_ERROR = 4;

	/**
	 * Constructor method.
	 * @param $socket   the socket to listen
	 * @param $type     type of activity
	 * @param $callback the callback which is called on socket activity
	 */
	public function __construct($socket, $type, $callback) {
		$this->socket   = $socket;
		$this->type     = $type;
		$this->callback = $callback;
	}

	/**
	 * Returns the socket resource.
	 */
	public function getSocket() {
		return $this->socket;
	}

	/**
	 * Returns type of the activity.
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Calls the callback and passes given @a $type to the callback.
	 */
	public function notify($type) {
		call_user_func($this->callback, $type);
	}
}
