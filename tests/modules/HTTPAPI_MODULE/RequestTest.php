<?php

require_once 'Phake.php';
require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . '/../../../lib/vendor/autoload.php';
require_once __DIR__ . '/../../../core/HTTPAPI_MODULE/Request.php';

class RequestTest extends \PHPUnit_Framework_TestCase {

	private $wrapped;

	/**
	 * @var \Request
	 */
	private $request;

	function setUp() {
		$this->wrapped = Phake::mock('React\Http\Request');
		$this->request = new \Request($this->wrapped);
	}

	function testGetMethodReturnsWrappedResult() {
		Phake::when($this->wrapped)->getMethod()->thenReturn('post');

		$this->assertEquals('post', $this->request->getMethod());
		Phake::verify($this->wrapped)->getMethod();
	}

	function testGetPathReturnsWrappedResult() {
		Phake::when($this->wrapped)->getPath()->thenReturn('/foo');

		$this->assertEquals('/foo', $this->request->getPath());
		Phake::verify($this->wrapped)->getPath();
	}

	function testGetQueryReturnsWrappedResult() {
		$query = array('foo' => 'baz');
		Phake::when($this->wrapped)->getQuery()->thenReturn($query);

		$this->assertEquals($query, $this->request->getQuery());
		Phake::verify($this->wrapped)->getQuery();
	}

	function testGetHttpVersionReturnsWrappedResult() {
		Phake::when($this->wrapped)->getHttpVersion()->thenReturn('1.1');

		$this->assertEquals('1.1', $this->request->getHttpVersion());
		Phake::verify($this->wrapped)->getHttpVersion();
	}

	function testGetHeadersReturnsWrappedResult() {
		$headers = array('Host' => 'localhost');
		Phake::when($this->wrapped)->getHeaders()->thenReturn($headers);

		$this->assertEquals($headers, $this->request->getHeaders());
		Phake::verify($this->wrapped)->getHeaders();
	}

	function testExpectsContinueReturnsWrappedResult() {
		Phake::when($this->wrapped)->expectsContinue()->thenReturn(true);

		$this->assertEquals(true, $this->request->expectsContinue());
		Phake::verify($this->wrapped)->expectsContinue();
	}

	function testIsReadableReturnsWrappedResult() {
		Phake::when($this->wrapped)->isReadable()->thenReturn(true);

		$this->assertEquals(true, $this->request->isReadable());
		Phake::verify($this->wrapped)->isReadable();
	}

	function testPauseCallsWrappedRequest() {
		$this->request->pause();
		Phake::verify($this->wrapped)->pause();
	}

	function testResumeCallsWrappedRequest() {
		$this->request->resume();
		Phake::verify($this->wrapped)->resume();
	}

	function testCloseCallsWrappedRequest() {
		$this->request->close();
		Phake::verify($this->wrapped)->close();
	}

	function testPipeCallsWrappedRequest() {
		$dest = Phake::mock('React\Stream\WritableStreamInterface');
		$options = array();
		$this->request->pipe($dest, $options);
		Phake::verify($this->wrapped)->pipe($dest, $options);
	}

	function testOnCallsWrappedRequest() {
		$this->request->on('data', array($this, 'foo'));
		Phake::verify($this->wrapped)->on('data', array($this, 'foo'));
	}

	function testGetCookiesCallsGetHeaders() {
		$this->request->getCookies();
		Phake::verify($this->wrapped)->getHeaders();
	}

	function testGetCookiesReturnsNoCookiesWithoutCookieHeader() {
		Phake::when($this->wrapped)->getHeaders()->thenReturn(array());
		$this->assertEquals(array(), $this->request->getCookies());
	}

	function testGetCookiesReturnsOneCookieWithOneCookieInCookieHeader() {
		Phake::when($this->wrapped)->getHeaders()->thenReturn(array('Cookie' => 'bar=foo'));
		$this->assertEquals(array('bar' => 'foo'), $this->request->getCookies());
	}

	function testGetCookiesReturnsTwoCookiesWithTwoCookiesInCookieHeader() {
		Phake::when($this->wrapped)->getHeaders()->thenReturn(array('Cookie' => 'bar=foo; baz=buz'));
		$this->assertEquals(array('bar' => 'foo', 'baz' => 'buz'), $this->request->getCookies());
	}

	function testGetCookieReturnsNullWithoutCookieHeader() {
		Phake::when($this->wrapped)->getHeaders()->thenReturn(array());
		$this->assertEquals(null, $this->request->getCookie('test'));
	}

	function testGetCookieReturnsNullWithNoSuchCookieInCookieHeader() {
		Phake::when($this->wrapped)->getHeaders()->thenReturn(array('Cookie' => 'bar=foo'));
		$this->assertEquals(null, $this->request->getCookie('test'));
	}

	function testGetCookieReturnsValueOfCookieFromCookieHeader() {
		Phake::when($this->wrapped)->getHeaders()->thenReturn(array('Cookie' => 'bar=foo'));
		$this->assertEquals('foo', $this->request->getCookie('bar'));
	}
}
