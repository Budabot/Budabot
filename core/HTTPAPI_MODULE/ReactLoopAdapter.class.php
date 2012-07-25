<?php

require __DIR__ . '/../../lib/vendor/autoload.php';

class ReactLoopAdapter implements React\EventLoop\LoopInterface {

	private $chatBot;
	private $readNotifiers;
	private $writeNotifiers;

	public function __construct($chatBot) {
		$this->chatBot        = $chatBot;
		$this->readNotifiers  = array();
		$this->writeNotifiers = array();
	}

    public function addReadStream($stream, $listener) {
		$id = (int)$stream;
		if (isset($this->readNotifiers[$id]) == false) {
			$loop = $this;
			$notifier = new SocketNotifier($stream, SocketNotifier::ACTIVITY_READ, function($type) use ($listener, $stream, $loop) {
				if (call_user_func($listener, $stream, $loop) === false) {
					$loop->removeReadStream($stream);
				}
			});
			$this->chatBot->addSocketNotifier($notifier);
			$this->readNotifiers[$id] = $notifier;
		}
	}

    public function addWriteStream($stream, $listener) {
		$id = (int)$stream;
		if (isset($this->writeNotifiers[$id]) == false) {
			$loop = $this;
			$notifier = new SocketNotifier($stream, SocketNotifier::ACTIVITY_WRITE, function($type) use ($listener, $stream, $loop) {
				if (call_user_func($listener, $stream, $loop) === false) {
					$loop->removeWriteStream($stream);
				}
			});
			$this->chatBot->addSocketNotifier($notifier);
			$this->writeNotifiers[$id] = $notifier;
		}
	}

    public function removeReadStream($stream) {
		$id = (int)$stream;
		if (isset($this->readNotifiers[$id])) {
			$this->chatBot->removeSocketNotifier($this->readNotifiers[$id]);
			unset ($this->readNotifiers[$id]);
		}
	}

    public function removeWriteStream($stream) {
		$id = (int)$stream;
		if (isset($this->writeNotifiers[$id])) {
			$this->chatBot->removeSocketNotifier($this->writeNotifiers[$id]);
			unset ($this->writeNotifiers[$id]);
		}
	}
    public function removeStream($stream) {
		$this->removeReadStream($stream);
		$this->removeWriteStream($stream);
	}

    public function addTimer($interval, $callback) {
		throw new BadMethodCallException("Timers are not implemented by this event loop!");
	}

    public function addPeriodicTimer($interval, $callback) {
		throw new BadMethodCallException("Timers are not implemented by this event loop!");
	}

    public function cancelTimer($signature) {
		throw new BadMethodCallException("Timers are not implemented by this event loop!");
	}

    public function tick() {
		throw new BadMethodCallException("Ticking is not implemented by this event loop!");
	}
    public function run() {
		throw new BadMethodCallException("Running is not implemented by this event loop!");
	}

    public function stop() {
		throw new BadMethodCallException("Running is not implemented by this event loop!");
	}
}
