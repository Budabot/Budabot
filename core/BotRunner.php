<?php

namespace Budabot\Core;

use LoggerConfiguratorDefault;
use Logger;

// isWindows is a little utility function to check
// whether the bot is running Windows or something
// else: returns true if under Windows, else false
function isWindows() {
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		return true;
	} else {
		return false;
	}
}

class BotRunner {

	// budabot's current version
	public $version = "3.5_RC1";

	private $argv = array();

	public function __construct($argv) {
		$this->argv = $argv;

		global $version;
		$version = $this->version;
	}

	public function run() {
		// set default timezone
		date_default_timezone_set("UTC");

		echo $this->getInitialInfoMessage();
		$this->loadPhpExtensions();
		
		// these must happen first since the classes that are loaded may be used by processes below
		$this->loadPhpLibraries();
		$this->loadEssentialCoreClasses();

		// load $vars
		global $vars;
		$vars = $this->getConfigVars();
		$logFolderName = $vars['name'] . '.' . $vars['dimension'];

		$this->setErrorHandling($logFolderName);

		$this->showSetupDialog();
		$this->canonicalizeBotCharacterName();

		$this->configureLogger($logFolderName);

		$this->setWindowTitle();

		LegacyLogger::log('INFO', 'StartUp', "Starting {$vars['name']} ($this->version) on RK{$vars['dimension']}...");

		$classLoader = new ClassLoader($vars['module_load_paths']);
		Registry::injectDependencies($classLoader);
		$classLoader->loadInstances();

		$this->connectToDatabase();
		$this->clearDatabaseInformation();

		$this->runUpgradeScripts();

		list($server, $port) = $this->getServerAndPort($vars);
		
		$chatBot = Registry::getInstance('chatBot');
		
		// startup core systems and load modules
		$chatBot->init($vars);
		
		// when using AOChatProxy, wait 10s before connecting to give AOChatProxy time to reset
		if ($vars['use_proxy'] == 1) {
			LegacyLogger::log('INFO', 'StartUp', "Waiting 10 seconds for AOChatProxy to reset...");
			sleep(10);
		}
		
		// connect to ao chat server
		$chatBot->connectAO($vars['login'], $vars['password'], $server, $port);
		
		// clear login credentials
		unset($vars['login']);
		unset($vars['password']);

		// pass control to Budabot class
		$chatBot->run();
	}

	private function getInitialInfoMessage() {
		return "\n\n\n\n\n
**************************************************
Budabot {$this->version}

Project Site:     https://github.com/Budabot/Budabot
Support Forum:    http://www.budabot.com/forum
In-Game Contact:  Tyrence27
**************************************************
\n";
	}

	private function loadPhpExtensions() {
		if (isWindows()) {
			// Load database and socket extensions
			dl("php_sockets.dll");
			dl("php_pdo_sqlite.dll");
			dl("php_pdo_mysql.dll");
		} else {
			// Load database extensions, if not already loaded
			// These are normally present in a modern Linux system--this is a safeguard
			if (!extension_loaded('pdo_sqlite')) {
				@dl('pdo_sqlite.so');
			}
			if (!extension_loaded('pdo_mysql')) {
				@dl('pdo_mysql.so');
			}
		}
	}

	protected function getConfigVars() {
		require_once 'ConfigFile.class.php';

		// Load the config
		$configFilePath = $this->argv[1];
		global $configFile;
		$configFile = new ConfigFile($configFilePath);
		$configFile->load();
		$vars = $configFile->getVars();
		return $vars;
	}

	private function setErrorHandling($logFolderName) {
		error_reporting(E_ALL & ~E_STRICT & ~E_WARNING & ~E_NOTICE);
		ini_set("log_errors", 1);
		ini_set('display_errors', 1);
		ini_set("error_log", "./logs/" . $logFolderName . "/php_errors.log");
	}

	private function loadPhpLibraries() {
		require_once './lib/addendum-0.4.1/annotations.php';
		require_once './lib/Net_SmartIRC-1.0.2/SmartIRC.php';
		require_once './lib/vendor/autoload.php';
		require_once './lib/LoggerAppenderBuffer.php';
	}

	private function loadEssentialCoreClasses() {
		require_once './core/Registry.class.php';
		require_once './core/ClassLoader.class.php';
		require_once './core/LegacyLogger.class.php';
		require_once './core/LoggerWrapper.class.php';
		require_once './core/annotations.php';
	}

	private function showSetupDialog() {
		if ($this->shouldShowSetup()) {
			global $vars;
			include "./core/SETUP/setup.php";
		}
	}

	private function shouldShowSetup() {
		global $vars;
		return $vars['login'] == "" || $vars['password'] == "" || $vars['name'] == "";
	}

	private function canonicalizeBotCharacterName() {
		global $vars;
		$vars["name"] = ucfirst(strtolower($vars["name"]));
	}

	// Configure log files to be separate for each bot
	private function configureLogger($logFolderName) {
		$configurator = new LoggerConfiguratorDefault();
		$config = $configurator->parse('conf/log4php.xml');
		$file = $config['appenders']['defaultFileAppender']['params']['file'];
		$file = str_replace("./logs/", "./logs/" . $logFolderName . "/", $file);
		$config['appenders']['defaultFileAppender']['params']['file'] = $file;
		Logger::configure($config);
	}

	private function setWindowTitle() {
		// Set the title of the command prompt window in Windows
		if (isWindows()) {
			global $vars;
			system("title {$vars['name']} - Budabot");
		}
	}

	private function connectToDatabase() {
		global $vars;
		$db = Registry::getInstance('db');
		$db->connect($vars["DB Type"], $vars["DB Name"], $vars["DB Host"], $vars["DB username"], $vars["DB password"]);
	}

	private function clearDatabaseInformation() {
		global $vars;
		unset($vars["DB Type"]);
		unset($vars["DB Name"]);
		unset($vars["DB Host"]);
		unset($vars["DB username"]);
		unset($vars["DB password"]);
	}

	private function runUpgradeScripts() {
		if (file_exists('upgrade.php')) {
			include 'upgrade.php';
		}
	}

	protected function getServerAndPort($vars) {
		// Choose server
		if ($vars['use_proxy'] == 1) {
			// For use with the AO chat proxy ONLY!
			$server = $vars['proxy_server'];
			$port = $vars['proxy_port'];
		} else if ($vars["dimension"] == 4) {
			$server = "chat.dt.funcom.com";
			$port = 7109;
		} else if ($vars["dimension"] == 5) {
			$server = "chat.d1.funcom.com";
			$port = 7105;
		} else {
			LegacyLogger::log('ERROR', 'StartUp', "No valid server to connect with! Available dimensions are 4 and 5.");
			sleep(10);
			die();
		}
		return array($server, $port);
	}
}
