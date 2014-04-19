<?php

require_once 'Phake.php';
require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . '/../../../lib/vendor/autoload.php';
require_once __DIR__ . '/../../helpers/BudabotTestCase.php';
require_once __DIR__ . '/../../../core/HTTP_SERVER_MODULE/SessionStorage.php';

use Budabot\Core\Modules\SessionStorage;

class SessionStorageTest extends \BudabotTestCase {

	/**
	 * @var SessionStorage
	 */
	private $storage;

	function setUp() {
		$this->storage = new SessionStorage();
	}

	function testIsAutoInstanced() {
		$this->assertTrue($this->isAutoInstanced($this->storage));
	}

	function testCreateSessionReturnsNewId() {
		$id1 = $this->storage->createSession();
		$id2 = $this->storage->createSession();
		$this->assertNotEquals($id1, $id2);
	}

	function testHasSessionReturnsFalseWhenNoSuchSessionIdExist() {
		$this->assertFalse($this->storage->hasSession('deadf00d'));
	}

	function testHasSessionReturnsFalseWithNullId() {
		$this->assertFalse($this->storage->hasSession(null));
	}

	function testHasSessionReturnsTrueWhenSessionIdExists() {
		$id = $this->storage->createSession();
		$this->assertTrue($this->storage->hasSession($id));
	}

	function testDestroySessionDestroysExistingSession() {
		$id = $this->storage->createSession();
		$this->storage->destroySession($id);
		$this->assertFalse($this->storage->hasSession($id));
	}

	function testSetGetDataReturnsNullWithNonExistingSession() {
		$this->storage->setData('deadf00d', 'foo', 'bar');
		$this->assertNull($this->storage->getData('deadf00d', 'foo'));
	}

	function testSetGetDataReturnsSameValueWithExistingSession() {
		$id = $this->storage->createSession();
		$this->storage->setData($id, 'foo', 'bar');
		$this->assertEquals('bar', $this->storage->getData($id, 'foo'));
	}
}
