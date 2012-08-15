<?php

class Registry {
	private static $repo = array();
	private static $repo2 = array();

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
		LegacyLogger::log("DEBUG", "Registry", "Requesting instance for '$name'");

		$instance = @Registry::$repo2[$name];
		if ($instance != null) {
			LegacyLogger::log("DEBUG", "Registry", "Using cache for '$name'");
		} else {
			$instance = Registry::$repo[$name];
			if ($instance == null) {
				LegacyLogger::log("WARN", "Registry", "Could not find instance for '$name'");
			} else {
				// this is to handle circular dependencies
				if (isset($set[$name])) {
					return $set[$name];
				}
				$set[$name] = $instance;

				Registry::injectDependencies($instance, $set);

				Registry::$repo2[$name] = $instance;
			}
		}

		if (USE_RUNKIT_CLASS_LOADING === true) {
			Registry::importChanges($instance);
		}

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
			} else if ($property->hasAnnotation('Logger')) {
				if (@$property->getAnnotation('Logger')->value != '') {
					$tag = $property->getAnnotation('Logger')->value;
				} else {
					$tag = $reflection->name;
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
		runkit_import($reflection->getFileName(), RUNKIT_IMPORT_CLASSES | RUNKIT_IMPORT_OVERRIDE);
	}
}

?>
