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
 *		command     = 'testos', 
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
 *	@DefineCommand(
 *		command     = 'reloadinstance', 
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
	public $settingManager;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $accessManager;

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
		
		$this->settingManager->add($this->moduleName, "show_test_commands", "Show test commands as they are executed", "edit", "options", "0", "true;false", "1;0");
		$this->settingManager->add($this->moduleName, "show_test_results", "Show test results from test commands", "edit", "options", "0", "true;false", "1;0");
	}

	/**
	 * @HandlesCommand("test")
	 * @Matches("/^test$/i")
	 */
	public function testAllCommand($message, $channel, $sender, $sendto, $args) {
		$type = "msg";
		if ($this->settingManager->get('show_test_results') == 1) {
			$mockSendto = $sendto;
		} else {
			$mockSendto = new MockCommandReply();
		}
	
		$files = $this->util->getFilesInDirectory($this->path);
		forEach ($files as $file) {
			$lines = file($this->path . $file, FILE_IGNORE_NEW_LINES);
			$this->runTests($lines, $sender, $type, $mockSendto);
		}
	}
	
	/**
	 * @HandlesCommand("test")
	 * @Matches("/^test ([a-z0-9_-]+)$/i")
	 */
	public function testModuleCommand($message, $channel, $sender, $sendto, $args) {
		$file = $args[1] . ".txt";
		echo $file . "\n";
		
		$type = "msg";
		if ($this->settingManager->get('show_test_results') == 1) {
			$mockSendto = $sendto;
		} else {
			$mockSendto = new MockCommandReply();
		}
	
		$lines = file($this->path . $file, FILE_IGNORE_NEW_LINES);
		if ($lines === false) {
			$sendto->reply("Could not find test <highlight>$file<end> to run.");
		} else {
			$this->runTests($lines, $sender, $type, $mockSendto);
		}
	}
	
	public function runTests($commands, $sender, $type, $sendto) {
		forEach ($commands as $line) {
			if ($line[0] == "!") {
				if ($this->settingManager->get('show_test_commands') == 1) {
					$this->chatBot->sendTell($line, $sender);
				}
				$line = substr($line, 1);
				$this->commandManager->process($type, $line, $sender, $sendto);
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
	 * @HandlesCommand("testos")
	 * @Matches("/^testos (.+)$/i")
	 */
	public function testosCommand($message, $channel, $sender, $sendto, $args) {
		$launcher = ucfirst(strtolower($args[1]));
	
		$packet = new stdClass;
		$packet->type = AOCP_GROUP_MESSAGE;
		$packet->args = array();
		$packet->args[0] = $this->chatBot->get_gid('org msg');
		$packet->args[1] = (int)0xFFFFFFFF;
		$packet->args[2] = "Blammo! $launcher has launched an orbital attack!";

		$this->chatBot->process_packet($packet);
	}
	
	/**
	 * @HandlesCommand("showcmdregex")
	 * @Matches("/^showcmdregex (.+)$/i")
	 */
	public function showcmdregexCommand($message, $channel, $sender, $sendto, $args) {
		$cmd = $args[1];
		
		// get all command handlers
		$handlers = $this->getAllCommandHandlers($cmd, $channel);
		
		// filter command handlers by access level
		$accessManager = $this->accessManager;
		$handlers = array_filter($handlers, function ($handler) use ($sender, $accessManager) {
			return $accessManager->checkAccess($sender, $handler->admin);
		});
		
		// get calls for handlers
		$calls = array_reduce($handlers, function ($handlers, $handler) {
			return array_merge($handlers, explode(',', $handler->file));
		}, array());

		// get regexes for calls
		$regexes = array();
		forEach ($calls as $call) {
			list($name, $method) = explode(".", $call);
			$instance = Registry::getInstance($name);
			try {
				$reflectedMethod = new ReflectionAnnotatedMethod($instance, $method);
				$regexes = array_merge($regexes, $this->commandManager->retrieveRegexes($reflectedMethod));
			} catch (ReflectionException $e) {
				continue;
			}
		}

		if (count($regexes) > 0) {
			$blob = '';
			forEach ($regexes as $regex) {
				$blob .= $regex . "\n";
			}
			$msg = $this->text->make_blob("Regexes for $cmd", $blob);
		} else {
			$msg = "No regexes found for command <highlight>$cmd<end>.";
		}
		$sendto->reply($msg);
	}
	
	public function getAllCommandHandlers($cmd, $channel) {
		$handlers = array();
		if (isset($this->commandManager->commands[$channel][$cmd])) {
			$handlers []= $this->commandManager->commands[$channel][$cmd];
		}
		if (isset($this->subcommandManager->subcommands[$cmd])) {
			forEach ($this->subcommandManager->subcommands[$cmd] as $handler) {
				if ($handler->type == $channel) {
					$handlers []= $handler;
				}
			}
		}
		return $handlers;
	}
	
	/**
	 * @HandlesCommand("reloadinstance")
	 * @Matches("/^reloadinstance (.+)$/i")
	 */
	public function reloadinstanceCommand($message, $channel, $sender, $sendto, $args) {
		$instanceName = $args[1];
		
		$instance = Registry::getInstance($instanceName);
		if ($instance === null) {
			$msg = "Could not find instance <highlight>$instanceName<end>.";
		} else {
			Registry::importChanges($instance);
			Registry::injectDependencies($instance);
			$msg = "Instance <highlight>$instanceName<end> has been reloaded.";
		}
		$sendto->reply($msg);
	}
}

class MockCommandReply implements CommandReply {
	public function reply($msg) {
		echo "got reply\n";
		//echo $msg . "\n";
	}
}

