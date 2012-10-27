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
	private static $botProcess = null;

	// this is the port where the fake aochat test server will listen for bot
	// to connect
	private static $chatServerPort  = 7000;

	// this is the port where the fake aochat server will listen for json-rpc
	// calls made from this test suite
	private static $chatJsonRpcPort = 8000;

	private static $botName    = 'Testbot';
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
	 * @BeforeSuite
	 * Prepare system for test suite before it runs.
	 */
	public static function prepareSuite()
	{
		self::startAOChatServer();
		self::startBudabot();
		self::$chatServer->buddyLogin(self::$superAdmin);
	}

	/**
	 * @BeforeScenario
	 * Prepare system for scenario before it runs.
	 */
	public static function prepareScenario()
	{
		self::$chatServer->clearTellMessagesOfCharacter(self::$superAdmin);
	} 

    /**
     * @Given /^"([^"]*)" module is enabled$/
     */
    public function moduleIsEnabled($module)
    {
		self::$chatServer->sendTellMessageToBot(self::$superAdmin, "!config mod $module enable all");
    }

	/**
	 * @When /^I give command "([^"]*)"$/
	 */
	public function iGiveCommand($command) {
		self::$chatServer->sendTellMessageToBot(self::$superAdmin, $command);
	}

	/**
	 * @Then /^the response should contain phrases:$/
	 */
	public function theResponseShouldContainPhrases($table) {
		$phrases = array();
		foreach ($table->getHash() as $hash) {
			$phrases []= $hash['profession'];
		}
		self::$chatServer->waitForTellMessageWithPhrases(15, $phrases);
	}

	/**
	 * @Then /^the response should contain phrase "([^"]*)"$/
	 */
	public function theResponseShouldContainPhrase($phrase) {
		self::$chatServer->waitForTellMessageWithPhrases(15, array($phrase));
	}

	/**
	 * Starts Budabot instance.
	 * Calling this more than once has no effect unless the bot is not running.
	 */
	private static function startBudabot() {
		if (self::$botProcess) {
			return;
		}

		$configPath = ROOT_PATH . '/conf/test_config.php';

		// delete old DB-file if it exists
		@unlink(ROOT_PATH . '/data/' . self::$dbFileName);
		// delete old config-file if it exists
		@unlink($configPath);

		// build new config file for the bot
		$config = new ConfigFile($configPath);
		$config->load();
		$config->setVar('login', 'testdummy');
		$config->setVar('password', '1234');
		$config->setVar('name', self::$botName);
		$config->setVar('SuperAdmin', self::$superAdmin);
		$config->setVar('DB Name', self::$dbFileName);
		$config->setVar('override_chat_server_host', '127.0.0.1');
		$config->setVar('override_chat_server_port', self::$chatServerPort);
		$config->setVar('disable_flood_limiting', 1);
		$config->save();

		// start budabot instance
		$spec = array(
			1 => array('file', 'nul', 'w')
		);
		$process = proc_open("php -f mainloop.php $configPath", $spec, $pipes, ROOT_PATH, null, array('bypass_shell' => true));
		if (!is_resource($process)) {
			throw new Exception("Failed to start Budabot!");
		}

		$terminateBot = function() use ($process, $pipes) {
			forEach($pipes as $pipe) {
				fclose($pipe);
			}
			proc_terminate($process);
		};

		// wait for the bot instance to be ready
		try {
			self::$chatServer->waitPrivateMessage(60 * 5 /* 5 minutes */,
				"Logon Complete :: All systems ready to use.");
		} catch (Exception $e) {
			$terminateBot();
			throw $e;
		}

		// make sure that the bot instance is terminated on exit
		register_shutdown_function($terminateBot);

		self::$botProcess = $process;
	}

	/**
	 * Starts fake AOChat server so that Budabot can connect to it.
	 * Calling this more than once has no effect unless the server is not running.
	 */
	private static function startAOChatServer() {
		if (self::$chatServer) {
			return;
		}

		$server = new AOChatServerStub();
		$server->startServer(self::$chatServerPort, self::$chatJsonRpcPort);

		// make sure that the server is stopped on exit
		register_shutdown_function(function() use ($server) {
			$server->stopServer();
		});
		
		$server->setAccountCharacters(array(self::$botName));

		self::$chatServer = $server;
	}
}
