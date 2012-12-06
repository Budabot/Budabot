<?php

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
	public $version = "3.0_RC3";

	private $argv = array();
	private $vars = array();

	public function __construct($argv) {
		$this->argv = $argv;

		global $version;
		$version = $this->version;
	}

	public function run() {
		$this->setDefaultTimeZone();

		echo $this->getInitialInfoMessage();
		$this->loadPhpExtensions();

		global $vars;
		$vars = $this->getConfigVars();

		$this->setErrorLevel();
		$this->setErrorLogFile();

		$this->loadPhpLibraries();
		$this->loadEssentialCoreClasses();

		$this->showSetupDialog();
		$this->canonicalizeBotCharacterName();

		$this->configureLogger();

		$this->setWindowTitle();

		$this->logStartupMessage();

		$this->createGlobalInstances();

		$this->connectToDatabase();
		$this->clearDatabaseInformation();

		$this->runUpgradeScripts();

		list($server, $port) = $this->getServerAndPort();
		$this->connectToAoChatServer($server, $port);
		$this->clearAoLoginCredentials();
		$this->startBot();
	}

	private function setDefaultTimeZone() {
		date_default_timezone_set("UTC");
	}

	private function getInitialInfoMessage() {
		return "\n\n\n\n\n
**************************************************
Budabot {$this->version}

Project site:  http://code.google.com/p/budabot2
Support forum: http://www.budabot.com/forum
Chat:          #budabot on irc.funcom.com, or
			   /tell Budanet !join
Contacts:      Tyrence, Marebone
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

	private function getConfigVars() {
		require_once 'ConfigFile.class.php';

		// Load the config
		$configFilePath = $this->argv[1];
		$configFile = new ConfigFile($configFilePath);
		$configFile->load();
		$vars = $configFile->getVars();
		return $vars;
	}

	private function setErrorLevel() {
		error_reporting(E_ALL & ~E_STRICT & ~E_WARNING & ~E_NOTICE);
	}

	private function setErrorLogFile() {
		ini_set("log_errors", 1);
		ini_set("error_log", "./logs/" . $this->getLogFolderName() . "/php_errors.log");
	}

	private function getLogFolderName() {
		global $vars;
		return $vars['name'] . '.' . $vars['dimension'];
	}

	private function loadPhpLibraries() {
		require_once './lib/addendum-0.4.1/annotations.php';
		require_once './lib/apache-log4php-2.2.0/Logger.php';
		require_once './lib/Net_SmartIRC-1.0.2/SmartIRC.php';
		require_once './lib/ReverseFileReader.class.php';
	}

	private function loadEssentialCoreClasses() {
		require_once './core/Registry.class.php';
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

	private function configureLogger() { // Configure log files to be separate for each bot
		$configurator = new LoggerConfiguratorDefault();
		$config = $configurator->parse('conf/log4php.xml');
		$file = $config['appenders']['defaultFileAppender']['params']['file'];
		$file = str_replace("./logs/", "./logs/" . $this->getLogFolderName() . "/", $file);
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

	private function logStartupMessage() {
		global $vars;
		LegacyLogger::log('INFO', 'StartUp', "Starting {$vars['name']} on RK{$vars['dimension']}...");
	}

	private function createGlobalInstances() {
		$newInstances = Registry::getNewInstancesInDir("./core");
		forEach ($newInstances as $name => $className) {
			Registry::setInstance($name, new $className);
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
			//unlink('upgrade.php');
		}
	}

	private function getServerAndPort() {
		global $vars;
		// Choose server
		if ($vars['use_proxy'] === 1) {
			// For use with the AO chat proxy ONLY!
			$server = $vars['proxy_server'];
			$port = $vars['proxy_port'];
		} else if ($vars["dimension"] == 1) {
			$server = "chat.d1.funcom.com";
			$port = 7101;
		} else if ($vars["dimension"] == 2) {
			$server = "chat.d2.funcom.com";
			$port = 7102;
		} else if ($vars["dimension"] == 4) {
			$server = "chat.dt.funcom.com";
			$port = 7109;
		} else {
			LegacyLogger::log('ERROR', 'StartUp', "No valid server to connect with! Available dimensions are 1, 2 and 4.");
			sleep(10);
			die();
		}

		// override $server and $port if overriding variables are present (used for integration tests)
		if (isset($vars['override_chat_server_host'])) {
			$server = $vars['override_chat_server_host'];
		}
		if (isset($vars['override_chat_server_port'])) {
			$port = $vars['override_chat_server_port'];
		}

		return array($server, $port);
	}

	private function connectToAoChatServer($server, $port) {
		global $vars;
		$chatBot = Registry::getInstance('chatBot');
		$chatBot->init($vars);
		$chatBot->connectAO($vars['login'], $vars['password'], $server, $port);
	}

	private function clearAoLoginCredentials() {
		global $vars;
		unset($vars['login']);
		unset($vars['password']);
	}

	private function startBot() {
		global $vars;
		$chatBot = Registry::getInstance('chatBot');
		if (isset($vars['disable_flood_limiting']) && $vars['disable_flood_limiting']) {
			$chatBot->chatqueue->increment = 0;
		}
		$chatBot->run();
	}
}
