<?php

namespace Budabot\Core;

use ReflectionAnnotatedClass;
use ReflectionClass;

class Registry {
	private static $repo = array();
	private static $dependencies = array();

	public static function setInstance($name, $obj) {
		$name = strtolower($name);
		LegacyLogger::log("DEBUG", "Registry", "Adding instance '$name'");
		Registry::$repo[$name] = $obj;
		self::injectDependencies($obj);
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
				$dependencyName = strtolower($dependencyName);
				self::$dependencies[$dependencyName] []= array($instance, $property->name);
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
		forEach (self::$repo as $instanceName => $instance) {
			if (isset(self::$dependencies[$instanceName]) && !empty(self::$dependencies[$instanceName])) {
				forEach (self::$dependencies[$instanceName] as $injection) {
					list($injectObject, $injectVariable) = $injection;
					$injectObject->{$injectVariable} = $instance;
				}
				unset(self::$dependencies[$instanceName]);
			}
		}
	}
	
	public static function checkForMissingDependencies() {
		forEach (self::$dependencies as $name => $arr) {
			$dependers = array();
			forEach ($arr as $obj) {
				list($injectObject, $injectVariable) = $obj;
				$dependers []= get_class($injectObject);
			}
			LegacyLogger::log("WARN", "Registry", "Could not find instance '$name' to inject to: ". implode(",", $dependers));
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
	
	public static function getNewInstancesInDir($path) {
		$original = get_declared_classes();
		if ($dir = dir($path)) {
			while (false !== ($file = $dir->read())) {
				if (!is_dir($path . '/' . $file) && preg_match("/\\.php$/i", $file)) {
					require_once "{$path}/{$file}";
				}
			}
			$dir->close();
		}
		$new = array_diff(get_declared_classes(), $original);

		$newInstances = array();
		forEach ($new as $className) {
			$reflection = new ReflectionAnnotatedClass($className);
			if ($reflection->hasAnnotation('Instance')) {
				if ($reflection->getAnnotation('Instance')->value != '') {
					$name = $reflection->getAnnotation('Instance')->value;
				} else {
					$name = Registry::formatName($className);
				}
				$newInstances[$name] = $className;
			}
		}
		return $newInstances;
	}
	
	public static function getAllInstances() {
		return self::$repo;
	}
}

?>
