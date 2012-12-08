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
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

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

	/**
	 * @Given /^RecipeBook service is online$/
	 */
	public function recipebookServiceIsOnline() {
		// test data for command '!rb blood plasma'
		ContextHelpers::$runnerRpcStub->givenRequestToUriReturnsResult(
			'http://aodevnet.com/recipes/api/search/kw/blood%20plasma/mode/default/format/json/bot/budabot',
			file_get_contents(ROOT_PATH . '/tests/testdata/recipebook/search_blood_plasma.json')
		);
		// test data for command '!rb non-existing thingy'
		ContextHelpers::$runnerRpcStub->givenRequestToUriReturnsResult(
			'http://aodevnet.com/recipes/api/search/kw/non-existing%20thingy/mode/default/format/json/bot/budabot',
			file_get_contents(ROOT_PATH . '/tests/testdata/recipebook/search_non_existing_thingy.json')
		);
		// test data for command '!rbshow 20'
		ContextHelpers::$runnerRpcStub->givenRequestToUriReturnsResult(
			'http://aodevnet.com/recipes/api/show/id/20/format/json/bot/budabot',
			file_get_contents(ROOT_PATH . '/tests/testdata/recipebook/recipe_20_blood_plasma.json')
		);
		// empty response for usage upload
		ContextHelpers::$runnerRpcStub->givenRequestToUriReturnsResult(
			'http://stats.jkbff.com/submitUsage.php', ''
		);
	}

	/**
	 * @Given /^stats service is online$/
	 */
	public function statsServiceIsOnline() {
		//throw new PendingException();
	}

	/**
	 * @When /^"([^"]*)" event is sent to "([^"]*)" module$/
	 */
	public function eventIsSentToModule($type, $module) {
		ContextHelpers::$runnerRpcStub->triggerEventInModule($module, $type);
	}

	/**
	 * @Then /^the received stats post contains all necessary information$/
	 */
	public function theStatsPostContainsAllNecessaryInformation() {
		$request = ContextHelpers::waitReceivedRequestForUri('http://stats.jkbff.com/submitUsage.php');
		parse_str($request, $params);
		$stats = json_decode($params['stats']);

		assertObjectHasAttribute('dimension', $stats->settings);
		assertObjectHasAttribute('is_guild_bot', $stats->settings);
		assertObjectHasAttribute('guildsize', $stats->settings);
		assertObjectHasAttribute('using_chat_proxy', $stats->settings);
		assertObjectHasAttribute('symbol', $stats->settings);
		assertObjectHasAttribute('spam_protection', $stats->settings);
		assertObjectHasAttribute('db_type', $stats->settings);
		assertObjectHasAttribute('bot_version', $stats->settings);
		assertObjectHasAttribute('using_svn', $stats->settings);
		assertObjectHasAttribute('os', $stats->settings);
		assertObjectHasAttribute('relay_enabled', $stats->settings);
		assertObjectHasAttribute('relay_type', $stats->settings);
		assertObjectHasAttribute('alts_inherit_admin', $stats->settings);
		assertObjectHasAttribute('bbin_status', $stats->settings);
		assertObjectHasAttribute('irc_status', $stats->settings);
		assertObjectHasAttribute('first_and_last_alt_only', $stats->settings);
		assertObjectHasAttribute('aodb_db_version', $stats->settings);
		assertObjectHasAttribute('guild_admin_access_level', $stats->settings);
		assertObjectHasAttribute('guild_admin_rank', $stats->settings);
		assertObjectHasAttribute('max_blob_size', $stats->settings);
		assertObjectHasAttribute('logon_delay', $stats->settings);
	}

}
