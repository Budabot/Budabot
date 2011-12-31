<?php

class Registry {
	private static $repo = array();
	
	public static function setInstance($name, &$obj) {
		$name = strtolower($name);
		Registry::$repo[$name] = $obj;
	}
	
	public static function instanceExists($name) {
		$name = strtolower($name);

		if (isset(Registry::$repo[$name])) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function getInstance($name, $set = array()) {
		$name = strtolower($name);

		$instance = Registry::$repo[$name];
		if ($instance == null) {
			return null;
		}
		
		// this is to handle circular dependencies
		if (isset($set[$name])) {
			return $set[$name];
		}
		$set[$name] = $instance;
		
		Registry::injectDependencies($instance, $set);
		return $instance;
	}
	
	public static function injectDependencies(&$instance, $set = array()) {
		// inject other instances that are annotated with @Inject
		$reflection = new ReflectionAnnotatedClass($instance);
		forEach ($reflection->getProperties() as $property) {
			if ($property->hasAnnotation('Inject')) {
				if ($property->getAnnotation('Inject')->value != '') {
					$dependencyName = $property->getAnnotation('Inject')->value;
				} else {
					$dependencyName = $property->name;
				}
				$dependencyName = strtolower($dependencyName);
				$instance->{$property->name} = Registry::getInstance($dependencyName, $set);
			}
		}
	}
}

?>
