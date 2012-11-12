<?php

/**
 * Defines API for other modules to access the item database.
 */
interface ItemsAPI {
	/**
	 * Returns item reference to item with given $ql and $name.
	 * Used by: ALIEN_MODULE
	 */
	public function findItem($ql, $name);
}

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
class ItemsController implements ItemsAPI {
	/** @Inject */
	public $db;

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Logger */
	public $logger;

	public $moduleName;

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
	 * This command handler searches for an item by id.
	 *
	 * @HandlesCommand("itemid")
	 * @Matches("/^itemid ([0-9]+)$/i")
	 */
	public function itemIdCommand($message, $channel, $sender, $sendto, $args) {
		$id = $args[1];

		$data = $this->findById($id);
		$num = count($data);
		if ($num == 0) {
			$output = "No item found with id <highlight>$id<end>.";
		} else {
			$output = trim($this->formatSearchResults($data, false, false));
		}

		$sendto->reply($output);
	}
	
	public function findById($id) {
		$sql = "SELECT * FROM aodb WHERE highid = ? UNION SELECT * FROM aodb WHERE lowid = ? LIMIT 1";
		$data = $this->db->query($sql, $id, $id);
		return $data;
	}

	/**
	 * This command handler downloads the latest version of the items db.
	 *
	 * @HandlesCommand("updateitems")
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
		$data = file_get_contents("http://budabot2.googlecode.com/svn/trunk/modules/ITEMS_MODULE");
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
				$contents = file_get_contents("http://budabot2.googlecode.com/svn/trunk/modules/ITEMS_MODULE/aodb{$latestVersion}.sql");
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

		$sql = "SELECT * FROM aodb WHERE $query ORDER BY `name` ASC, highql DESC LIMIT 0, " . $this->settingManager->get("maxitems");
		$data = $this->db->query($sql);
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
			$blob .= $this->formatSearchResults($data, $ql, true);
			$xrdbLink = $this->text->make_chatcmd("XRDB", "/start http://www.xyphos.com/viewtopic.php?f=6&t=10000091");
			$budabotItemsExtractorLink = $this->text->make_chatcmd("Budabot Items Extractor", "/start http://budabot.com/forum/viewtopic.php?f=7&t=873");
			$blob .= "\n\n<highlight>Item DB rips created using Xyphos' $xrdbLink tool and the $budabotItemsExtractorLink plugin<end>";
			$link = $this->text->make_blob("Item Search Results ($num)", $blob);

			return $link;
		} else {
			return trim($this->formatSearchResults($data, $ql, false));
		}
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
		$url = "http://itemxml.xyphos.com/?id={$id}";
		$data = file_get_contents($url);
		
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

	/**
	 * Implemented from ItemsAPI interface.
	 */
	public function findItem($ql, $name) {
		$row = $this->db->queryRow("SELECT * FROM aodb WHERE name = ? AND lowql <= ? AND highql >= ?", $name, $ql, $ql);
		return $this->text->make_item($row->lowid, $row->highid, $ql, $row->name);
	}
}

?>
