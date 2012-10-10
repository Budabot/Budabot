<?php
/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'test', 
 *		accessLevel = 'all', 
 *		description = "Test the bot commands", 
 *		help        = 'test.txt'
 *	)
 */
class TestController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $util;

	/** @Inject */
	public $commandManager;
	
	/** @Logger */
	public $logger;

	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->path = getcwd() . "/modules/" . $this->moduleName . "/tests/";
	}

	/**
	 * This command handler shows menu of each profession's LE procs.
	 *
	 * @HandlesCommand("test")
	 * @Matches("/^test$/i")
	 */
	public function testCommand($message, $channel, $sender, $sendto, $args) {
		$type = "msg";
		$mockSendto = new MockCommandReply();
	
		$files = $this->util->getFilesInDirectory($this->path);
		forEach ($files as $file) {
			$lines = file($this->path . $file, FILE_IGNORE_NEW_LINES);
			forEach ($lines as $line) {
				if ($line[0] == "!") {
					$this->chatBot->sendTell($line, $sender);
					$line = substr($line, 1);
					$this->commandManager->process($type, $line, $sender, $sendto);
				}
			}
		}
	}
}

class MockCommandReply implements CommandReply {
	public function reply($msg) {
		echo "got reply\n";
		//echo $msg . "\n";
	}
}
