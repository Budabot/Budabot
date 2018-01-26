<?php

namespace Budabot\User\Modules;

use Budabot\Core\AutoInject;
use ReflectionAnnotatedMethod;
use Budabot\Core\Registry;

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'showcmdregex',
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
 *		command     = 'cmdhandlers',
 *		accessLevel = 'admin',
 *		description = "Show command handlers for a command",
 *		help        = 'cmdhandlers.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'createblob',
 *		accessLevel = 'admin',
 *		description = "Creates a blob of random characters",
 *		help        = 'createblob.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'makeitem',
 *		accessLevel = 'admin',
 *		description = "Creates an item link",
 *		help        = 'makeitem.txt'
 *	)
 */
class DevController extends AutoInject {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/**
	 * @Setup
	 */
	public function setup() {
		$this->commandAlias->register($this->moduleName, "querysql select", "select");
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

		$count = count($regexes);
		if ($count > 0) {
			$blob = '';
			forEach ($regexes as $regex) {
				$blob .= $regex . "\n";
			}
			$msg = $this->text->makeBlob("Regexes for $cmd ($count)", $blob);
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
	 * @HandlesCommand("intransaction")
	 * @Matches("/^intransaction$/i")
	 */
	public function intransactionCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->db->inTransaction()) {
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
		$stacktrace = $this->util->getStackTrace();
		$count = substr_count($stacktrace, "\n");
		$msg = $this->text->makeBlob("Current Stacktrace ($count)", $stacktrace);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("cmdhandlers")
	 * @Matches("/^cmdhandlers (.*)$/i")
	 */
	public function cmdhandlersCommand($message, $channel, $sender, $sendto, $args) {
		$cmdArray = explode(" ", $args[1], 2);
		$cmd = $cmdArray[0];
		
		$blob = '';

		// command
		forEach ($this->commandManager->commands as $channelName => $channel) {
			if (isset($channel[$cmd])) {
				$blob .= "<header2>$channelName ($cmd)<end>\n";
				$blob .= $channel[$cmd]->file . "\n\n";
			}
		}

		// subcommand
		forEach ($this->subcommandManager->subcommands[$cmd] as $row) {
			$blob .= "<header2>$row->type ($row->cmd)<end>\n";
			$blob .= $row->file . "\n\n";
		}

		$msg = $this->text->makeBlob("Command Handlers for '$cmd'", $blob);
		
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("makeitem")
	 * @Matches("/^makeitem (\d+) (\d+) (\d+) (.+)$/i")
	 */
	public function makeitemCommand($message, $channel, $sender, $sendto, $args) {
		$lowId = $args[1];
		$highId = $args[2];
		$ql = $args[3];
		$name = $args[4];
		$sendto->reply($this->text->makeItem($lowId, $highId, $ql, $name));
	}
	
	/**
	 * @HandlesCommand("createblob")
	 * @Matches("/^createblob (\d+)$/i")
	 * @Matches("/^createblob (\d+) (\d+)$/i")
	 */
	public function createblobCommand($message, $channel, $sender, $sendto, $args) {
		$length = $args[1];
		if (isset($args[2])) {
			$numBlobs = $args[2];
		} else {
			$numBlobs = 1;
		}
		
		for ($i = 0; $i < $numBlobs; $i++) {
			$blob = $this->randString($length);
			$msg = $this->text->makeBlob("Blob $i", $blob);
			$sendto->reply($msg);
		}
	}
	
	public function randString($length, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789 \n') {
		$str = '';
		$count = strlen($charset);
		while ($length--) {
			$str .= $charset[mt_rand(0, $count-1)];
		}
		return $str;
	}
}
