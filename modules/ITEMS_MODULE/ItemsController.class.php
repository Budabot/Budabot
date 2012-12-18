<?php

/**
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'items',
 *		accessLevel = 'all',
 *		description = 'Searches for an item',
 *		help        = 'items.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'itemid',
 *		accessLevel = 'all',
 *		description = 'Searches for an item by id',
 *		help        = 'items.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'updateitems',
 *		accessLevel = 'guild',
 *		description = 'Downloads the latest version of the items db',
 *		help        = 'updateitems.txt'
 *	)
 */
class ItemsController {
	
	public $moduleName;

	/** @Inject */
	public $db;

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $http;

	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Logger */
	public $logger;

	/**
	 * @Setting("maxitems")
	 * @Description("Number of Items shown on the list")
	 * @Visibility("edit")
	 * @Type("number")
	 * @Options("30;40;50;60")
	 */
	public $defaultMaxitems = "40";

	/** @Setup */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "aodb");
	}

	/**
	 * This command handler searches for an item.
	 *
	 * @HandlesCommand("items")
	 * @Matches("/^items ([0-9]+) (.+)$/i")
	 * @Matches("/^items (.+)$/i")
	 */
	public function itemsCommand($message, $channel, $sender, $sendto, $args) {
		if (count($args) == 3) {
			$ql = $args[1];
			if (!($ql >= 1 && $ql <= 500)) {
				$msg = "QL must be between 1 and 500.";
				$sendto->reply($msg);
				return;
			}
			$search = $args[2];
		} else {
			$search = $args[1];
			$ql = false;
		}

		$search = htmlspecialchars_decode($search);
		$msg = $this->find_items_from_local($search, $ql);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("itemid")
	 * @Matches("/^itemid ([0-9]+)$/i")
	 */
	public function itemIdCommand($message, $channel, $sender, $sendto, $args) {
		$id = $args[1];

		$row = $this->findById($id);
		if ($row === null) {
			$output = "No item found with id <highlight>$id<end>.";
		} else {
			$output = trim($this->formatSearchResults(array($row), false, false));
		}

		$sendto->reply($output);
	}
	
	public function findById($id) {
		$sql = "SELECT * FROM aodb WHERE highid = ? UNION SELECT * FROM aodb WHERE lowid = ? LIMIT 1";
		return $this->db->queryRow($sql, $id, $id);
	}

	/**
	 * @HandlesCommand("updateitems")
	 * @Matches("/^updateitems$/i")
	 */
	public function updateitemsCommand($message, $channel, $sender, $sendto) {
		$msg = $this->download_newest_itemsdb();
		$sendto->reply($msg);
	}

	/**
	 * @Event("24hrs")
	 * @Description("Check to make sure items db is the latest version available")
	 */
	public function checkForUpdate() {
		$msg = $this->download_newest_itemsdb();
		if (preg_match("/^The items database has been updated/", $msg)) {
			$this->chatBot->sendGuild($msg);
		}
	}

	public function download_newest_itemsdb() {
		$this->logger->log('DEBUG', "Starting items db update");

		// get list of files in ITEMS_MODULE
		$data = $this->http->get("http://budabot2.googlecode.com/svn/trunk/modules/ITEMS_MODULE/")->waitAndReturnResponse()->body;
		$data = str_replace("<hr noshade>", "", $data);  // not valid xml

		try {
			$xml = new SimpleXmlElement($data);

			// find the latest items db version on the server
			$latestVersion = null;
			forEach ($xml->body->ul->li as $item) {
				if (preg_match("/^aodb(.*)\\.sql$/i", $item->a, $arr)) {
					if ($latestVersion === null) {
						$latestVersion = $arr[1];
					} else if ($this->util->compare_version_numbers($arr[1], $currentVersion)) {
						$latestVersion = $arr[1];
					}
				}
			}
		} catch (Exception $e) {
			$msg = "Error updating items db: " . $e->getMessage();
			$this->logger->log('ERROR', $msg);
			return $msg;
		}

		if ($latestVersion !== null) {
			$currentVersion = $this->settingManager->get("aodb_db_version");

			// if server version is greater than current version, download and load server version
			if ($currentVersion === false || $this->util->compare_version_numbers($latestVersion, $currentVersion) > 0) {
				// download server version and save to ITEMS_MODULE directory
				$contents = $this->http->get("http://budabot2.googlecode.com/svn/trunk/modules/ITEMS_MODULE/aodb{$latestVersion}.sql")
					->waitAndReturnResponse()->body;
				$fh = fopen("./modules/ITEMS_MODULE/aodb{$latestVersion}.sql", 'w');
				fwrite($fh, $contents);
				fclose($fh);

				$this->db->begin_transaction();

				// load the sql file into the db
				$this->db->loadSQLFile("ITEMS_MODULE", "aodb");

				$this->db->commit();

				$this->logger->log('INFO', "Items db updated from '$currentVersion' to '$latestVersion'");
				$msg = "The items database has been updated to the latest version.  Version: $latestVersion";
			} else {
				$this->logger->log('DEBUG', "Items db already up to date '$currentVersion'");
				$msg = "The items database is already up to date.  Version: $currentVersion";
			}
		} else {
			$this->logger->log('ERROR', "Could not find latest items db on server");
			$msg = "There was a problem finding the latest version on the server";
		}

		$this->logger->log('DEBUG', "Finished items db update");

		return $msg;
	}

	public function find_items_from_local($search, $ql) {
		$tmp = explode(" ", $search);
		$first = true;
		forEach ($tmp as $key => $value) {
			$value = str_replace("'", "''", $value);
			if ($value[0] == "-") {
				$value = substr($value, 1);
				$op = "NOT LIKE";
			} else {
				$op = "LIKE";
			}
			if ($first) {
				$query .= "`name` $op '%$value%'";
				$first = false;
			} else {
				$query .= " AND `name` $op '%$value%'";
			}
		}

		if ($ql) {
			$query .= " AND `lowql` <= $ql AND `highql` >= $ql";
		}

		$sql = "SELECT * FROM aodb WHERE $query ORDER BY `name` ASC, highql DESC LIMIT 1000";
		$data = $this->db->query($sql);
		$data = $this->orderSearchResults($data, $tmp);
		$data = array_slice($data, 0, $this->settingManager->get("maxitems"));

		$resultsLimited = false;
		if (count($data) > $this->settingManager->get("maxitems")) {
			$resultsLimited = true;
		}
		$num = count($data);
		
		if ($num == 0) {
			if ($ql) {
				$msg = "No QL <highlight>$ql<end> items found matching <highlight>$search<end>.";
			} else {
				$msg = "No items found matching <highlight>$search<end>.";
			}
			return $msg;
		} else if ($num > 3) {
			$blob = "Version: " . $this->settingManager->get('aodb_db_version') . "\n";
			if ($ql) {
				$blob .= "Search: QL $ql $search\n\n";
			} else {
				$blob .= "Search: $search\n\n";
			}
			if ($resultsLimited === true) {
				$blob .= "*Note: Results have been limited to the first " . $this->settingManager->get("maxitems") . " results.\n\n";
			}
			$blob .= $this->formatSearchResults($data, $ql, true);
			$xrdbLink = $this->text->make_chatcmd("XRDB", "/start http://www.xyphos.com/viewtopic.php?f=6&t=10000091");
			$budabotItemsExtractorLink = $this->text->make_chatcmd("Budabot Items Extractor", "/start http://budabot.com/forum/viewtopic.php?f=7&t=873");
			//$blob .= "\n\n<highlight>Item DB rips created using Xyphos' $xrdbLink tool and the $budabotItemsExtractorLink plugin<end>";
			$link = $this->text->make_blob("Item Search Results ($num)", $blob);

			return $link;
		} else {
			return trim($this->formatSearchResults($data, $ql, false));
		}
	}
	
	// sort by exact word matches higher than partial word matches
	public function orderSearchResults($data, $searchTerms) {
		$newData = array();
		forEach ($data as $key => $row) {
			$match = false;
			$itemKeywords = preg_split("/\s/", $row->name);
			$numExactMatches = 0;
			forEach ($itemKeywords as $keyword) {
				forEach ($searchTerms as $searchWord) {
					if (strcasecmp($keyword, $searchWord) == 0) {
						$numExactMatches++;
						break;
					}
				}
			}
			$row->numExactMatches = $numExactMatches;
		}
		
		usort($data, function($a, $b) { return ($a->numExactMatches > $b->numExactMatches) ? -1 : 1; });
		
		return $data;
	}

	public function formatSearchResults($data, $ql, $showImages) {
		$list = '';
		forEach ($data as $row) {
			if ($showImages) {
				$list .= $this->text->make_image($row->icon) . "\n";
			}
			if ($ql) {
				$list .= "QL $ql ".$this->text->make_item($row->lowid, $row->highid, $ql, $row->name);
			} else {
				$list .= $this->text->make_item($row->lowid, $row->highid, $row->highql, $row->name);
			}
			if ($row->lowql != $row->highql) {
				$list .= " (QL".$row->lowql." - ".$row->highql.")\n";
			} else {
				$list .= " (QL".$row->lowql.")\n";
			}
			if ($showImages) {
				$list .= "\n";
			}
		}
		return $list;
	}
	
	public function doXyphosLookup($id) {
		$data = $this->http->get('http://itemxml.xyphos.com/')->withQueryParams(array('id' => $id))
			->waitAndReturnResponse()->body;
		if (empty($data) || '<error>' == substr($data, 0, 7)) {
			return null;
		}
		
		$data = preg_replace_callback("|<description>(.+)</description>|s", array($this, 'escapeDescription'), $data);
		
		$doc = new DOMDocument();
		$doc->prevservWhiteSpace = false;
		$doc->loadXML($data);
		
		$obj = new stdClass;
		
		if ($doc->documentElement === null) {
			$this->logger->log('WARN', "Could not parse xml: '$url'");
			return null;
		} else {
			$obj->lowid = $doc->getElementsByTagName('low')->item(0)->attributes->getNamedItem("id")->nodeValue;
			$obj->highid = $doc->getElementsByTagName('high')->item(0)->attributes->getNamedItem("id")->nodeValue;
			$obj->highql = $doc->getElementsByTagName('high')->item(0)->attributes->getNamedItem("ql")->nodeValue;
			$obj->name = $doc->getElementsByTagName('name')->item(0)->nodeValue;

			$attributes = $doc->getElementsByTagName('attribute');
			$obj->icon = 0;
			forEach ($attributes as $attribute) {
				if ($attribute->attributes->getNamedItem("name")->nodeValue == "Icon") {
					$obj->icon = $attribute->attributes->getNamedItem("value")->nodeValue;
				}
			}
		}

		return $obj;
	}
	
	private function escapeDescription($arr) {
		return "<description>" . htmlspecialchars($arr[1]) . "</description>";
	}
	
	public function findByName($name, $ql = null) {
		if ($ql === null) {
			return $this->db->queryRow("SELECT * FROM aodb WHERE name = ? ORDER BY highql DESC, highid DESC", $name);
		} else {
			return $this->db->queryRow("SELECT * FROM aodb WHERE name = ? AND lowql <= ? AND highql >= ? ORDER BY highid DESC", $name, $ql, $ql);
		}
	}

	public function getItem($name, $ql) {
		$row = $this->findByName($name, $ql);
		if ($row === null) {
			$this->logger->log("WARN", "Could not find item '$name' at QL '$ql'");
		} else {
			return $this->text->make_item($row->lowid, $row->highid, $ql, $row->name);
		}
	}
	
	public function getItemAndIcon($name, $ql) {
		$row = $this->findByName($name, $ql);
		if ($row === null) {
			$this->logger->log("WARN", "Could not find item '$name' at QL '$ql'");
		} else {
			return $this->text->make_image($row->icon) . "\n" .
				$this->text->make_item($row->lowid, $row->highid, $row->highql, $row->name);
		}
	}
}

?>
