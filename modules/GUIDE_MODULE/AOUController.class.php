<?php

global $version;

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'aou', 
 *		accessLevel = 'all', 
 *		description = 'Search for or view a guide from AO-Universe.com', 
 *		help        = 'aou.txt'
 *	)
 */
class AOUController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $text;
	
	private $url = "http://www.ao-universe.com/mobile/parser.php?bot=budabot";

	/**
	 * View an AO-U guide.
	 *
	 * @HandlesCommand("aou")
	 * @Matches("/^aou (\d+)$/i")
	 */
	public function aouView($message, $channel, $sender, $sendto, $args) {
		$guideid = $args[1];

		$guide = file_get_contents($this->url . "&mode=view&id=" . $guideid);

		$dom = new DOMDocument;
		$dom->loadXML($guide);

		$content = $dom->getElementsByTagName('content')->item(0);

		$title = $content->getElementsByTagName('name')->item(0)->nodeValue;

		$pattern = "/(\\[[^\\]]+\\])/";
		$matches = preg_split($pattern, $content->getElementsByTagName('text')->item(0)->nodeValue, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		$blob = '';
		$blob .= $this->text->make_chatcmd("Guide on AO-Universe.com", "/start http://www.ao-universe.com/main.php?site=knowledge&id={$guideid}") . "\n";
		$blob .= $this->text->make_chatcmd("Guide on AO-Universe.com Mobile", "/start http://www.ao-universe.com/mobile/index.php?id=14&pid={$guideid}") . "\n\n";

		$blob .= "Update: <highlight>" . $content->getElementsByTagName('update')->item(0)->nodeValue . "<end>\n";
		$blob .= "Class: <highlight>" . $content->getElementsByTagName('class')->item(0)->nodeValue . "<end>\n";
		$blob .= "Faction: <highlight>" . $content->getElementsByTagName('faction')->item(0)->nodeValue . "<end>\n";
		$blob .= "Level: <highlight>" . $content->getElementsByTagName('level')->item(0)->nodeValue . "<end>\n";
		$blob .= "Author: <highlight>" . $content->getElementsByTagName('author')->item(0)->nodeValue . "<end>\n\n";

		$blob .= $this->processMatches($matches);

		$blob .= "\n\n<yellow>Powered by<end> " . $this->text->make_chatcmd("AO-Universe.com", "/start http://www.ao-universe.com");

		$msg = $this->text->make_blob($title, $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * Search for an AO-U guide.
	 *
	 * @HandlesCommand("aou")
	 * @Matches("/^aou (.+)$/i")
	 */
	public function aouSearch($message, $channel, $sender, $sendto, $args) {
		$search = $args[1];

		$results = file_get_contents($this->url . "&mode=search&search=" . rawurlencode($search));

		$dom = new DOMDocument;
		$dom->loadXML($results);

		$guides = $dom->getElementsByTagName('guide');

		$blob = '';
		$count = 0;
		forEach ($guides as $guide) {
			$count++;
			$id = $guide->getElementsByTagName('id')->item(0)->nodeValue;
			$name = $guide->getElementsByTagName('name')->item(0)->nodeValue;
			$desc = $guide->getElementsByTagName('desc')->item(0)->nodeValue;
			$guide_link = $this->text->make_chatcmd("$name", "/tell <myname> aou $id");

			$blob .= "$guide_link\n{$desc}\n\n";
		}

		$blob .= "\n<yellow>Powered by<end> " . $this->text->make_chatcmd("AO-Universe.com", "/start http://www.ao-universe.com");

		if ($count > 0) {
			$msg = $this->text->make_blob("AO-U Guides containing '$search' ($count)", $blob);
		} else {
			$msg = "Could not find any guides containing: '$search'";
		}
		$sendto->reply($msg);
	}
	
	private function processMatches($matches) {
		$output = '';
		forEach ($matches as $match) {
			$output .= $this->processTag($match);
		}
		return $output;
	}
	
	private function processTag($tag) {
		switch ($tag) {
			case "[b]":
				return "<highlight>";
			case "[/b]":
				return "<end>";
		}
		
		if ($tag[0] == '[') {
			return "";
		}
		
		return $tag;
	}
}
