<?php

class Set {
	/** @Inject */
	public $setting;

	public function __set($name, $value) {
        return $this->setting->save($name, $value);
    }

    public function __get($name) {
		return $this->setting->get($name);
	}
}