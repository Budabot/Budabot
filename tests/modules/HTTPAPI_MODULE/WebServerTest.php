<?php

require_once 'Phake.php';
require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . '/../../../lib/vendor/autoload.php';
require_once __DIR__ . '/../../helpers/BudabotTestCase.php';
require_once __DIR__ . '/../../../core/HTTPAPI_MODULE/WebServer.php';

interface MockRequest {
}

interface MockResponse {
	function writeHead();
	function end();
}

class WebServerTest extends \BudabotTestCase {

	/**
	 * @var WebServer
	 */
	private $server;
	private $wsConnCreated = false;

	function setUp() {
		$socketServer = Phake::mock('React\Socket\Server');
		$this->server = new WebServer($socketServer, $this);
		$this->serverReflection = new ReflectionClass($this->server);
	}

	public function createWebSocketConnection($conn) {
		$this->wsConnCreated = true;
	}

	function mockRequestWithHeaders($headers) {
		$request = Phake::mock('React\Http\Request');
		Phake::when($request)->getHeaders()->thenReturn($headers);
		return $request;
	}

	function testExtendsReactHttpServer() {
		$this->assertEquals('React\Http\Server', $this->serverReflection
			->getParentClass()->getName());
	}

	function testOverridesHandleRequest() {
		$this->assertEquals('WebServer', $this->serverReflection
			->getMethod('handleRequest')->getDeclaringClass()->getName());
	}

	function testHandleWithRatchetRemovesEventListeners() {
		$conn = Phake::mock('React\Socket\Connection');
		$this->server->handleWithRatchet($conn, new \React\Http\Request('GET', '/'));
		Phake::verify($conn)->removeAllListeners();
	}

	function testHandleWithRatchetCreatesWebSocketConnection() {
		$conn = Phake::mock('React\Socket\Connection');
		$this->server->handleWithRatchet($conn, new \React\Http\Request('GET', '/'));
		$this->assertTrue($this->wsConnCreated);
	}

	function testHandleWithRatchetEmitsHeaderData() {
		$conn = Phake::mock('React\Socket\Connection');
		$request = new \React\Http\Request('GET', '/');
		$requestString = $this->server->toRequestString($request);
		$this->server->handleWithRatchet($conn, new \React\Http\Request('GET', '/'));
		Phake::verify($conn)->emit('data', array($requestString, $conn));
	}

	function testIsWebSocketHandshakeReturnsFalseWithNoHeaders() {
		$this->assertFalse($this->server->isWebSocketHandshake(
			$this->mockRequestWithHeaders(array())));
	}

	function testIsWebSocketHandshakeReturnsTrueWhenRequestHasWsUpgradeHeader() {
		$this->assertTrue($this->server->isWebSocketHandshake($this->mockRequestWithHeaders(array(
			'Upgrade' => 'websocket'
		))));
	}

	function testIsWebSocketHandshakeReturnsFalseWhenRequestHasWrongUpgradeHeader() {
		$this->assertFalse($this->server->isWebSocketHandshake($this->mockRequestWithHeaders(array(
			'Upgrade' => 'foo'
		))));
	}

	function testToHeaderStringReturnsValidHttpRequestString() {
		$request = new \React\Http\Request('GET', '/', array(), '1.1', array(
			'Upgrade' => 'websocket'
		));
		$this->assertEquals(
			"GET / HTTP/1.1\r\n" .
			"Upgrade: websocket\r\n" .
			"\r\n",
			$this->server->toRequestString($request));
	}
}
