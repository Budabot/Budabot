<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * Guides compiled by Plugsz (RK1)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'guides', 
 *		accessLevel = 'all', 
 *		description = 'Guides for AO', 
 *		help        = 'guides.txt'
 *	)
 */
class GuidesController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $commandAlias;
	
	private $path;
	private $fileExt = ".txt";
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->commandAlias->register($this->moduleName, "guides breed", "breed");
		$this->commandAlias->register($this->moduleName, "guides healdelta", "healdelta");
		$this->commandAlias->register($this->moduleName, "guides lag", "lag");
		$this->commandAlias->register($this->moduleName, "guides nanodelta", "nanodelta");
		$this->commandAlias->register($this->moduleName, "guides stats", "stats");
		$this->commandAlias->register($this->moduleName, "guides buffs", "buffs");
		$this->commandAlias->register($this->moduleName, "aou 11", "title");
		
		$this->path = getcwd() . "/modules/" . $this->moduleName . "/guides/";
	}
	
	/**
	 * @HandlesCommand("guides")
	 * @Matches("/^guides$/i")
	 */
	public function guidesListCommand($message, $channel, $sender, $sendto, $args) {
		if ($handle = opendir($this->path)) {
			$topicList = array();

			/* This is the correct way to loop over the directory. */
			while (false !== ($fileName = readdir($handle))) {
				// if file has the correct extension, it's a topic file
				if ($this->util->endsWith($fileName, $this->fileExt)) {
					$topicList[] =  str_replace($this->fileExt, '', $fileName);
				}
			}

			closedir($handle);

			sort($topicList);

			$linkContents = '';
			forEach ($topicList as $topic) {
				$linkContents .= $this->text->make_chatcmd($topic, "/tell <myname> guides $topic") . "\n";
			}

			if ($linkContents) {
				$msg = $this->text->make_blob('Topics (' . count($topicList) . ')', $linkContents);
			} else {
				$msg = "No topics available.";
			}
		} else {
			$msg = "Error reading topics.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("guides")
	 * @Matches("/^guides ([a-z0-9_-]+)$/i")
	 */
	public function guidesShowCommand($message, $channel, $sender, $sendto, $args) {
		// get the filename and read in the file
		$fileName = strtolower($args[1]);
		$info = $this->getTopicContents($this->path, $fileName, $this->fileExt);

		if (!$info) {
			$msg = "No guide named <highlight>$fileName<end> was found.";
		} else {
			$msg = $this->text->make_legacy_blob(ucfirst($fileName), $info);
		}
		$sendto->reply($msg);
	}

	private function getTopicContents($path, $fileName, $fileExt) {
		// get the filename and read in the file
		$file = "$path$fileName$fileExt";
		return file_get_contents($file);
	}
}
