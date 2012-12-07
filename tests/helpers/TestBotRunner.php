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
		$chatBot = Registry::getInstance('chatBot');
		$chatBot->chatqueue->increment = 0;

		parent::startBot();
	}
}
