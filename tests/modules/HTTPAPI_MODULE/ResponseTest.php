<?php

require_once 'Phake.php';
require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . '/../../../lib/vendor/autoload.php';
require_once __DIR__ . '/../../../core/HTTP_SERVER_MODULE/Response.php';

use Budabot\Core\Modules\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var React\Http\Response
	 */
	private $wrapped;

	/**
	 * @var Budabot\Core\Modules\Response
	 */
	private $response;

	function setUp() {
		$this->wrapped = Phake::mock('React\Http\Response');
		$this->response = new Response($this->wrapped);
	}

	function testIsWritableReturnsWrappedResult() {
		Phake::when($this->wrapped)->isWritable()->thenReturn(true);
		$this->assertTrue($this->response->isWritable());
		Phake::verify($this->wrapped)->isWritable();
	}

	function testWriteContinueCallsWrappedResponse() {
		$this->response->writeContinue();
		Phake::verify($this->wrapped)->writeContinue();
	}

	function testWriteHeadCallsWrappedResponse() {
		$this->response->writeHead(200, array('Content-Type' => 'text/plain'));
		Phake::verify($this->wrapped)->writeHead(200, array('Content-Type' => 'text/plain'));
	}

	function testWriteHeadCallsWrappedResponseWithDefaultParameters() {
		$this->response->writeHead();
		Phake::verify($this->wrapped)->writeHead(200, array());
	}

	function testWriteCallsWrappedResponse() {
		$this->response->write('foo data');
		Phake::verify($this->wrapped)->write('foo data');
	}

	function testEndCallsWrappedResponse() {
		$this->response->end('foo data');
		Phake::verify($this->wrapped)->end('foo data');
	}

	function testEndCallsWrappedResponseWithDefaultParameter() {
		$this->response->end();
		Phake::verify($this->wrapped)->end(null);
	}

	function testCloseCallsWrappedResponse() {
		$this->response->close();
		Phake::verify($this->wrapped)->close();
	}

	function testOnCallsWrappedRequest() {
		$this->response->on('data', array($this, 'foo'));
		Phake::verify($this->wrapped)->on('data', array($this, 'foo'));
	}

	function testCookieIsInHeaders() {
		$this->response->setCookie('foo', 'bar');
		$this->response->writeHead(200, array());
		Phake::verify($this->wrapped)->writeHead(200, array('Set-Cookie' => 'foo=bar'));
	}

	function testCookieIsAddedToHeaders() {
		$this->response->setCookie('foo', 'bar');
		$this->response->writeHead(200, array('Content-Type' => 'text/plain'));
		Phake::verify($this->wrapped)->writeHead(200, array(
			'Set-Cookie' => 'foo=bar',
			'Content-Type' => 'text/plain'
		));
	}

	function testCookieWithExtraOptionIsAddedToHeaders() {
		$this->response->setCookie('foo', 'bar', array('Path' => '/'));
		$this->response->writeHead(200, array('Content-Type' => 'text/plain'));
		Phake::verify($this->wrapped)->writeHead(200, array(
			'Set-Cookie' => 'foo=bar; Path=/',
			'Content-Type' => 'text/plain'
		));
	}
}
