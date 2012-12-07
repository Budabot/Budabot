<?php

require_once __DIR__ . '/../../core/BotRunner.php';

class TestBotRunner extends BotRunner {

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
		$this->sendHttpRequestsToHttpApi();
		$this->disableAoChatFloodLimiting();
		parent::startBot();
	}

	private function sendHttpRequestsToHttpApi() {
		AsyncHttp::$overrideAddress = '127.0.0.1';
		AsyncHttp::$overridePort = Registry::getInstance('setting')->httpapi_port;
		HttpRequest::$overridePathPrefix = '/tests';

		Registry::getInstance('setting')->httpapi_enabled = 1;

		$this->servePorkTestdata();

	}

	private function servePorkTestdata() {
		Registry::getInstance('httpapi')->registerHandler("|^/tests/character/bio/d/./name/(.+)/bio[.]xml$|", function ($request, $response) {
			if (preg_match("|^/tests/character/bio/d/./name/(.+)/bio[.]xml$|", $request->getPath(), $matches)) {
				$charName = $matches[1];
				$response->writeHead(200);
				$response->end(file_get_contents("./tests/testdata/pork/$charName.xml"));
			}
		});
	}

	private function disableAoChatFloodLimiting() {
		$chatBot = Registry::getInstance('chatBot');
		$chatBot->chatqueue->increment = 0;
	}
}
