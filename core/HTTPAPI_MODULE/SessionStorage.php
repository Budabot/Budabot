<?php

/**
 * @Instance
 */
class SessionStorage {

	private $sessions = array();

	public function createSession() {
		$id = self::generateId();
		$s = new StdClass();
		$s->data = array();
		$this->sessions[$id] = $s;
		return $id;
	}

	private static function generateId() {
		session_start();
		$id = session_id();
		session_destroy();
		return $id;
	}

	public function hasSession($id) {
		return isset($this->sessions[$id]);
	}

	public function destroySession($id) {
		unset($this->sessions[$id]);
	}

	public function setData($id, $name, $value) {
		if ($this->hasSession($id)) {
			$this->sessions[$id]->data[$name] = $value;
		}
	}

	public function getData($id, $name) {
		if ($this->hasSession($id) && isset($this->sessions[$id]->data[$name])) {
			return $this->sessions[$id]->data[$name];
		}
		return null;
	}
}
