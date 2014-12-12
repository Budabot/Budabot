<?php

namespace Budabot\User\Modules;

use Budabot\Core\AutoInject;
use Budabot\Core\CommandReply;
use Budabot\Core\Registry;
use stdClass;

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
 *		command     = 'testevent',
 *		accessLevel = 'admin',
 *		description = "Test the bot commands",
 *		help        = 'testevent.txt'
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
 *		command     = 'msgsize',
 *		accessLevel = 'all',
 *		description = "Show the number of characters in a command response",
 *		help        = 'msgsize.txt'
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
		/*
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
		*/
		
		$response = $this->http->get('http://budabot.jkbff.com/test/delay.php')->waitAndReturnResponse();
		$sendto->reply($response->body);
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
		$time = $this->util->unixtimeToReadable(time() - $starttime);
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
			$time = $this->util->unixtimeToReadable(time() - $starttime);
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
	 * @HandlesCommand("testevent")
	 * @Matches("/^testevent (.+)$/i")
	 */
	public function testeventCommand($message, $channel, $sender, $sendto, $args) {
		$event = $args[1];
		
		list($instanceName, $methodName) = explode(".", $event);
		$instance = Registry::getInstance($instanceName);
		if ($instance == null) {
			$sendto->reply("Instance <highlight>$instanceName<end> does not exist.");
		} else if (!method_exists($instance, $methodName)) {
			$sendto->reply("Method <highlight>$methodName<end> does not exist on instance <highlight>$instanceName<end>.");
		} else {
			$this->eventManager->callEventHandler(null, $event);
			$sendto->reply("Event has been fired.");
		}
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
	 * @HandlesCommand("msgsize")
	 * @Matches("/^msgsize (.+)$/i")
	 */
	public function msgsizeCommand($message, $channel, $sender, $sendto, $args) {
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