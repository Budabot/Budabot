<?php

namespace Budabot\User\Modules;

use stdClass;
use DOMDocument;

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

	/** @Inject */
	public $http;

	const AOU_URL = "http://www.ao-universe.com/mobile/parser.php?bot=budabot";
	
	/**
	 * View an AO-U guide.
	 *
	 * @HandlesCommand("aou")
	 * @Matches("/^aou (\d+)$/i")
	 */
	public function aouView($message, $channel, $sender, $sendto, $args) {
		$guideid = $args[1];

		$params = array(
			'mode' => 'view',
			'id' => $guideid
		);
		$guide = $this->http->get(self::AOU_URL)->withQueryParams($params)->waitAndReturnResponse()->body;

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
		$blob .= $this->text->makeChatcmd("Guide on AO-Universe.com", "/start http://www.ao-universe.com/main.php?site=knowledge&id={$guideid}") . "\n\n";

		$blob .= "Update: <highlight>" . $content->getElementsByTagName('update')->item(0)->nodeValue . "<end>\n";
		$blob .= "Profession: <highlight>" . $content->getElementsByTagName('class')->item(0)->nodeValue . "<end>\n";
		$blob .= "Faction: <highlight>" . $content->getElementsByTagName('faction')->item(0)->nodeValue . "<end>\n";
		$blob .= "Level: <highlight>" . $content->getElementsByTagName('level')->item(0)->nodeValue . "<end>\n";
		$blob .= "Author: <highlight>" . $this->processInput($content->getElementsByTagName('author')->item(0)->nodeValue) . "<end>\n\n";

		$blob .= $this->processInput($content->getElementsByTagName('text')->item(0)->nodeValue);

		$blob .= "\n\n<highlight>Powered by<end> " . $this->text->makeChatcmd("AO-Universe.com", "/start http://www.ao-universe.com");

		$msg = $this->text->makeBlob($title, $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * Search for an AO-U guide and include guides that have the search terms in the guide text.
	 *
	 * @HandlesCommand("aou")
	 * @Matches("/^aou all (.+)$/i")
	 */
	public function aouAllSearch($message, $channel, $sender, $sendto, $args) {
		$search = $args[1];

		$msg = $this->searchForAOUGuide($search, true);
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

		$msg = $this->searchForAOUGuide($search, false);
		$sendto->reply($msg);
	}
	
	public function searchForAOUGuide($search, $searchGuideText) {
		$searchTerms = explode(" ", $search);
	
		$params = array(
			'mode' => 'search',
			'search' => $search
		);
		$results = $this->http->get(self::AOU_URL)->withQueryParams($params)->waitAndReturnResponse()->body;

		$dom = new DOMDocument;
		$dom->loadXML($results);
		
		$sections = $dom->getElementsByTagName('section');
		$blob = '';
		$count = 0;
		forEach ($sections as $section) {
			$category = $this->getSearchResultCategory($section);
		
			$guides = $section->getElementsByTagName('guide');
			$tempBlob = '';
			$found = false;
			forEach ($guides as $guide) {
				$guideObj = $this->getGuideObject($guide);
				// since aou returns guides that have keywords in the guide body, we filter the results again
				// to only include guides that contain the keywords in the category, name, or description
				if ($searchGuideText || $this->striposarray($category . ' ' . $guideObj->name . ' ' . $guideObj->description, $searchTerms)) {
					$count++;
					$tempBlob .= '  ' . $this->text->makeChatcmd("$guideObj->name", "/tell <myname> aou $guideObj->id") . " - " . $guideObj->description . "\n";
					$found = true;
				}
			}
			
			if ($found) {
				$blob .= "<pagebreak><header2>" . $category . "<end>\n";
				$blob .= $tempBlob;
				$blob .= "\n";
			}
		}

		$blob .= "\n<highlight>Powered by<end> " . $this->text->makeChatcmd("AO-Universe.com", "/start http://www.ao-universe.com");

		if ($count > 0) {
			if ($searchGuideText) {
				$title = "All AO-U Guides containing '$search' ($count)";
			} else {
				$title = "AO-U Guides containing '$search' ($count)";
			}
			$msg = $this->text->makeBlob($title, $blob);
		} else {
			$msg = "Could not find any guides containing: '$search'.";
			if (!$searchGuideText) {
				$msg .= " Try including all results with <highlight>!aou all $search<end>.";
			}
		}
		return $msg;
	}
	
	private function striposarray($haystack, $needles) {
		forEach ($needles as $needle) {
			if (stripos($haystack, $needle) === false) {
				return false;
			}
		}
		return true;
	}
	
	private function getSearchResultCategory($section) {
		$folders = $section->getElementsByTagName('folder');
		$output = array();
		forEach ($folders as $folder) {
			$output []= $folder->getElementsByTagName('name')->item(0)->nodeValue;
		}
		return implode(" - ", array_reverse($output));
	}
	
	private function getGuideObject($guide) {
		$obj = new stdClass;
		$obj->id = $guide->getElementsByTagName('id')->item(0)->nodeValue;
		$obj->name = $guide->getElementsByTagName('name')->item(0)->nodeValue;
		$obj->description = $guide->getElementsByTagName('desc')->item(0)->nodeValue;
		return $obj;
	}
	
	private function replaceItem($arr) {
		$type = $arr[1];
		$id = $arr[3];
		
		$output = '';

		$row = $this->itemsController->findById($id);
		if ($row !== null) {
			$output = $this->generateItemMarkup($type, $row);
		} else {
			$obj = $this->itemsController->getDetailedItemInfo($id);
			if (null == $obj) {
				$output = $id;
			} else if ($obj->icon == 0) {  // for perks and items that aren't displayable in game
				$output = $this->text->makeChatcmd($obj->name, "/start https://aoitems.com/item/{$obj->lowid}");
			} else {
				$output = $this->generateItemMarkup($type, $obj);
			}
		}
		return $output;
	}
	
	private function replaceWaypoint($arr) {
		$label = $arr[2];
		$params = explode(" ", $arr[1]);
		forEach ($params as $param) {
			list($name, $value) = explode("=", $param);
			$$name = $value;
		}
		
		return $this->text->makeChatcmd($label . " ({$x}x{$y})", "/waypoint $x $y $pf");
	}
	
	private function replaceGuideLinks($arr) {
		$url = $arr[2];
		$label = $arr[3];
		
		if (preg_match("/pid=(\\d+)/", $url, $idArray)) {
			return $this->text->makeChatcmd($label, "/tell <myname> aou " . $idArray[1]);
		} else {
			return $this->text->makeChatcmd($label, "/start $url");
		}
	}
	
	private function processInput($input) {
		$input = preg_replace_callback("/\\[(item|itemname|itemicon)( nolink)?\\](\\d+)\\[\\/(item|itemname|itemicon)\\]/i", array($this, 'replaceItem'), $input);
		$input = preg_replace_callback("/\\[waypoint ([^\\]]+)\\]([^\\]]*)\\[\\/waypoint\\]/", array($this, 'replaceWaypoint'), $input);
		$input = preg_replace_callback("/\\[(localurl|url)=([^ \\]]+)\\]([^\\[]+)\\[\\/(localurl|url)\\]/", array($this, 'replaceGuideLinks'), $input);
		$input = preg_replace("/\\[img\\]([^\\[]+)\\[\\/img\\]/", "-image-", $input);
		$input = preg_replace("/\\[color=#([0-9A-F]+)\\]([^\\[]+)\\[\\/color\\]/", "<font color=#\\1>\\2</font>", $input);
		$input = preg_replace("/\\[color=([^\\]]+)\\]([^\\[]+)\\[\\/color\\]/", "<\\1>\\2<end>", $input);
		$input = str_replace("[center]", "<center>", $input);
		$input = str_replace("[/center]", "</center>", $input);
		$input = str_replace("[i]", "<i>", $input);
		$input = str_replace("[/i]", "</i>", $input);
		$input = str_replace("[b]", "<highlight>", $input);
		$input = str_replace("[/b]", "<end>", $input);

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
			case "[ts_ts]":
				return " + ";
			case "[ts_ts2]":
				return " = ";
			case "[cttd]":
				return " | ";
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
			$output .= $this->text->make_image($obj->icon);
		}
		
		if ($type == "item" || $type == "itemname") {
			$output .= $this->text->makeItem($obj->lowid, $obj->highid, $obj->highql, $obj->name);
		}

		return $output;
	}
}
