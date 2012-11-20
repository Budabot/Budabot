<?php

class AutoInject {
	public function __get($name) {
		if ($name == 'logger') {
			$instance = new LoggerWrapper(get_class($this));
		} else {
			$instance = Registry::getInstance($name);
		}
		if ($instance !== null) {
			$this->$name = $instance;
		}
		return $this->$name;
	}
}