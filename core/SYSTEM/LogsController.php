<?php

namespace Budabot\Core\Modules;

use Exception;
use ReverseFileReader;

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command       = 'logs',
 *		accessLevel   = 'admin',
 *		description   = 'View bot logs',
 *		help          = 'logs.txt'
 *	)
 */
class LogsController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $commandManager;

	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $text;

	/** @Inject */
	public $util;

	/** @Logger */
	public $logger;

	/**
	 * @Setup
	 * This handler is called on bot startup.
	 */
	public function setup() {
		
	}

	/**
	 * @HandlesCommand("logs")
	 * @Matches("/^logs$/i")
	 */
	public function logsCommand($message, $channel, $sender, $sendto, $args) {
		$files = $this->util->getFilesInDirectory($this->logger->get_logging_directory());
		sort($files);
		$blob = '';
		forEach ($files as $file) {
			$file_link = $this->text->make_chatcmd($file, "/tell <myname> logs $file");
			$errorLink = $this->text->make_chatcmd("ERROR", "/tell <myname> logs $file ERROR");
			$chatLink = $this->text->make_chatcmd("CHAT", "/tell <myname> logs $file CHAT");
			$blob .= "$file_link [$errorLink] [$chatLink] \n";
		}

		$msg = $this->text->make_blob('Log Files', $blob);
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("logs")
	 * @Matches("/^logs ([a-zA-Z0-9-_\.]+)$/i")
	 * @Matches("/^logs ([a-zA-Z0-9-_\.]+) (.+)$/i")
	 */
	public function logsFileCommand($message, $channel, $sender, $sendto, $args) {
		$filename = $this->logger->get_logging_directory() . "/" . $args[1];
		$readsize = $this->settingManager->get('max_blob_size') - 500;

		try {
			$file = new ReverseFileReader($filename);
			$contents = '';
			while (!$file->sof()) {
				$line = $file->getLine();

				// if user entered search criteria, filter by that
				if (isset($args[2]) && !preg_match("/{$args[2]}/i", $line)) {
					continue;
				}

				if (strlen($contents . $line) > $readsize) {
					break;
				}
				$contents .= $line;
			}
			$file->close();
			if (empty($contents)) {
				$msg = "File is empty or nothing matched your search criteria.";
			} else {
				if (isset($args[2])) {
					$contents = "Search: $args[2]\n\n" . $contents;
				}
				$msg = $this->text->make_blob($args[1], $contents);
			}
		} catch (Exception $e) {
			$msg = "Error: " . $e->getMessage();
		}
		$sendto->reply($msg);
	}
}
