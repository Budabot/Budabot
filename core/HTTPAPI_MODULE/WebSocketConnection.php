<?php

use Ratchet\MessageComponentInterface;
use Ratchet\Server\IoConnection;

/**
 * This class is pretty much copy & pasted from Ratchet\Server\IoServer and
 * modified to act as a simple connection handler.
 */
class WebSocketConnection {

	private $app;

	public function __construct(MessageComponentInterface $app, $conn) {
		$this->app = $app;
		$conn->decor = new IoConnection($conn);

		$conn->decor->resourceId    = (int)$conn->stream;
		$conn->decor->remoteAddress = $conn->getRemoteAddress();

		$this->app->onOpen($conn->decor);

		$conn->on('data', array($this, 'handleData'));
		$conn->on('end', array($this, 'handleEnd'));
		$conn->on('error', array($this, 'handleError'));
	}

	public function handleData($data, $conn) {
		try {
			$this->app->onMessage($conn->decor, $data);
		} catch (Exception $e) {
			$this->handleError($e, $conn);
		}
	}

	public function handleEnd($conn) {
		try {
			$this->app->onClose($conn->decor);
		} catch (Exception $e) {
			$this->handleError($e, $conn);
		}

		unset($conn->decor);
	}

	public function handleError(Exception $e, $conn) {
		$this->app->onError($conn->decor, $e);
	}
}
