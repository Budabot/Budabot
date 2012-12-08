<?php

require __DIR__ . '/../vendor/autoload.php';
require_once 'AOChatServer.php';
require_once 'JSONRPCServer.php';

const MESSAGE_TIMEOUT = 30;

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
			$that->tellMessages []= $msg;
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
	public function waitPrivateMessage($value) {
		$that = $this;
		$result = $this->blockUntil(MESSAGE_TIMEOUT, function() use ($that, $value) {
			forEach($that->privateMessages as $message) {
				if (stripos($message, $value) !== false) {
					return true;
				}
			}
		});
		if (!$result) {
			throw new Exception("Failed to receive message from bot: $value");
		}
	}

	/**
	 * Sets a character as logged in. 
	 * Called by the test runner via JSON-RPC call.
	 */
	public function buddyLogin($name) {
		$this->aoServer->addBuddy($name, true);
	}

	/**
	 * Clears any tell messages send by the bot.
	 * Called by the test runner via JSON-RPC call.
	 */
	public function clearTellMessages() {
		$this->tellMessages = array();
	}

	/**
	 * Sends a tell $message to bot as $asName character.
	 * Called by the test runner via JSON-RPC call.
	 */
	public function sendTellMessageToBot($asName, $message) {
		$this->aoServer->sendTellMessage($asName, $message);
	}

	/**
	 * Waits until a tell message with given array of phrases have been received.
	 * Throws an exception if $timeout occurs.
	 * Called by the test runner via JSON-RPC call.
	 */
	public function waitForTellMessageWithPhrases($phrases) {
		$that = $this;
		$result = $this->blockUntil(MESSAGE_TIMEOUT, function() use ($that, &$phrases) {
			forEach($phrases as $phrase) {
				forEach($that->tellMessages as $message) {
					if (stripos($message, $phrase) !== false) {
						$i = array_search($phrase, $phrases);
						unset($phrases[$i]);
					}
				}
			}
			return empty($phrases);
		});
		if (!$result) {
			throw new Exception("Failed to receive tell messages with phrase(s): " . implode($phrases, ', '));
		}
	}

	public function getAccountCharacters() {
		return $this->accountChars;
	}

	private function blockUntil($timeout, $callback) {
		$endTime = time() + intval($timeout);
		while (time() < $endTime) {
			$result = call_user_func($callback);
			if ($result) {
				return true;
			}
			$this->loop->tick();
		}
		return false;
	}
}

$chatPort = $argv[1];
$rpcPort  = $argv[2];

$loop      = React\EventLoop\Factory::create();

$aoServer   = new AOChatServer($loop, $chatPort);
$controller = new ServerController($loop, $aoServer);
$rpcServer  = new JSONRPCServer($loop, $rpcPort, $controller);

$loop->run();


