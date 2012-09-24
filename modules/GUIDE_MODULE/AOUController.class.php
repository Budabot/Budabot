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
	
	/** @Inject */
	public $itemsController;
	
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
		
		if ($dom->getElementsByTagName('error')->length > 0) {
			$msg = "An error occurred while trying to retrieve AOU guide with id <highlight>$guideid<end>: " .
				$dom->getElementsByTagName('text')->item(0)->nodeValue;
			$sendto->reply($msg);
			return;
		}

		$content = $dom->getElementsByTagName('content')->item(0);

		$title = $content->getElementsByTagName('name')->item(0)->nodeValue;

		$blob = '';
		$blob .= $this->text->make_chatcmd("Guide on AO-Universe.com", "/start http://www.ao-universe.com/main.php?site=knowledge&id={$guideid}") . "\n";
		$blob .= $this->text->make_chatcmd("Guide on AO-Universe.com Mobile", "/start http://www.ao-universe.com/mobile/index.php?id=14&pid={$guideid}") . "\n\n";

		$blob .= "Update: <highlight>" . $content->getElementsByTagName('update')->item(0)->nodeValue . "<end>\n";
		$blob .= "Class: <highlight>" . $content->getElementsByTagName('class')->item(0)->nodeValue . "<end>\n";
		$blob .= "Faction: <highlight>" . $content->getElementsByTagName('faction')->item(0)->nodeValue . "<end>\n";
		$blob .= "Level: <highlight>" . $content->getElementsByTagName('level')->item(0)->nodeValue . "<end>\n";
		$blob .= "Author: <highlight>" . $content->getElementsByTagName('author')->item(0)->nodeValue . "<end>\n\n";

		$blob .= $this->processInput($content->getElementsByTagName('text')->item(0)->nodeValue);

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
		
		$sections = $dom->getElementsByTagName('section');
		$blob = '';
		$count = 0;
		forEach ($sections as $section) {
			$blob .= "<pagebreak><header2>" . $this->getSearchResultCategory($section) . "<end>\n";
			$guides = $section->getElementsByTagName('guide');
			forEach ($guides as $guide) {
				$count++;
				$blob .= '  ' . $this->getGuideLink($guide) . "\n";
			}
			$blob .= "\n";
		}

		$blob .= "\n<yellow>Powered by<end> " . $this->text->make_chatcmd("AO-Universe.com", "/start http://www.ao-universe.com");

		if ($count > 0) {
			$msg = $this->text->make_blob("AO-U Guides containing '$search' ($count)", $blob);
		} else {
			$msg = "Could not find any guides containing: '$search'";
		}
		$sendto->reply($msg);
	}
	
	private function getSearchResultCategory($section) {
		$folders = $section->getElementsByTagName('folder');
		$output = array();
		forEach ($folders as $folder) {
			$output []= $folder->getElementsByTagName('name')->item(0)->nodeValue;
		}
		return implode(" - ", array_reverse($output));
	}
	
	private function getGuideLink($guide) {
		$id = $guide->getElementsByTagName('id')->item(0)->nodeValue;
		$name = $guide->getElementsByTagName('name')->item(0)->nodeValue;
		$desc = $guide->getElementsByTagName('desc')->item(0)->nodeValue;
		return $this->text->make_chatcmd("$name", "/tell <myname> aou $id") . " - " . $desc;
	}
	
	private function replaceItem($arr) {
		$type = $arr[1];
		$id = $arr[3];
		
		$output = '';

		$data = $this->itemsController->findById($id);
		if (count($data) > 0) {
			$output = $this->generateItemMarkup($type, $data[0]);
		} else {
			$obj = $this->itemsController->doXyphosLookup($id);
			if (null == $obj) {
				$output = $id;
			} else if ($obj->icon == 0) {  // for perks and items that aren't displayable in game
				$output = $this->text->make_chatcmd($obj->name, "/start http://www.xyphos.com/ao/aodb.php?id={$obj->lowid}");
			} else {
				$output = $this->generateItemMarkup($type, $obj);
			}
		}
		return $output;
	}
	
	private function replaceWaypoint($arr) {
		$label = $arr[2];
		$params = explode(" ", $arr[1]);
		forEach($params as $param) {
			list($name, $value) = explode("=", $param);
			$$name = $value;
		}
		
		return $this->text->make_chatcmd("/waypoint $x $y $pid", $label);
	}
	
	private function processInput($input) {
		$input = preg_replace_callback("/\\[(item|itemname|itemicon)( nolink)?\\](\\d+)\\[\\/(item|itemname|itemicon)\\]/i", array($this, 'replaceItem'), $input);
		$input = preg_replace_callback("/\\[waypoint ([^\\]]+)\\](.*?)\\[\\/waypoint\\]/", array($this, 'replaceWaypoint'), $input);

		$pattern = "/(\\[[^\\]]+\\])/";
		$matches = preg_split($pattern, $input, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

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
			case "[ts_ts]":
				return "\n+\n";
			case "[ts_ts2]":
				return "\n=\n";
			case "[cttr]":
			case "[br]":
				return "\n";
		}

		if ($tag[0] == '[') {
			return "";
		}

		return $tag;
	}
	
	private function generateItemMarkup($type, $obj) {
		$output = '';
		if ($type == "item" || $type == "itemicon") {
			$output .= $this->text->make_image($obj->icon) . "\n";
		}
		
		if ($type == "item" || $type == "itemname") {
			$output .= $this->text->make_item($obj->lowid, $obj->highid, $obj->highql, $obj->name);
		}
		
		if ($type == "item") {
			$output .= "\n";
		}
		return $output;
	}
}
