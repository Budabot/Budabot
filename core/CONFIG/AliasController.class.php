<?php

namespace Budabot\Core\Modules;

use stdClass;

/**
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command       = 'alias',
 *		accessLevel   = 'mod',
 *		description   = 'Manage command aliases',
 *		help          = 'alias.txt',
 *		defaultStatus = '1'
 *	)
 */
class AliasController {

	/** @Inject */
	public $commandAlias;

	/** @Inject */
	public $commandManager;

	/** @Inject */
	public $text;
	
	/**
	 * @Setup
	 * This handler is called on bot startup.
	 */
	public function setup() {
		
	}

	/**
	 * This command handler add a command alias.
	 *
	 * @HandlesCommand("alias")
	 * @Matches("/^alias add ([a-z0-9]+) (.+)/si")
	 */
	public function aliasAddCommand($message, $channel, $sender, $sendto, $args) {
		$alias = strtolower($args[1]);
		$cmd = $args[2];
	
		$alias_obj = new stdClass;
		$alias_obj->module = '';
		$alias_obj->cmd = $cmd;
		$alias_obj->alias = $alias;
		$alias_obj->status = 1;
	
		$commands = $this->commandManager->get($alias);
		$enabled = false;
		forEach ($commands as $command) {
			if ($command->status == '1') {
				$enabled = true;
				break;
			}
		}
		$row = $this->commandAlias->get($alias);
		if ($enabled) {
			$msg = "Cannot add alias <highlight>{$alias}<end> since there is already an active command with that name.";
		} else if ($row === null) {
			$this->commandAlias->add($alias_obj);
			$this->commandAlias->activate($cmd, $alias);
			$msg = "Alias <highlight>{$alias}<end> for command <highlight>{$cmd}<end> added successfully.";
		} else if ($row->status == 0 || ($row->status == 1 && $row->cmd == $cmd)) {
			$this->commandAlias->update($alias_obj);
			$this->commandAlias->activate($cmd, $alias);
			$msg = "Alias <highlight>{$alias}<end> for command <highlight>{$cmd}<end> added successfully.";
		} else if ($row->status == 1 && $row->cmd != $cmd) {
			$msg = "Cannot add alias <highlight>{$alias}<end> since an alias with that name already exists.";
		}
		$sendto->reply($msg);
	}

	/**
	 * This command handler list all aliases.
	 *
	 * @HandlesCommand("alias")
	 * @Matches("/^alias list$/i")
	 */
	public function aliasListCommand($message, $channel, $sender, $sendto, $args) {
		$paddingSize = 30;
	
		$a = $this->padRow("Alias", $paddingSize);
		$blob = "<header2>{$a}Command<end>\n\n";
		$count = 0;
		forEach ($this->commandAlias->getEnabledAliases() as $alias) {
			if ($count++ % 2 == 0) {
				$color = "white";
			} else {
				$color = "highlight";
			}
			$removeLink = $this->text->make_chatcmd('Remove', "/tell <myname> alias rem {$alias->alias}");
			$a = $this->padRow($alias->alias, $paddingSize);
			$blob .= "<{$color}>{$a}{$alias->cmd}<end> $removeLink\n";
		}
	
		$msg = $this->text->makeBlob('Alias List', $blob);
		$sendto->reply($msg);
	}

	/**
	 * This command handler remove a command alias.
	 *
	 * @HandlesCommand("alias")
	 * @Matches("/^alias rem ([a-z0-9]+)/i")
	 */
	public function aliasRemCommand($message, $channel, $sender, $sendto, $args) {
		$alias = strtolower($args[1]);
	
		$row = $this->commandAlias->get($alias);
		if ($row === null || $row->status != 1) {
			$msg = "Could not find alias <highlight>{$alias}<end>!";
		} else {
			$row->status = 0;
			$this->commandAlias->update($row);
			$this->commandAlias->deactivate($alias);
	
			$msg = "Alias <highlight>{$alias}<end> removed successfully.";
		}
		$sendto->reply($msg);
	}

	private function padRow($str, $size) {
		return str_pad($str, $size - strlen($str), ".");
	}
}
