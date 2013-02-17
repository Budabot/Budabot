<?php

require_once 'Phake.php';
require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . '/../../../lib/vendor/autoload.php';
require_once __DIR__ . '/../../../core/HTTPAPI_MODULE/Request.php';
require_once __DIR__ . '/../../../core/HTTPAPI_MODULE/Response.php';
require_once __DIR__ . '/../../../core/HTTPAPI_MODULE/SessionStorage.php';
require_once __DIR__ . '/../../../core/HTTPAPI_MODULE/Session.php';

class SessionTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \Session
	 */
	private $session;

	/**
	 * @var \Request
	 */
	private $requestMock;

	/**
	 * @var \SessionStorage
	 */
	private $storageMock;

	/**
	 * @var \Response
	 */
	private $responseMock;

	function setUp() {
		$this->storageMock  = Phake::mock('SessionStorage');
		$this->requestMock  = Phake::mock('Request');
		$this->responseMock = Phake::mock('Response');

		$this->session = new Session(
			$this->storageMock,
			$this->requestMock,
			$this->responseMock
		);
	}

	function testStartChecksIfSessionIdIsPresentInCookie() {
		$this->session->start();
		Phake::verify($this->requestMock)->getCookie(Session::SESSION_NAME);
	}

	function testStartCreatesSessionIfSessionIdIsNotAvailable() {
		Phake::when($this->requestMock)->getCookie(Session::SESSION_NAME)->thenReturn(null);
		Phake::when($this->storageMock)->hasSession(null)->thenReturn(false);
		$this->session->start();
		Phake::verify($this->storageMock)->createSession();
	}

	function testStartCallsHasSessionIfSessionIdIsAvailable() {
		Phake::when($this->requestMock)->getCookie(Session::SESSION_NAME)->thenReturn('deadf00d');
		$this->session->start();
		Phake::verify($this->storageMock)->hasSession('deadf00d');
	}

	function testStartSetsCookieWhenNewSessionIsCreated() {
		Phake::when($this->storageMock)->createSession()->thenReturn('deadf00d');
		$this->session->start();

		Phake::verify($this->storageMock)->createSession();
		Phake::verify($this->responseMock)->setCookie(
			Session::SESSION_NAME, 'deadf00d', array('Path' => '/'));
	}

	function testIsStartedReturnsFalseIfSessionIdIsNotSet() {
		$this->assertFalse($this->session->isStarted());
	}

	function testIsStartedReturnsTrueIfSessionIdIsSet() {
		$this->session->setId('deadbeef');
		$this->assertTrue($this->session->isStarted());
	}

	function testIsStartedReturnsFalseIfSessionIsDestroyed() {
		$this->session->setId('deadbeef');
		$this->session->destroy();
		$this->assertFalse($this->session->isStarted());
	}

	function testDestroyDestroysSession() {
		$this->session->setId('deadbeef');
		$this->session->destroy();
		Phake::verify($this->storageMock)->destroySession('deadbeef');
	}

	function testGetDataReturnsNullIfNotStarted() {
		$this->assertNull($this->session->getData('foo'));
	}

	function testGetDataReturnsStorageValueWhenStarted() {
		$this->session->setId('deadf00d');
		Phake::when($this->storageMock)->getData('deadf00d', 'foo')->thenReturn('bar');
		$this->assertEquals('bar', $this->session->getData('foo'));
		Phake::verify($this->storageMock)->getData('deadf00d', 'foo');
	}

	function testSetDataDoesNotAccessStorageIfNotStarted() {
		$this->session->setData('foo', 'bar');
		Phake::verify($this->storageMock, Phake::never())->setData(
			$this->anything(), $this->anything(), $this->anything());
	}

	function testSetDataAccessesStorageIfStarted() {
		$this->session->setId('deadf00d');
		$this->session->setData('foo', 'bar');
		Phake::verify($this->storageMock)->setData('deadf00d', 'foo', 'bar');
	}
}
