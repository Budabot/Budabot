<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'profile', 
 *		accessLevel = 'admin', 
 *		description = 'Guides for AO', 
 *		help        = 'profile.txt',
 *		alias       = 'profiles'
 *	)
 */
class ProfileController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $commandManager;
	
	private $path;
	private $fileExt = ".txt";
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->path = getcwd() . "/core/" . $this->moduleName . "/profiles/";
	}
	
	/**
	 * @HandlesCommand("profile")
	 * @Matches("/^profile$/i")
	 */
	public function profileListCommand($message, $channel, $sender, $sendto, $args) {
		if ($handle = opendir($this->path)) {
			$profileList = array();

			/* This is the correct way to loop over the directory. */
			while (false !== ($fileName = readdir($handle))) {
				// if file has the correct extension, it's a profile file
				if (stripos($fileName, $this->fileExt) !== false) {
					$profileList[] =  str_replace($this->fileExt, '', $fileName);
				}
			}

			closedir($handle);

			sort($profileList);

			$linkContents = '';
			forEach ($profileList as $profile) {
				$name = ucfirst(strtolower($profile));
				$linkContents .= $this->text->make_chatcmd($name, "/tell <myname> profile $profile") . "\n";
			}

			if ($linkContents) {
				$linkContents .= "\n\n<orange>Warning: Running a profile script will change your configuration.  Proceed only if you understand the consequences.<end>";
				$msg = $this->text->make_blob('Profiles (' . count($profileList) . ')', $linkContents);
			} else {
				$msg = "No profiles available.";
			}
		} else {
			$msg = "Error profiles topics.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("profile")
	 * @Matches("/^profile ([a-z0-9_-]+)$/i")
	 */
	public function profileShowCommand($message, $channel, $sender, $sendto, $args) {
		// get the filename and read in the file
		$fileName = strtolower($args[1]);
		$name = ucfirst($fileName);
		$info = file_get_contents($this->path . $fileName . $this->fileExt);
		$lines = explode("\n", $info);
		$output = '';
		forEach ($lines as $line) {
			if ($line[0] == "!") {
				$output .= "<pagebreak>" . $line . "\n";
				$line = substr(trim($line), 1);
				$profileSendTo = new ProfileCommandReply();
				$this->commandManager->process("msg", $line, $sender, $profileSendTo);
				$output .= $profileSendTo->result . "\n\n";
			}
		}
		
		$msg = $this->text->make_blob("$name Profile Results", $output);
		$sendto->reply($msg);
	}
}

class ProfileCommandReply implements CommandReply {
	public $result;

	public function reply($msg) {
		$this->result = $msg;
	}
}
