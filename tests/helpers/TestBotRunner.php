<?php

require_once __DIR__ . '/../../lib/vendor/autoload.php';
require_once __DIR__ . '/../../core/BotRunner.php';
require_once __DIR__ . '/../../lib/TestAOChatServer/JSONRPCServer.php';
require_once 'RunnerRpcService.php';

use Budabot\Core\BotRunner;
use Budabot\Core\Registry;
use Budabot\Core\AsyncHttp;
use Budabot\Core\HttpRequest;

class TestBotRunner extends BotRunner {

	private $rpcService;

	protected function getConfigVars() {
		$json = file_get_contents('./tests/BehatBotConfig.json');
		return json_decode($json, true);
	}

	protected function getServerAndPort() {
		$vars = $this->getConfigVars();
		$server = $vars['override_chat_server_host'];
		$port = $vars['override_chat_server_port'];
		return array($server, $port);
	}

	protected function startBot() {
		$this->startRpcServer();
		$this->sendHttpRequestsToHttpServer();
		$this->disableAoChatFloodLimiting();

		parent::startBot();
	}

	private function startRpcServer() {
		$vars = $this->getConfigVars();
		$this->rpcService = new RunnerRpcService();
		Registry::injectDependencies($this->rpcService);
		$this->rpcService->start($vars['testbotrunner_rpc_port']);
	}

	private function sendHttpRequestsToHttpServer() {
		AsyncHttp::$overrideAddress = '127.0.0.1';
		AsyncHttp::$overridePort = Registry::getInstance('setting')->http_server_port;
		HttpRequest::$overridePathPrefix = '/tests';

		Registry::getInstance('settingManager')->registerChangeListener('http_server_port', function($a, $b, $newValue) {
			AsyncHttp::$overridePort = $newValue;
		});

		Registry::getInstance('httpServerController')->startHTTPServer();
	}

	private function disableAoChatFloodLimiting() {
		$chatBot = Registry::getInstance('chatBot');
		$chatBot->chatqueue->increment = 0;
	}
}
