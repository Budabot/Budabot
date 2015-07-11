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
			$msg = $this->text->make_blob("Regexes for $cmd ($count)", $blob);
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
		$stacktrace = $this->util->getStackTrace();
		$count = substr_count($stacktrace, "\n");
		$msg = $this->text->make_blob("Current Stacktrace ($count)", $stacktrace);
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

		$msg = $this->text->make_blob("Command Handlers for '$cmd'", $blob);
		
		$sendto->reply($msg);
	}
}
