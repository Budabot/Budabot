<?php

class Set {
	/** @Inject */
	public $settingManager;

	public function __set($name, $value) {
		return $this->settingManager->save($name, $value);
	}

	public function __get($name) {
		return $this->settingManager->get($name);
	}
}
