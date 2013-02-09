<?php

use React\Socket\Connection;
use React\Http\Request;
use React\Socket\ServerInterface;
use Ratchet\MessageComponentInterface;

class WebServer extends React\Http\Server {

	private $wsConnFactory;

	/**
	 * @var MessageComponentInterface
	 */
	private $ratchetComponent = null;

	function __construct(ServerInterface $server, $wsConnFactory = null) {
		parent::__construct($server);
		$this->wsConnFactory = $wsConnFactory? $wsConnFactory: $this;
	}

	public function setRatchetComponent(MessageComponentInterface $component) {
		$this->ratchetComponent = $component;
	}

	public function handleRequest(Connection $conn, Request $request, $bodyBuffer) {
		if ($this->ratchetComponent && $this->isWebSocketHandshake($request)) {
			$this->handleWithRatchet($conn, $request);
		} else {
			parent::handleRequest($conn, $request, $bodyBuffer);
		}
	}

	public function isWebSocketHandshake(Request $request) {
		$headers = $request->getHeaders();
		return isset($headers['Upgrade']) &&
			strcasecmp($headers['Upgrade'], 'websocket') == 0;
	}

	public function toRequestString(Request $request) {
		$method = $request->getMethod();
		$path = $request->getPath();
		$version = $request->getHttpVersion();
		$headerData = "$method $path HTTP/$version\r\n";
		foreach ($request->getHeaders() as $header => $value) {
			$headerData .= "$header: $value\r\n";
		}
		$headerData .= "\r\n";
		return $headerData;
	}

	public function handleWithRatchet(Connection $conn, Request $request) {
		$conn->removeAllListeners();
		$this->wsConnFactory->createWebSocketConnection($conn);
		$conn->emit('data', array($this->toRequestString($request), $conn));
	}

	public function createWebSocketConnection(Connection $conn) {
		new WebSocketConnection($this->ratchetComponent, $conn);
	}
}
