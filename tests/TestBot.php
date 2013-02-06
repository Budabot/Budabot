<?php

// run this as php.exe tests\TestBot.php <path to php-file>

define('ROOT_PATH', __DIR__ . '/..');

require 'helpers/TestBotRunner.php';
require 'helpers/ContextHelpers.php';

// print child processes' stdout to current cmd prompt
ContextHelpers::$parameters['budabot_log'] = true;
ContextHelpers::$parameters['aochatserver_log'] = true;

ContextHelpers::loadConfigVariables();
ContextHelpers::startAOChatServer();
ContextHelpers::startBudabot();
ContextHelpers::startRunnerRpcStub();
ContextHelpers::setupAdminnoobPorkTestData();
ContextHelpers::waitForBudabotBeReady();

if ($argc >= 2) {
	require $argv[1];
}

while (true) {
	sleep(1);
}
