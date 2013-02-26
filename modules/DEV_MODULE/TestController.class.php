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
 *		command     = 'testevent',
 *		accessLevel = 'admin',
 *		description = "Test the bot commands",
 *		help        = 'test.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'intransaction',
 *		accessLevel = 'admin',
 *		description = "Test the bot commands",
 *		help        = 'test.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'rollbacktransaction',
 *		accessLevel = 'admin',
 *		description = "Test the bot commands",
 *		help        = 'test.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'stacktrace',
 *		accessLevel = 'admin',
 *		description = "Test the bot commands",
 *		help        = 'test.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'testcloaklower',
 *		accessLevel = 'admin',
 *		description = "Test the bot commands",
 *		help        = 'test.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'testcloakraise',
 *		accessLevel = 'admin',
 *		description = "Test the bot commands",
 *		help        = 'test.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'testblobsize',
 *		accessLevel = 'admin',
 *		description = "Test the bot commands",
 *		help        = 'test.txt'
 *	)
 */
class TestController extends AutoInject {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

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
	public function testListCommand($message, $channel, $sender, $sendto, $args) {
		$files = $this->util->getFilesInDirectory($this->path);
		$count = count($files);
		sort($files);
		$blob = $this->text->make_chatcmd("All Tests", "/tell <myname> test all") . "\n";
		forEach ($files as $file) {
			$name = str_replace(".txt", "", $file);
			$blob .= $this->text->make_chatcmd($name, "/tell <myname> test $name") . "\n";
		}
		$msg = $this->text->make_blob("Tests Available ($count)", $blob);
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("test")
	 * @Matches("/^test all$/i")
	 */
	public function testAllCommand($message, $channel, $sender, $sendto, $args) {
		$type = "msg";
		if ($this->setting->show_test_results == 1) {
			$mockSendto = $sendto;
		} else {
			$mockSendto = new MockCommandReply();
		}
	
		$files = $this->util->getFilesInDirectory($this->path);
		$starttime = time();
		$sendto->reply("Starting tests...");
		forEach ($files as $file) {
			$lines = file($this->path . $file, FILE_IGNORE_NEW_LINES);
			$this->runTests($lines, $sender, $type, $mockSendto);
		}
		$time = $this->util->unixtime_to_readable(time() - $starttime);
		$sendto->reply("Finished tests. Time: $time");
	}
	
	/**
	 * @HandlesCommand("test")
	 * @Matches("/^test ([a-z0-9_-]+)$/i")
	 */
	public function testModuleCommand($message, $channel, $sender, $sendto, $args) {
		$file = $args[1] . ".txt";
		
		$type = "msg";
		if ($this->setting->show_test_results == 1) {
			$mockSendto = $sendto;
		} else {
			$mockSendto = new MockCommandReply();
		}
	
		$lines = file($this->path . $file, FILE_IGNORE_NEW_LINES);
		if ($lines === false) {
			$sendto->reply("Could not find test <highlight>$file<end> to run.");
		} else {
			$starttime = time();
			$sendto->reply("Starting test $file...");
			$this->runTests($lines, $sender, $type, $mockSendto);
			$time = $this->util->unixtime_to_readable(time() - $starttime);
			$sendto->reply("Finished test $file. Time: $time");
		}
	}
	
	public function runTests($commands, $sender, $type, $sendto) {
		forEach ($commands as $line) {
			if ($line[0] == "!") {
				if ($this->setting->show_test_commands == 1) {
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
	 * @HandlesCommand("testevent")
	 * @Matches("/^testevent (.+)$/i")
	 */
	public function testeventCommand($message, $channel, $sender, $sendto, $args) {
		$event = $args[1];
		
		$this->eventManager->callEventHandler(null, $event);
	}
	
	/**
	 * @HandlesCommand("intransaction")
	 * @Matches("/^intransaction$/i")
	 */
	public function intransactionCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->db->in_transaction()) {
			$msg = "There is an active transaction.";
		} else {
			$msg = "There is no active transaction.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("rollbacktransaction")
	 * @Matches("/^rollbacktransaction$/i")
	 */
	public function rollbacktransactionCommand($message, $channel, $sender, $sendto, $args) {
		$this->db->rollback();
		
		$msg = "The active transaction has been rolled back.";
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("stacktrace")
	 * @Matches("/^stacktrace$/i")
	 */
	public function stacktraceCommand($message, $channel, $sender, $sendto, $args) {
		$msg = $this->text->make_blob("Current Stacktrace", $this->util->getStackTrace());
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("testcloaklower")
	 * @Matches("/^testcloaklower$/i")
	 */
	public function testcloaklowerCommand($message, $channel, $sender, $sendto, $args) {
		$packet = new stdClass;
		$packet->type = AOCP_GROUP_MESSAGE;
		$packet->args = array();
		$packet->args[0] = $this->chatBot->get_gid($this->chatBot->vars['my_guild']);
		$packet->args[1] = (int)0xFFFFFFFF;
		$packet->args[2] = "$sender turned the cloaking device in your city off.";

		$this->chatBot->process_packet($packet);
	}
	
	/**
	 * @HandlesCommand("testcloakraise")
	 * @Matches("/^testcloakraise$/i")
	 */
	public function testcloakraiseCommand($message, $channel, $sender, $sendto, $args) {
		$packet = new stdClass;
		$packet->type = AOCP_GROUP_MESSAGE;
		$packet->args = array();
		$packet->args[0] = $this->chatBot->get_gid($this->chatBot->vars['my_guild']);
		$packet->args[1] = (int)0xFFFFFFFF;
		$packet->args[2] = "$sender turned the cloaking device in your city on.";

		$this->chatBot->process_packet($packet);
	}
	
	/**
	 * @HandlesCommand("testblobsize")
	 * @Matches("/^testblobsize (.+)$/i")
	 */
	public function testblobsizeCommand($message, $channel, $sender, $sendto, $args) {
		$cmd = $args[1];

		$mockSendto = new ReplySizeCommandReply($sendto);
		$this->commandManager->process($channel, $cmd, $sender, $mockSendto);
	}
}

class MockCommandReply implements CommandReply {
	public function reply($msg) {
		//echo "got reply\n";
		//echo $msg . "\n";
	}
}

class ReplySizeCommandReply implements CommandReply {
	private $sendto;

	public function __construct($sendto) {
		$this->sendto = $sendto;
	}

	public function reply($msg) {
		if (!is_array($msg)) {
			$msg = array($msg);
		}
		
		forEach ($msg as $page) {
			$this->sendto->reply($page);
			$this->sendto->reply(strlen($page));
		}
	}
}