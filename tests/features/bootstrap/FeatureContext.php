<?php

use Behat\Behat\Context\ClosuredContextInterface,
	Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext,
	Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
	Behat\Gherkin\Node\TableNode;

define('ROOT_PATH', __DIR__ . '/../../..');

// load all composer dependencies
require_once ROOT_PATH . '/lib/vendor/autoload.php';
require_once ROOT_PATH . '/tests/helpers/ContextHelpers.php';


/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
	private static $enabledModules = array();

	/**
	 * Initializes context.
	 * Every scenario gets it's own context object.
	 *
	 * @param array $parameters context parameters (set them up through behat.yml)
	 */
	public function __construct(array $parameters) {
		ContextHelpers::$parameters = $parameters;
	}

	/**
	 * @BeforeSuite
	 * Prepare system for test suite before it runs.
	 */
	public static function prepareSuite() {
		ContextHelpers::loadConfigVariables();
		ContextHelpers::startAOChatServer();
		ContextHelpers::startBudabot();
		ContextHelpers::startRunnerRpcStub();
		ContextHelpers::setupAdminnoobPorkTestData();
		ContextHelpers::waitForBudabotBeReady();
	}

	/**
	 * @BeforeScenario
	 * Prepare system for scenario before it runs.
	 */
	public static function prepareScenario() {
		ContextHelpers::$chatServer->clearTellMessages();
	} 

	/**
	 * @Given /^"([^"]*)" module is enabled$/
	 */
	public function moduleIsEnabled($module) {
		if (!isset(self::$enabledModules[$module])) {
			ContextHelpers::$chatServer->sendTellMessageToBot(ContextHelpers::$vars['SuperAdmin'], "!config mod $module enable all");
			ContextHelpers::$chatServer->waitForTellMessageWithPhrases(array("Updated status of the module"));
			self::$enabledModules[$module] = true;
		}
	}

	/**
	 * @When /^I give command "([^"]*)"$/
	 */
	public function iGiveCommand($command) {
		ContextHelpers::$chatServer->sendTellMessageToBot(ContextHelpers::$vars['SuperAdmin'], $command);
	}

	/**
	 * @Then /^the response should contain phrases:$/
	 */
	public function theResponseShouldContainPhrases($table) {
		$phrases = array();
		foreach ($table->getHash() as $hash) {
			$phrases []= array_pop($hash);
		}
		ContextHelpers::$chatServer->waitForTellMessageWithPhrases($phrases);
	}

	/**
	 * @Then /^the response should contain phrase "([^"]*)"$/
	 */
	public function theResponseShouldContainPhrase($phrase) {
		ContextHelpers::$chatServer->waitForTellMessageWithPhrases(array($phrase));
	}

	/**
	 * @Given /^my public IP address is "([^"]*)"$/
	 */
	public function myPublicIpAddressIs($address) {
		ContextHelpers::$runnerRpcStub->givenRequestToUriReturnsResult(
			'http://automation.whatismyip.com/n09230945.asp', $address
		);
	}
}
