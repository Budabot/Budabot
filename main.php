<?php

	/*
	 ** This file is part of Budabot.
	 **
	 ** Budabot is free software: you can redistribute it and/org modify
	 ** it under the terms of the GNU General Public License as published by
	 ** the Free Software Foundation, either version 3 of the License, or
	 ** (at your option) any later version.
	 **
	 ** Budabot is distributed in the hope that it will be useful,
	 ** but WITHOUT ANY WARRANTY; without even the implied warranty of
	 ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 ** GNU General Public License for more details.
	 **
	 ** You should have received a copy of the GNU General Public License
	 ** along with Budabot. If not, see <http://www.gnu.org/licenses/>.
	*/

	// Define the version.
	$version = "3.0_Alpha";

	// Text block that is displayed when initially launching the process.
	echo "\n\n\n\n\n
	**************************************************
	Budabot $version

	Project site:  http://code.google.com/p/budabot2
	Support forum: http://www.budabot.com/forum
	Chat:          #budabot on irc.funcom.com, or
	               /tell Budanet !join
	Contacts:      Tyrence, Marebone, Laen
	**************************************************
	\n";

	date_default_timezone_set("UTC");

	// isWindows is a little utility function to check
	// whether the bot is running Windows or something
	// else: returns true if under Windows, else false.
	function isWindows() {
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			return true;
		} else {
			return false;
		}
	}

	if (isWindows()) {
		// Load database extensions and socket extension.
		dl("php_sockets.dll");
		dl("php_pdo_sqlite.dll");
		dl("php_pdo_mysql.dll");
	} else {
		// Load database extensions, if not already loaded.
		// These are normally present in a modern Linux system. This is a safeguard.
		if (!extension_loaded('pdo_sqlite')) {
			@dl('pdo_sqlite.so');
		}
		if (!extension_loaded('pdo_mysql')) {
			@dl('pdo_mysql.so');
		}
	}

	require_once './core/ConfigFile.class.php';

	// Load required files.
	$configFilePath = $argv[1];
	$configFile = new ConfigFile($configFilePath);
	$configFile->load();
	$vars = $configFile->getVars();

	/* function exceptions_error_handler($severity, $message, $filename, $lineno) {
		if (error_reporting() == 0) {
		return;
		}
		if (error_reporting() & $severity) {
		throw new ErrorException($message, 0, $severity, $filename, $lineno);
		}
	}
	set_error_handler('exceptions_error_handler'); */

	// Set error level.
	//error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING);
	error_reporting(E_ALL & ~E_STRICT & ~E_WARNING & ~E_NOTICE);
	//error_reporting(-1);

	ini_set("log_errors", 1);
	ini_set("error_log", "./logs/{$vars['name']}.{$vars['dimension']}/php_errors.log");

	require_once './lib/addendum-0.4.1/annotations.php';
	require_once './lib/apache-log4php-2.2.0/Logger.php';
	require_once './core/Registry.class.php';
	require_once './core/LegacyLogger.class.php';
	require_once './core/LoggerWrapper.class.php';
	require_once './core/annotations.php';

	require_once './core/PrivateChannelCommandReply.class.php';
	require_once './core/PrivateMessageCommandReply.class.php';
	require_once './core/GuildChannelCommandReply.class.php';
	require_once './core/StructuredMessage.class.php';
	require_once './core/LegacyController.class.php';
	require_once './core/LegacyEventProxy.class.php';
	require_once './core/LegacyCommandProxy.class.php';
	require_once './core/LegacySubcommandProxy.class.php';
	require_once './core/StopExecutionException.class.php';
	require_once './core/SQLException.class.php';

	require_once './core/AOChat.class.php';
	require_once './core/Budabot.class.php';
	require_once './core/DB.class.php';
	require_once './core/xml.php';
	require_once './core/MyCurl.class.php';
	require_once './core/AccessLevel.class.php';
	require_once './core/Admin.class.php';
	require_once './core/CommandManager.class.php';
	require_once './core/Subcommand.class.php';
	require_once './core/CommandAlias.class.php';
	require_once './core/EventManager.class.php';
	require_once './core/Setting.class.php';
	require_once './core/Help.class.php';
	require_once './core/BuddylistManager.class.php';
	require_once './core/Ban.class.php';
	require_once './core/Util.class.php';
	require_once './core/Text.class.php';
	require_once './core/SocketNotifier.class.php';
	require_once './core/AsyncHttp.class.php';
	require_once './core/Timer.class.php';

	// Show setup dialog.
	if ($vars['login'] == "" || $vars['password'] == "" || $vars['name'] == "") {
		include "./core/SETUP/setup.php";
	}

	$vars["name"] = ucfirst(strtolower($vars["name"]));

	// Configure log files to be separate for each bot.
	$configurator = new LoggerConfiguratorDefault();
	$config = $configurator->parse('conf/log4php.xml');
	$file = $config['appenders']['defaultFileAppender']['params']['file'];
	$file = str_replace("./logs/", "./logs/{$vars['name']}.{$vars['dimension']}/", $file);
	$config['appenders']['defaultFileAppender']['params']['file'] = $file;
	Logger::configure($config);

	// Set the title of the command prompt window in Windows.
	if (isWindows()) {
		system("title {$vars['name']} - Budabot");
	}

	LegacyLogger::log('INFO', 'StartUp', "Starting {$vars['name']} on RK{$vars['dimension']}...");

	// Choose server.
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

	// Create new objects.
	$db = new DB($vars["DB Type"], $vars["DB Name"], $vars["DB Host"], $vars["DB username"], $vars["DB password"]);
	if ($db->errorCode != 0) {
		LegacyLogger::log('ERROR', 'StartUp', "Error in creating database object: {$db->errorInfo}");
		sleep(5);
		die();
	}

	// Clear database information.
	unset($vars["DB Type"]);
	unset($vars["DB Name"]);
	unset($vars["DB Host"]);
	unset($vars["DB username"]);
	unset($vars["DB password"]);

	// Run upgrade scripts.
	if (file_exists('upgrade.php')) {
		include 'upgrade.php';
		//unlink('upgrade.php');
	}

	// Create global instances.
	Registry::setInstance('db', $db);
	Registry::setInstance('commandManager', new CommandManager);
	Registry::setInstance('subcommand', new Subcommand);
	Registry::setInstance('commandAlias', new CommandAlias);
	Registry::setInstance('eventManager', new EventManager);
	Registry::setInstance('help', new Help);
	Registry::setInstance('setting', new Setting);
	Registry::setInstance('buddylistManager', new BuddylistManager);
	Registry::setInstance('ban', new Ban);
	Registry::setInstance('accessLevel', new AccessLevel);
	Registry::setInstance('admin', new Admin);
	Registry::setInstance('text', new Text);
	Registry::setInstance('util', new Util);
	Registry::setInstance('timer', new Timer);
	Registry::setInstance('chatBot', new Budabot($vars));

	// Initialize connection to server.
	$chatBot = Registry::getInstance('chatBot');
	$chatBot->init();
	$chatBot->connectAO($vars['login'], $vars['password'], $server, $port);

	// Clear the login and the password.
	unset($vars['login']);
	unset($vars['password']);

	$chatBot->run();

?>
