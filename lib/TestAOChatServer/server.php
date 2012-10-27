<?php

require __DIR__ . '/../vendor/autoload.php';
require_once 'AOChatServer.php';
require_once 'JSONRPCServer.php';

class ServerController implements IAOChatModel {

	public $privateMessages = array();
	public $tellMessages = array();

	private $loop = null;

	public function __construct($loop, $aoServer) {
		$this->loop = $loop;
		$that = $this;

		$this->aoServer = $aoServer;
		$this->aoServer->setModel($this);
		
		$this->aoServer->on('private_message', function($gid, $msg, $blob) use ($that) {
			$that->privateMessages []= $msg;
		});
		$this->aoServer->on('tell_message', function($uid, $msg, $blob) use ($that) {
			$info = $that->aoServer->getCharInfo($uid);
			$that->tellMessages[strtolower($info->name)] []= $msg;
		});
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
	public function sendTellMessage($asName, $message) {
		$this->aoServer->sendTellMessage($asName, $message);
	}

	/**
	 * Waits for given private message from bot.
	 * Throws an exception if the given timeout occurs (in seconds).
	 * Called by the test runner via JSON-RPC call.
	 */
	public function waitPrivateMessage($timeout, $value) {
		$endTime = time() + intval($timeout);
		while (time() < $endTime) {
			forEach($this->privateMessages as $message) {
				if (stripos($message, $value) !== false) {
					return;
				}
			}
			$this->loop->tick();
		}
		throw new Exception("Timeout while waiting for message: $message");
	}

	/**
	 * Sets a character as logged in. 
	 * Called by the test runner via JSON-RPC call.
	 */
	public function buddyLogin($name) {
		$this->aoServer->addBuddy($name, true);
	}

	/**
	 * Clears any tell messages send to given character.
	 * Called by the test runner via JSON-RPC call.
	 */
	public function clearTellMessagesOfCharacter($name) {
		$this->tellMessages[strtolower($name)] = array();
	}

	/**
	 * Sends a tell $message to bot as $asName character.
	 * Called by the test runner via JSON-RPC call.
	 */
	public function sendTellMessageToBot($asName, $message) {
		$this->aoServer->sendTellMessage($asName, $message);
	}

	/**
	 * Returns array of tell messages send to character $name.
	 * Called by the test runner via JSON-RPC call.
	 */
	public function getTellMessagesOfCharacter($name) {
		$name = strtolower($name);
		if (isset($this->tellMessages[$name])) {
			return $this->tellMessages[$name];
		}
		return array();
	}

	public function getAccountCharacters() {
		return $this->accountChars;
	}
}

$chatPort = $argv[1];
$rpcPort  = $argv[2];

$loop      = React\EventLoop\Factory::create();

$aoServer   = new AOChatServer($loop, $chatPort);
$controller = new ServerController($loop, $aoServer);
$rpcServer  = new JSONRPCServer($loop, $rpcPort, $controller);

$loop->run();


