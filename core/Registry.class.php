<?php

namespace Budabot\Core;

use ReflectionAnnotatedClass;
use ReflectionClass;

class Registry {
	private static $repo = array();

	public static function setInstance($name, $obj) {
		$name = strtolower($name);
		LegacyLogger::log("DEBUG", "Registry", "Adding instance '$name'");
		Registry::$repo[$name] = $obj;
	}
	
	public static function formatName($name) {
		$name = strtolower($name);
		$array = explode("\\", $name);
		return array_pop($array);
	}

	public static function instanceExists($name) {
		$name = strtolower($name);

		return isset(Registry::$repo[$name]);
	}

	public static function getInstance($name, $reload = false) {
		$name = strtolower($name);
		LegacyLogger::log("DEBUG", "Registry", "Requesting instance for '$name'");

		$instance = Registry::$repo[$name];
		if ($instance == null) {
			LegacyLogger::log("WARN", "Registry", "Could not find instance for '$name'");
		}

		if ($instance !== null && (USE_RUNKIT_CLASS_LOADING === true || $reload === true)) {
			Registry::importChanges($instance);
			Registry::injectDependencies($instance);
		}

		return $instance;
	}

	public static function injectDependencies($instance) {
		// inject other instances that are annotated with @Inject
		$reflection = new ReflectionAnnotatedClass($instance);
		forEach ($reflection->getProperties() as $property) {
			if ($property->hasAnnotation('Inject')) {
				if ($property->getAnnotation('Inject')->value != '') {
					$dependencyName = $property->getAnnotation('Inject')->value;
				} else {
					$dependencyName = $property->name;
				}
				$dependency = Registry::getInstance($dependencyName);
				if ($dependency == null) {
					LegacyLogger::log("WARN", "Registry", "Could resolve dependency '$dependencyName'");
				} else {
					$instance->{$property->name} = $dependency;
				}
			} else if ($property->hasAnnotation('Logger')) {
				if (@$property->getAnnotation('Logger')->value != '') {
					$tag = $property->getAnnotation('Logger')->value;
				} else {
					$array = explode("\\", $reflection->name);
					$tag = array_pop($array);
				}
				$instance->{$property->name} = new LoggerWrapper($tag);
			}
		}
	}

	public static function importChanges($instance) {
		try {
			$reflection = new ReflectionClass($instance);
		} catch(ReflectionException $e) {
			LegacyLogger::log("WARN", "Registry", "RUNKIT: Failed to reflect class, reason was: '" . $e->getMessage() . "'");
			return;
		}
		LegacyLogger::log("DEBUG", "Registry", "Re-importing file '" . $reflection->getFileName() . "'");
		runkit_import($reflection->getFileName(), RUNKIT_IMPORT_CLASSES | RUNKIT_IMPORT_FUNCTIONS | RUNKIT_IMPORT_OVERRIDE);
	}
	
	public static function getAllInstances() {
		return self::$repo;
	}
}

?>
