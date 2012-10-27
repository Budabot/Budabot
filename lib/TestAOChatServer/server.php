<?php

require __DIR__ . '/../vendor/autoload.php';
require_once 'AOChatServer.php';
require_once 'JSONRPCServer.php';

class ServerController implements IAOChatModel {

	public function __construct($aoServer) {
		$this->aoServer = $aoServer;
		$this->aoServer->setModel($this);
	}

	/**
	 * Sets list of characters that the bot can log in with.
	 * Called by the test runner via JSON-RPC call.
	 */
	public function setAccountCharacters($characters) {
		$this->accountChars = $characters;
	}

	/**
	 * Sends a tell message to the bot.
	 * Called by the test runner via JSON-RPC call.
	 */
	public function sendTellMessage($name, $message) {
		$this->aoServer->sendTellMessage($name, $message);
	}
	
	public function getAccountCharacters() {
		return $this->accountChars;
	}
}

$chatPort = $argv[1];
$rpcPort  = $argv[2];

$loop      = React\EventLoop\Factory::create();

$aoServer   = new AOChatServer($loop, $chatPort);
$controller = new ServerController($aoServer);
$rpcServer  = new JSONRPCServer($loop, $rpcPort, $controller);

$loop->run();


