<?php

/**
 * Authors:
 *  - Tyrence (RK2)
 *
 * @Instance
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
	public $helpManager;

	/** @Inject */
	public $text;

	/**
	 * @Setup
	 * This handler is called on bot startup.
	 */
	public function setup() {
		$className = get_class($this);
		$this->commandManager->activate("msg", "$className.aboutCommand", "about", "all");
		$this->commandManager->activate("priv", "$className.aboutCommand", "about", "all");
		$this->commandManager->activate("guild", "$className.aboutCommand", "about", "all");
		
		$this->commandManager->activate("msg", "$className.helpShowCommand,$className.helpListCommand", "help", "all");
		$this->commandManager->activate("priv", "$className.helpShowCommand,$className.helpListCommand", "help", "all");
		$this->commandManager->activate("guild", "$className.helpShowCommand,$className.helpListCommand", "help", "all");

		$this->helpManager->register($this->moduleName, "about", "about.txt", "all", "Basic info about Budabot");
		$this->helpManager->register($this->moduleName, "help", "help.txt", "all", "How to use help");
	}

	/**
	 * @Matches("/^about$/i")
	 */
	public function aboutCommand($message, $channel, $sender, $sendto) {
		$msg = $this->getAbout();
		$sendto->reply($msg);
	}
	
	public function getAbout() {
		global $version;
		$data = file_get_contents("./core/HELP/about.txt");
		$data = str_replace('<version>', $version, $data);
		return $this->text->make_blob("About Budabot $version", $data);
	}
	
	/**
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
				$helpLink = $this->text->make_chatcmd("Click here", "/tell <myname> help $row->name");
				$blob .= "  {$row->name}: {$row->description} $helpLink\n";
			}

			$msg = $this->text->make_blob("Help (main)", $blob);
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @Matches("/^help (.+)$/i")
	 */
	public function helpShowCommand($message, $channel, $sender, $sendto, $args) {
		$helpcmd = ucfirst($args[1]);
		$blob = $this->helpManager->find($helpcmd, $sender);
		if ($blob !== false) {
			$msg = $this->text->make_blob("Help ($helpcmd)", $blob);
			$sendto->reply($msg);
		} else {
			$sendto->reply("No help found on this topic.");
		}
	}
}
