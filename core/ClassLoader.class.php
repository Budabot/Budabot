<?php

namespace Budabot\Core;

use ReflectionAnnotatedClass;

class ClassLoader {
	/** @Logger */
	public $logger;
	
	private $moduleLoadPaths;
	
	public function __construct($moduleLoadPaths) {
		$this->moduleLoadPaths = $moduleLoadPaths;
	}

	public function loadInstances() {
		$newInstances = $this->getNewInstancesInDir("./core");
		forEach ($newInstances as $name => $className) {
			Registry::setInstance($name, new $className);
		}
		
		$this->loadCoreModules();
		$this->loadUserModules();
		
		$this->logger->log('DEBUG', "Inject dependencies for all instances");
		forEach (Registry::getAllInstances() as $instance) {
			Registry::injectDependencies($instance);
		}
	}
	
	private function loadCoreModules() {
		// load the core modules, hard-code to ensure they are loaded in the correct order
		$this->logger->log('INFO', "Loading CORE modules...");
		$core_modules = array('CONFIG', 'SYSTEM', 'ADMIN', 'BAN', 'HELP', 'LIMITS', 'PLAYER_LOOKUP', 'FRIENDLIST', 'ALTS', 'USAGE', 'PREFERENCES', 'HTTP_SERVER_MODULE', 'PROFILE', 'COLORS');
		forEach ($core_modules as $MODULE_NAME) {
			$this->registerModule("./core", $MODULE_NAME);
		}
	}
	
	/**
	 * @name: loadUserModules
	 * @description: load all user modules
	 */
	private function loadUserModules() {
		$this->logger->log('INFO', "Loading USER modules...");
		forEach ($this->moduleLoadPaths as $path) {
			$this->logger->log('DEBUG', "Loading modules in path '$path'");
			if (file_exists($path) && $d = dir($path)) {
				while (false !== ($MODULE_NAME = $d->read())) {
					if ($this->isModuleDir($path, $MODULE_NAME)) {
						$this->registerModule($path, $MODULE_NAME);
					}
				}
				$d->close();
			}
		}
	}

	private function isModuleDir($path, $moduleName) {
		return $this->isValidModuleName($moduleName)
			&& is_dir("$path/$moduleName");
	}

	private function isValidModuleName($name) {
		return $name != '.' && $name != '..';
	}
	
	public function registerModule($baseDir, $MODULE_NAME) {
		// read module.ini file (if it exists) from module's directory
		if (file_exists("{$baseDir}/{$MODULE_NAME}/module.ini")) {
			$entries = parse_ini_file("{$baseDir}/{$MODULE_NAME}/module.ini");
			// check that current PHP version is greater or equal than module's
			// minimum required PHP version
			if (isset($entries["minimum_php_version"])) {
				$minimum = $entries["minimum_php_version"];
				$current = phpversion();
				if (strnatcmp($minimum, $current) > 0) {
					$this->logger->log('WARN', "Could not load module"
					." {$MODULE_NAME} as it requires at least PHP version '$minimum',"
					." but current PHP version is '$current'");
					return;
				}
			}
		}

		$newInstances = $this->getNewInstancesInDir("{$baseDir}/{$MODULE_NAME}");
		forEach ($newInstances as $name => $className) {
			$obj = new $className;
			$obj->moduleName = $MODULE_NAME;
			if (Registry::instanceExists($name)) {
				$this->logger->log('WARN', "Instance with name '$name' already registered--replaced with new instance");
			}
			Registry::setInstance($name, $obj);
		}

		if (count($newInstances) == 0) {
			$this->logger->log('ERROR', "Could not load module {$MODULE_NAME}. No classes found with @Instance annotation!");
			return;
		}
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
}
