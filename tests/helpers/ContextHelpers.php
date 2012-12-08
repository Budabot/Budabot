<?php

use Behat\Behat\Context\ClosuredContextInterface,
	Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext,
	Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
	Behat\Gherkin\Node\TableNode;

// load all composer dependencies
require_once ROOT_PATH . '/lib/TestAOChatServer/AOChatServerStub.php';
require_once ROOT_PATH . '/tests/helpers/RpcServerStub.php';

/**
 * Helper functionality for feature context.
 */
class ContextHelpers
{
	public static $chatServer = null;
	public static $botProcess = null;
	public static $runnerRpcStub = null;
	public static $vars = array();
	public static $parameters = array();

	public static function loadConfigVariables() {
		self::$vars = json_decode(file_get_contents(
			ROOT_PATH . '/tests/BehatBotConfig.json'), true);
	}

	/**
	 * Starts Budabot instance.
	 * Calling this more than once has no effect unless the bot is not running.
	 */
	public static function startBudabot() {
		if (self::$botProcess) {
			return;
		}

		// delete old DB-file if it exists
		@unlink(ROOT_PATH . '/data/' . self::$vars['DB Name']);

		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$phpExec = realpath(ROOT_PATH . "\\win32\\php.exe") . " -c php-win.ini";
		} else {
			$phpExec = "php";
		}

		// start budabot instance
		$process = new Process();
		$process->setCommand("$phpExec -f tests/helpers/test_main.php");
		
		$path = self::$parameters['budabot_log'];
		if (is_string($path)) {
			$file = fopen($path, 'w');
			$process->setDescriptorspec(array(
				1 => $file,
				2 => $file
			));
		} else if ($path) {
			$process->setDescriptorspec(array());
		} else {
			$process->setDescriptorspec(array(
				1 => array('file', 'nul', 'w'),
				2 => array('file', 'nul', 'w')
			));
		}

		$process->setWorkingDir(ROOT_PATH);
		if (!$process->start()) {
			throw new Exception("Failed to start Budabot!");
		}

		// make sure that the bot instance is terminated on exit
		register_shutdown_function(function() use ($process) {
			$process->stop();
		});

		self::$botProcess = $process;
	}

	public static function waitForBudabotBeReady() {
		// wait for the bot instance to be ready
		self::$chatServer->waitPrivateMessage("Logon Complete :: All systems ready to use.");

		self::$chatServer->buddyLogin(self::$vars['SuperAdmin']);

		// check that the bot is ready to accept commands
		self::$chatServer->sendTellMessageToBot(self::$vars['SuperAdmin'], "hello botty");
		self::$chatServer->waitForTellMessageWithPhrases(array("Unknown command"));
	}

	/**
	 * Starts fake AOChat server so that Budabot can connect to it.
	 * Calling this more than once has no effect unless the server is not running.
	 */
	public static function startAOChatServer() {
		if (self::$chatServer) {
			return;
		}

		self::$chatServer = new AOChatServerStub();
		self::$chatServer->startServer(self::$vars['override_chat_server_port'],
			self::$vars['aochatserver_rpc_port'], self::$parameters['aochatserver_log']);

		// make sure that the server is stopped on exit
		register_shutdown_function(array(self::$chatServer, 'stopServer'));
		
		self::$chatServer->setAccountCharacters(array(self::$vars['name']));
	}

	public static function startRunnerRpcStub() {
		self::$runnerRpcStub = new RpcServerStub();
		self::$runnerRpcStub->startServer(self::$vars['testbotrunner_rpc_port']);
	}

	public static function setupAdminnoobPorkTestData() {
		self::$runnerRpcStub->givenRequestToUriReturnsResult(
			'http://people.anarchy-online.com/character/bio/d/1/name/adminnoob/bio.xml',
			file_get_contents(ROOT_PATH . '/tests/testdata/pork/adminnoob.xml')
		);
	}
}
