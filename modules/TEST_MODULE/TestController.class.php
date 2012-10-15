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
 *		accessLevel = 'admin', 
 *		description = "Test the bot commands", 
 *		help        = 'test.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'testorgjoin', 
 *		accessLevel = 'admin', 
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
	 * @Setup
	 */
	public function setup() {
		$this->path = getcwd() . "/modules/" . $this->moduleName . "/tests/";
	}

	/**
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
	
	/**
	 * @HandlesCommand("testorgjoin")
	 * @Matches("/^testorgjoin (.+)$/i")
	 */
	public function testorgjoinCommand($message, $channel, $sender, $sendto, $args) {
		$packet = new stdClass;
		$packet->type = AOCP_GROUP_MESSAGE;
		$packet->args = array();
		$packet->args[0] = $this->chatBot->gid['org msg'];
		$packet->args[1] = (int)0xFFFFFFFF;
		$packet->args[2] = "$sender invited $args[1] to your organization.";

		$this->chatBot->process_packet($packet);
	}
}

class MockCommandReply implements CommandReply {
	public function reply($msg) {
		echo "got reply\n";
		//echo $msg . "\n";
	}
}
