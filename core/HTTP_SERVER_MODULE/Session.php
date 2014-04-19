<?php

namespace Budabot\Core\Modules;

class Session {

	const SESSION_NAME = 'SESSION_ID';

	/**
	 * @var SessionStorage
	 */
	private $storage;

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var Response
	 */
	private $response;

	private $id;

	public function __construct(SessionStorage $storage, Request $request, Response $response) {
		$this->storage = $storage;
		$this->request = $request;
		$this->response = $response;
	}

	/**
	 * Sets current session id, used for unit testing.
	 * @internal
	 */
	public function setId($id) {
		$this->id = $id;
	}

	public function start() {
		if ($this->isStarted()) {
			return;
		}

		$id = $this->request->getCookie(self::SESSION_NAME);
		if (!$this->storage->hasSession($id)) {
			$id = $this->storage->createSession();
			$this->response->setCookie(self::SESSION_NAME, $id, array(
				'Path' => '/'
			));
		}
		$this->id = $id;
	}

	public function destroy() {
		$this->storage->destroySession($this->id);
		$this->id = null;
	}

	public function isStarted() {
		return $this->id !== null;
	}

	public function getData($name) {
		if ($this->isStarted()) {
			return $this->storage->getData($this->id, $name);
		}
		return null;
	}

	public function setData($name, $value) {
		if ($this->isStarted()) {
			$this->storage->setData($this->id, $name, $value);
		}
	}
}
