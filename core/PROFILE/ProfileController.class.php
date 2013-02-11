<?php

namespace budabot\core\modules;

use \budabot\core\CommandReply;
use \Exception;

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
	public $db;
	
	/** @Inject */
	public $settingManager;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $commandManager;
	
	private $path;
	private $fileExt = ".txt";
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->path = getcwd() . "/data/profiles/";
		
		// make sure that the profile folder exists
		if (!dir($this->path)) {
			mkdir($this->path, 0777);
		}
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
				if ($this->util->endsWith($fileName, $this->fileExt)) {
					$profileList[] =  str_replace($this->fileExt, '', $fileName);
				}
			}

			closedir($handle);

			sort($profileList);

			$linkContents = '';
			forEach ($profileList as $profile) {
				$name = ucfirst(strtolower($profile));
				$viewLink = $this->text->make_chatcmd("View", "/tell <myname> profile view $profile");
				$loadLink = $this->text->make_chatcmd("Load", "/tell <myname> profile load $profile");
				$linkContents .= "$profile [$viewLink] [$loadLink]\n";
			}

			if ($linkContents) {
				$linkContents .= "\n\n<orange>Warning: Running a profile script will change your configuration.  Proceed only if you understand the consequences.<end>";
				$msg = $this->text->make_blob('Profiles (' . count($profileList) . ')', $linkContents);
			} else {
				$msg = "No profiles available.";
			}
		} else {
			$msg = "Could not open profiles directory.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("profile")
	 * @Matches("/^profile view ([a-z0-9_-]+)$/i")
	 */
	public function profileViewCommand($message, $channel, $sender, $sendto, $args) {
		$profileName = $args[1];
		$filename = $this->path . '/' . $profileName . $this->fileExt;
		if (!file_exists($filename)) {
			$msg = "Profile <highlight>$profileName<end> does not exist.";
		} else {
			$blob = htmlspecialchars(file_get_contents($filename));
			$msg = $this->text->make_blob("Profile $profileName", $blob);
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("profile")
	 * @Matches("/^profile save ([a-z0-9_-]+)$/i")
	 */
	public function profileSaveCommand($message, $channel, $sender, $sendto, $args) {
		$profileName = $args[1];
		$filename = $this->path . '/' . $profileName . $this->fileExt;
		if (file_exists($filename)) {
			$msg = "Profile <highlight>$profileName<end> already exists.";
		} else {
			$contents = "# Settings\n";
			forEach ($this->settingManager->settings as $name => $value) {
				if ($name != "botid" && $name != "version" && !$this->util->endsWith($name, "_db_version")) {
					$contents .= "!settings save $name $value\n";
				}
			}
			$contents .= "\n# Events\n";
			$data = $this->db->query("SELECT * FROM eventcfg_<myname>");
			forEach ($data as $row) {
				$status = "disable";
				if ($row->status == 1) {
					$status = "enable";
				}
				$contents .= "!config event {$row->type} {$row->file} {$status} all\n";
			}
			$contents .= "\n# Commands\n";
			$data = $this->db->query("SELECT * FROM cmdcfg_<myname>");
			forEach ($data as $row) {
				$status = "disable";
				if ($row->status == 1) {
					$status = "enable";
				}
				$contents .= "!config cmd {$row->cmd} {$status} {$row->type}\n";
			}
			file_put_contents($filename, $contents);
			$msg = "Profile <highlight>$profileName<end> has been saved.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("profile")
	 * @Matches("/^profile (rem|remove|del|delete) ([a-z0-9_-]+)$/i")
	 */
	public function profileRemCommand($message, $channel, $sender, $sendto, $args) {
		$profileName = $args[2];
		$filename = $this->path . '/' . $profileName . $this->fileExt;
		if (!file_exists($filename)) {
			$msg = "Profile <highlight>$profileName<end> does not exist.";
		} else {
			unlink($filename);
			$msg = "Profile <highlight>$profileName<end> has been deleted.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("profile")
	 * @Matches("/^profile load ([a-z0-9_-]+)$/i")
	 */
	public function profileLoadCommand($message, $channel, $sender, $sendto, $args) {
		$profileName = $args[1];
		$filename = $this->getFilename($profileName);
		
		if (!file_exists($filename)) {
			$msg = "Profile <highlight>$profileName<end> does not exist.";
		} else {
			$sendto->reply("Loading profile <highlight>$profileName<end>...");
			$output = $this->loadProfile($filename, $sender);
			if ($ouptput === false) {
				$msg = "There was an error loading the profile <highlight>$profileName<end>.";
			} else {
				$msg = $this->text->make_blob("Profile Results: $profileName", $output);
			}
		}
		$sendto->reply($msg);
	}
	
	public function getFilename($profileName) {
		return $this->path . '/' . $profileName . $this->fileExt;
	}
	
	public function loadProfile($filename, $sender) {
		$info = file_get_contents($filename);
		$lines = explode("\n", $info);
		$this->db->begin_transaction();
		try {
			$profileSendTo = new ProfileCommandReply();
			forEach ($lines as $line) {
				if ($line[0] == "!") {
					$profileSendTo->reply("<pagebreak>" . $line);
					$line = substr(trim($line), 1);
					$this->commandManager->process("msg", $line, $sender, $profileSendTo);
					$profileSendTo->reply("\n");
				}
			}
			$this->db->commit();
			return $profileSendTo->result;
		} catch (Exception $e) {
			$this->logger->log("ERROR", "Could not load profile", $e);
			$this->db->rollback();
			return false;
		}
	}
}

class ProfileCommandReply implements CommandReply {
	public $result;

	public function reply($msg) {
		$this->result .= $msg . "\n";
	}
}
