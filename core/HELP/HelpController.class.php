<?php

namespace Budabot\Core\Modules;

/**
 * Authors:
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command       = 'help',
 *		accessLevel   = 'all',
 *		description   = 'Show help topics',
 *		help          = 'help.txt',
 *		defaultStatus = '1'
 *	)
 */
class HelpController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $commandManager;
	
	/** @Inject */
	public $commandAlias;

	/** @Inject */
	public $helpManager;

	/** @Inject */
	public $text;

	/**
	 * @Setup
	 * This handler is called on bot startup.
	 */
	public function setup() {
		$this->helpManager->register($this->moduleName, "about", "about.txt", "all", "Info about the development of Budabot");
		
		$this->commandAlias->register($this->moduleName, "help about", "about");
	}
	
	public function getAbout() {
		global $version;
		$data = file_get_contents("./core/HELP/about.txt");
		$data = str_replace('<version>', $version, $data);
		return $this->text->make_blob("About Budabot $version", $data);
	}
	
	/**
	 * @HandlesCommand("help")
	 * @Matches("/^help$/i")
	 */
	public function helpListCommand($message, $channel, $sender, $sendto) {
		global $version;

		$data = $this->helpManager->getAllHelpTopics($sender);

		if (count($data) == 0) {
			$msg = "No help files found.";
		} else {
			$blob = '';
			$current_module = '';
			forEach ($data as $row) {
				if ($current_module != $row->module) {
					$blob .= "\n<pagebreak><header2>{$row->module}:<end>\n";
					$current_module = $row->module;
				}
				$helpLink = $this->text->make_chatcmd($row->name, "/tell <myname> help $row->name");
				$blob .= "  {$helpLink}: {$row->description}\n";
			}

			$msg = $this->text->make_blob("Help (main)", $blob);
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("help")
	 * @Matches("/^help (.+)$/i")
	 */
	public function helpShowCommand($message, $channel, $sender, $sendto, $args) {
		$helpcmd = strtolower($args[1]);
		
		if ($helpcmd == 'about') {
			$msg = $this->getAbout();
			$sendto->reply($msg);
			return;
		}
	
		// check for alias
		$temp = $this->commandAlias->getCommandByAlias($helpcmd);
		if ($temp !== null) {
			$arr = explode(' ', $temp);
			$helpcmd = $arr[0];
		}

		$blob = $this->helpManager->find($helpcmd, $sender);
		if ($blob !== false) {
			$helpcmd = ucfirst($helpcmd);
			$msg = $this->text->make_blob("Help ($helpcmd)", $blob);
			$sendto->reply($msg);
		} else {
			$sendto->reply("No help found on this topic.");
		}
	}
}
