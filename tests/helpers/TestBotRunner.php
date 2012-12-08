<?php

require_once __DIR__ . '/../../lib/vendor/autoload.php';
require_once __DIR__ . '/../../core/BotRunner.php';
require_once __DIR__ . '/../../lib/TestAOChatServer/JSONRPCServer.php';
require_once 'RunnerRpcService.php';

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
		$this->sendHttpRequestsToHttpApi();
		$this->disableAoChatFloodLimiting();

		parent::startBot();
	}

	private function startRpcServer() {
		$vars = $this->getConfigVars();
		$this->rpcService = new RunnerRpcService();
		Registry::injectDependencies($this->rpcService);
		$this->rpcService->start($vars['testbotrunner_rpc_port']);
	}

	private function sendHttpRequestsToHttpApi() {
		AsyncHttp::$overrideAddress = '127.0.0.1';
		AsyncHttp::$overridePort = Registry::getInstance('setting')->httpapi_port;
		HttpRequest::$overridePathPrefix = '/tests';

		Registry::getInstance('setting')->httpapi_enabled = 1;
	}

	private function disableAoChatFloodLimiting() {
		$chatBot = Registry::getInstance('chatBot');
		$chatBot->chatqueue->increment = 0;
	}
}
