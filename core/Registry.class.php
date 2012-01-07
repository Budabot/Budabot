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

		if (isset(Registry::$repo[$name]) || isset(Registry::$repo2[$name])) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function getInstance($name, $set = array()) {
		$name = strtolower($name);
		Logger::log("DEBUG", "Registry", "Requesting instance for '$name'");

		if (USE_RUNKIT_CLASS_LOADING === true) {
			Registry::importChanges(ucfirst($name) . ".class.php");
		}
		
		$instance = Registry::$repo2[$name];
		if ($instance != null) {
			Logger::log("DEBUG", "Registry", "Using cache for '$name'");
			return $instance;
		}

		$instance = Registry::$repo[$name];
		if ($instance == null) {
			Logger::log("WARN", "Registry", "Could not find instance for '$name'");
			return null;
		}
		
		// this is to handle circular dependencies
		if (isset($set[$name])) {
			return $set[$name];
		}
		$set[$name] = $instance;
		
		Registry::injectDependencies($instance, $set);
		
		Registry::$repo2[$name] = $instance;
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
				if ($property->getAnnotation('Logger')->value != '') {
					$tag = $property->getAnnotation('Logger')->value;
				} else {
					$tag = $reflection->name;
				}
				$instance->{$property->name} = new LoggerWrapper($tag);
			}
		}
	}
	
	public static function importChanges($name) {
		$file = Registry::findInclude($name);
		if ($file !== null) {
			Logger::log("DEBUG", "Registry", "Re-importing file '$file'");
			runkit_import($file, RUNKIT_IMPORT_CLASSES | RUNKIT_IMPORT_OVERRIDE);
		}
	}
	
	public static function findInclude($name) {
		forEach (get_included_files() as $file) {
			if (preg_match("/" . preg_quote($name) . "$/i", $file)) {
				return $file;
			}
		}
		return null;
	}
}

class LoggerWrapper {
	private $tag;

	public function __construct($tag) {
		$this->tag = $tag;
	}
	
	public function log($category, $message) {
		Logger::log($category, $this->tag, $message);
	}
	
	public function log_chat($channel, $sender, $message) {
		Logger::log_chat($channel, $sender, $message);
	}
}

?>
