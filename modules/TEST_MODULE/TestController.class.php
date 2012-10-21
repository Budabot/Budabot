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
 *	@DefineCommand(
 *		command     = 'testtowerattack', 
 *		accessLevel = 'admin', 
 *		description = "Test the bot commands", 
 *		help        = 'test.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'testtowervictory', 
 *		accessLevel = 'admin', 
 *		description = "Test the bot commands", 
 *		help        = 'test.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'showcmdregex', 
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
	public $eventManager;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $text;

	/** @Inject */
	public $commandManager;

	/** @Inject */
	public $subcommandManager;
	
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
		$packet->args[0] = $this->chatBot->get_gid('org msg');
		$packet->args[1] = (int)0xFFFFFFFF;
		$packet->args[2] = "$sender invited $args[1] to your organization.";

		$this->chatBot->process_packet($packet);
	}
	
	/**
	 * @HandlesCommand("testtowerattack")
	 * @Matches("/^testtowerattack (clan|neutral|omni) (.+) (.+) (clan|neutral|omni) (.+) (.+) (\d+) (\d+)$/i")
	 */
	public function testtowerattackCommand($message, $channel, $sender, $sendto, $args) {
		$eventObj = new stdClass;
		$eventObj->sender = -1;
		$eventObj->channel = "All Towers";
		$eventObj->message = "The $args[1] organization $args[2] just entered a state of war! $args[3] attacked the $args[4] organization $args[5]'s tower in $args[6] at location ($args[7],$args[8]).";
		$eventObj->type = 'towers';
		$this->eventManager->fireEvent($eventObj);
	}
	
	/**
	 * @HandlesCommand("testtowervictory")
	 * @Matches("/^testtowervictory (Clan|Neutral|Omni) (.+) (Clan|Neutral|Omni) (.+) (.+)$/i")
	 */
	public function testtowervictoryCommand($message, $channel, $sender, $sendto, $args) {
		$packet = new stdClass;
		$packet->type = AOCP_GROUP_MESSAGE;
		$packet->args = array();
		$packet->args[0] = $this->chatBot->get_gid('tower battle outcome');
		$packet->args[1] = (int)0xFFFFFFFF;
		$packet->args[2] = "The $args[1] organization $args[2] attacked the $args[3] $args[4] at their base in $args[5]. The attackers won!!";

		$this->chatBot->process_packet($packet);
	}
	
	/**
	 * @HandlesCommand("showcmdregex")
	 * @Matches("/^showcmdregex (.+)$/i")
	 */
	public function showcmdregexCommand($message, $channel, $sender, $sendto, $args) {
		$cmd = $args[1];
		$handlers = array();
		if (isset($this->commandManager->commands[$channel][$cmd])) {
			$commandHandler = $this->commandManager->commands[$channel][$cmd];
			$handlers = array_merge($handlers, explode(',', $commandHandler->file));
		}
		if (isset($this->subcommandManager->subcommands[$cmd])) {
			forEach ($this->subcommandManager->subcommands[$cmd] as $commandHandler) {
				if ($commandHandler->type == $channel) {
					$handlers = array_merge($handlers, explode(',', $commandHandler->file));
				}
			}
		}
		$regexes = array();
		forEach ($handlers as $handler) {
			list($name, $method) = explode(".", $handler);
			$instance = Registry::getInstance($name);
			try {
				$reflectedMethod = new ReflectionAnnotatedMethod($instance, $method);
				$regexes = array_merge($regexes, $this->commandManager->retrieveRegexes($reflectedMethod));
			} catch (ReflectionException $e) {
				continue;
			}
		}
	
		$blob = '';
		forEach ($regexes as $regex) {
			$blob .= $regex . "\n";
		}
		$msg = $this->text->make_blob("Regexes for $cmd", $blob);
		$sendto->reply($msg);
	}
}

class MockCommandReply implements CommandReply {
	public function reply($msg) {
		echo "got reply\n";
		//echo $msg . "\n";
	}
}
