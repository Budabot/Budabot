<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

define('ROOT_PATH', __DIR__ . '/../../..');

/**
 * Faked LegacyLogger class, needed for ConfigFile class.
 */
class LegacyLogger {
	public static function log($category, $tag, $message) {
		//print "$category, $tag, $message\n";
		// stop testing if error occurs
		if ($category == 'ERROR') {
			//throw new Exception($message);
		}
	}
}


// load all composer dependencies
require_once ROOT_PATH . '/lib/vendor/autoload.php';
require_once ROOT_PATH . '/lib/TestAOChatServer/AOChatServerStub.php';
require_once ROOT_PATH . '/core/ConfigFile.class.php';

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
	private static $chatServer = null;

	// this is the port where the fake aochat test server will listen for bot
	// to connect
	private static $chatServerPort  = 7000;

	// this is the port where the fake aochat server will listen for json-rpc
	// calls made from this test suite
	private static $chatJsonRpcPort = 8000;

	private static $superAdmin = 'Adminnoob';
	private static $dbFileName = 'test_budabot.db';

	/**
	 * Initializes context.
	 * Every scenario gets it's own context object.
	 *
	 * @param array $parameters context parameters (set them up through behat.yml)
	 */
	public function __construct(array $parameters)
	{
		// Initialize your context here
	}

	/**
	 * @Given /^the bot is running$/
	 */
	public function theBotIsRunning() {
		self::startAOChatServer();
		self::startBudabot();
	}

	/**
	 * @Given /^I am logged in$/
	 */
	public function iAmLoggedIn() {
		throw new PendingException();
	}

	/**
	 * @When /^I give command "([^"]*)"$/
	 */
	public function iGiveCommand($arg1) {
		throw new PendingException();
	}

	/**
	 * @Then /^the response should contain words:$/
	 */
	public function theResponseShouldContainWords(TableNode $table) {
		throw new PendingException();
	}

	/**
	 * @Then /^the response should contain word "([^"]*)"$/
	 */
	public function theResponseShouldContainWord($arg1) {
		throw new PendingException();
	}

	/**
	 * Starts Budabot instance.
	 * Calling this more than once has no effect unless the bot is not running.
	 */
	private static function startBudabot() {
		// delete old DB-file if it exists
		@unlink(ROOT_PATH . '/data/' . self::$dbFileName);
		// delete old config-file if it exists
		@unlink(ROOT_PATH . '/config/test_config.php');

		// build new config file for the bot
		$config = new ConfigFile(ROOT_PATH . '/conf/test_config.php');
		$config->load();
		$config->setVar('login', 'testdummy');
		$config->setVar('password', '1234');
		$config->setVar('name', 'Testbot');
		$config->setVar('SuperAdmin', self::$superAdmin);
		$config->setVar('DB Name', self::$dbFileName);
		$config->setVar('override_chat_server_host', '127.0.0.1');
		$config->setVar('override_chat_server_port', self::$chatServerPort);
		$config->save();

		// start budabot
		throw new PendingException();
		
		// wait for the bot to be ready
		self::$chatServer->waitPrivateMessage(60 * 5 /* 5 minutes */,
			"Logon Complete :: All systems ready to use.");
	}

	/**
	 * Starts fake AOChat server so that Budabot can connect to it.
	 * Calling this more than once has no effect unless the server is not running.
	 */
	private static function startAOChatServer() {
		if (!self::$chatServer) {
			$server = new AOChatServerStub();
			$server->startServer(self::$chatServerPort, self::$chatJsonRpcPort);

			// make sure that the server is stopped on exit
			register_shutdown_function(function() use ($server) {
				$server->stopServer();
			});

			self::$chatServer = $server;
		}
	}
}
