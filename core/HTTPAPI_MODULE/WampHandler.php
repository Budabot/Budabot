<?php

namespace Budabot\Core\Modules;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;
use Evenement\EventEmitter;
use Exception;

class WampHandler extends EventEmitter implements WampServerInterface {
	function onOpen(ConnectionInterface $conn) {
	}

	function onClose(ConnectionInterface $conn) {
	}

	function onError(ConnectionInterface $conn, Exception $e) {
	}

	function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
	}

	function onSubscribe(ConnectionInterface $conn, $topic) {
		$this->topics["$topic"] = $topic;
		$this->emit("subscribe-$topic", array($conn, $topic));
	}

	function onUnSubscribe(ConnectionInterface $conn, $topic) {
	}

	function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
		$this->emit("$topic", array($conn, $topic, $event));
	}

	function publish($topicName, $payload) {
		if (isset($this->topics[$topicName])) {
			$this->topics[$topicName]->broadcast($payload);
		}
	}
}
