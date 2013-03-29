<?php

namespace Budabot\Core;

class AutoInject {
	public function __get($name) {
		if ($name == 'logger') {
			$tag = Registry::formatName(get_class($this));
			$instance = new LoggerWrapper($tag);
		} else {
			$instance = Registry::getInstance($name);
		}
		if ($instance !== null) {
			$this->$name = $instance;
		}
		return $this->$name;
	}
}
