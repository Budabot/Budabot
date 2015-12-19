<?php

namespace Budabot\User\Modules;

use Exception;
use stdClass;
use DOMDocument;

/**
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'citems',
 *		accessLevel = 'all',
 *		description = 'Searches for an item using the central items db',
 *		help        = 'items.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'litems',
 *		accessLevel = 'all',
 *		description = 'Searches for an item using the local items db',
 *		help        = 'items.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'items',
 *		accessLevel = 'all',
 *		description = 'Searches for an item using the default items db',
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

	/** @Setup */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "aodb");
		
		$this->settingManager->add($this->moduleName, 'maxitems', 'Number of items shown on the list', 'edit', 'number', '40', '30;40;50;60');
		$this->settingManager->add($this->moduleName, 'items_database', 'Use local items database or a central (remote) items database', 'edit', 'options', 'central', 'local;central');
		$this->settingManager->add($this->moduleName, 'cidb_url', "The URL of the CIDB to use (if items_database is set to 'remote')", 'edit', 'text', 'http://cidb.botsharp.net/', 'http://cidb.botsharp.net/');
	}

	/**
	 * @HandlesCommand("items")
	 * @Matches("/^items ([0-9]+) (.+)$/i")
	 * @Matches("/^items (.+)$/i")
	 */
	public function itemsCommand($message, $channel, $sender, $sendto, $args) {
		$msg = $this->find_items($args);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("citems")
	 * @Matches("/^citems ([0-9]+) (.+)$/i")
	 * @Matches("/^citems (.+)$/i")
	 */
	public function citemsCommand($message, $channel, $sender, $sendto, $args) {
		$msg = $this->find_items($args, 'central');
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("litems")
	 * @Matches("/^litems ([0-9]+) (.+)$/i")
	 * @Matches("/^litems (.+)$/i")
	 */
	public function litemsCommand($message, $channel, $sender, $sendto, $args) {
		$msg = $this->find_items($args, 'local');
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
		$data = $this->http
			->get("https://api.github.com/repos/Budabot/Budabot/contents/modules/ITEMS_MODULE")
			->withHeader('User-Agent', 'Budabot')
			->waitAndReturnResponse()
			->body;

		try {
			$json = json_decode($data);
		
			// find the latest items db version on the server
			$latestVersion = null;
			forEach ($json as $item) {
				if (preg_match("/^aodb(.*)\\.sql$/i", $item->name, $arr)) {
					if ($latestVersion === null) {
						$latestVersion = $arr[1];
					} else if ($this->util->compareVersionNumbers($arr[1], $currentVersion)) {
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
			if ($currentVersion === false || $this->util->compareVersionNumbers($latestVersion, $currentVersion) > 0) {
				// download server version and save to ITEMS_MODULE directory
				$contents = $this->http
					->get("https://raw.githubusercontent.com/Budabot/Budabot/master/modules/ITEMS_MODULE/aodb{$latestVersion}.sql")
					->withHeader('User-Agent', 'Budabot')
					->waitAndReturnResponse()
					->body;

				$fh = fopen("./modules/ITEMS_MODULE/aodb{$latestVersion}.sql", 'w');
				fwrite($fh, $contents);
				fclose($fh);

				$this->db->beginTransaction();

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

	public function find_items($args, $db = null) {
		if (count($args) == 3) {
			$ql = $args[1];
			if (!($ql >= 1 && $ql <= 500)) {
				return "QL must be between 1 and 500.";
			}
			$search = $args[2];
		} else {
			$search = $args[1];
			$ql = false;
		}

		$search = htmlspecialchars_decode($search);
	
		if ($db == null) {
			$db = $this->settingManager->get('items_database');
		}
		switch ($db) {
			case 'local':
				// local database
				$data = $this->find_items_from_local($search, $ql);

				$budabotItemsExtractorLink = $this->text->make_chatcmd("Budabot Items Extractor", "/start https://github.com/Budabot/ItemsExtractor");
				$footer = "Item DB rips created using the $budabotItemsExtractorLink tool.";

				$msg = $this->createItemsBlob($data, $search, $ql, $this->settingManager->get('aodb_db_version'), 'local', $footer);
				break;
			default:
				// central items database
				$url = $this->settingManager->get('cidb_url');
				$obj = $this->find_items_from_remote($search, $ql, $url);

				if ($obj == null) {
					$msg = "Unable to query Central Items Database.";
				} else {
					$msg = $this->createItemsBlob($obj->results, $search, $ql, $obj->version, $url, '', $obj->elapsed);
				}
				break;
		}
		return $msg;
	}
	
	/*
	 * Method to query the Central Items Database - Demoder
	 */
	public function find_items_from_remote($search, $ql, $server) {
		$parameters = array(
			"bot" => "Budabot",
			"output" => "json",
			"max" => "250",
			"version" => "1.2"
		);

		if ($ql > 0) {
			$parameters["ql"] = $ql;
		}
		
		// special search commands for aoitems.com
		$searchParams = explode(' ', $search);
		$specialSearch = array('type', 'slot', 'ql');
		forEach ($searchParams as $key => $searchParam) {
			forEach ($specialSearch as $s) {
				if ($this->util->startsWith($searchParam, $s . '=')) {
					$value = substr($searchParam, strlen($s) + 1);
					if (!empty($value)) {
						unset($searchParams[$key]);
						$parameters[$s] = $value;
					}
				}
			}
		}
		$search = implode(' ', $searchParams);
		$parameters['search'] = $search;

		$startTime = microtime(true);
		$response = $this->http->get($server)->withQueryParams($parameters)->waitAndReturnResponse();
		$elapsed = microtime(true) - $startTime;
		if (empty($response) || empty($response->body)) {
			return null;
		} else {
			$obj = json_decode($response->body);
			$obj->elapsed = $elapsed;
			
			// change attribute names to match expected format
			forEach ($obj->results as $item) {
				$item->lowid = $item->LowID;
				$item->highid = $item->HighID;
				$item->lowql = $item->LowQL;
				$item->highql = $item->HighQL;
				$item->name = $item->Name;
				$item->icon = $item->Icon;
			}
			
			// sort results to match Budabot local results order, and restrict to first 40 results
			$data = $this->orderSearchResults($obj->results, $search);
			$obj->results = array_slice($data, 0, $this->settingManager->get("maxitems"));
			
			return $obj;
		}
	}
	
	public function find_items_from_local($search, $ql) {
		$tmp = explode(" ", $search);
		list($query, $params) = $this->util->generateQueryFromParams($tmp, 'name');

		if ($ql) {
			$query .= " AND `lowql` <= ? AND `highql` >= ?";
			$params []= $ql;
			$params []= $ql;
		}

		$sql = "SELECT * FROM aodb WHERE $query ORDER BY `name` ASC, highql DESC LIMIT 1000";
		$data = $this->db->query($sql, $params);
		$data = $this->orderSearchResults($data, $search);
		$data = array_slice($data, 0, $this->settingManager->get("maxitems"));
		
		return $data;
	}
	
	public function createItemsBlob($data, $search, $ql, $version, $server, $footer, $elapsed) {
		$num = count($data);
		if ($num == 0) {
			if ($ql) {
				$msg = "No QL <highlight>$ql<end> items found matching <highlight>$search<end>.";
			} else {
				$msg = "No items found matching <highlight>$search<end>.";
			}
			return $msg;
		} else if ($num < 4) {
			return trim($this->formatSearchResults($data, $ql, false));
		} else {
			$blob = "Version: <highlight>$version<end>\n";
			if ($ql) {
				$blob .= "Search: <highlight>QL $ql $search<end>\n";
			} else {
				$blob .= "Search: <highlight>$search<end>\n";
			}
			$blob .= "Server: <highlight>" . $server . "<end>\n";
			if (isset($elapsed)) {
				$blob .= "Time: <highlight>" . round($elapsed, 2) . "s<end>\n";
			}
			$blob .= "\n";
			$blob .= $this->formatSearchResults($data, $ql, true);
			if ($num == $this->settingManager->get('maxitems')) {
				$blob .= "\n\n<highlight>*Results have been limited to the first " . $this->settingManager->get("maxitems") . " results.<end>";
			}
			$blob .= "\n\n" . $footer;
			$link = $this->text->make_blob("Item Search Results ($num)", $blob);

			return $link;
		}
	}
	
	// sort by exact word matches higher than partial word matches
	public function orderSearchResults($data, $search) {
		$searchTerms = explode(" ", $search);
		forEach ($data as $row) {
			if (strcasecmp($search, $row->name) == 0) {
				$numExactMatches = 100;
			} else {
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
			}
			$row->numExactMatches = $numExactMatches;
		}
		
		$this->util->mergesort($data, function($a, $b) {
			if ($a->numExactMatches == $b->numExactMatches) {
				return 0;
			} else {
				return ($a->numExactMatches > $b->numExactMatches) ? -1 : 1;
			}
		});
		
		return $data;
	}

	public function formatSearchResults($data, $ql, $showImages) {
		$list = '';
		forEach ($data as $row) {
			if ($showImages) {
				$list .= $this->text->make_image($row->icon) . "\n";
			}
			if ($ql) {
				$list .= "QL $ql " . $this->text->make_item($row->lowid, $row->highid, $ql, $row->name);
			} else {
				$list .= $this->text->make_item($row->lowid, $row->highid, $row->highql, $row->name);
			}
			if ($row->lowql != $row->highql) {
				$list .= " (QL" . $row->lowql . " - " . $row->highql . ")\n";
			} else {
				$list .= " (QL" . $row->lowql . ")\n";
			}
			if ($showImages) {
				$list .= "\n<pagebreak>";
			}
		}
		return $list;
	}
	
	public function getDetailedItemInfo($id, $ql = null) {
		// leaving this function here in case something replaces this functionality in the future
		return null;
		
		$params = array('id' => $id);
		if ($ql !== null) {
			$params['ql'] = $ql;
		}
		
		$url = 'http://itemxml.xyphos.com/';
	
		$response = $this->http->get($url)->withQueryParams($params)->waitAndReturnResponse();
		$data = $response->body;
		if ($response->error || empty($data) || '<error>' == substr($data, 0, 7)) {
			return null;
		}
		
		$data = preg_replace_callback("|<description>(.+)</description>|s", array($this, 'escapeDescription'), $data);
		
		$doc = new DOMDocument();
		$doc->prevservWhiteSpace = false;
		$doc->loadXML($data);
		
		$obj = new stdClass;
		
		if ($doc->documentElement === null) {
			$this->logger->log('WARN', "Could not parse xml: '$url' " . print_r($params, true));
			return null;
		} else {
			$obj->lowid = $doc->getElementsByTagName('low')->item(0)->attributes->getNamedItem("id")->nodeValue;
			$obj->highid = $doc->getElementsByTagName('high')->item(0)->attributes->getNamedItem("id")->nodeValue;
			$obj->highql = $doc->getElementsByTagName('high')->item(0)->attributes->getNamedItem("ql")->nodeValue;
			$obj->name = $doc->getElementsByTagName('name')->item(0)->nodeValue;

			$attributes = $doc->getElementsByTagName('attribute');
			$obj->icon = 0;
			forEach ($attributes as $attribute) {
				$name = $attribute->attributes->getNamedItem("name")->nodeValue;
				$value = $attribute->attributes->getNamedItem("value")->nodeValue;
				if ($name == "Icon") {
					$obj->icon = $value;
				}
				$obj->attributes->$name->value = $value;
				$obj->attributes->$name->extra = $attribute->attributes->getNamedItem("extra")->nodeValue;
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

	public function getItem($name, $ql = null) {
		$row = $this->findByName($name, $ql);
		$ql = ($ql === null ? $row->highql : $ql);
		if ($row === null) {
			$this->logger->log("WARN", "Could not find item '$name' at QL '$ql'");
		} else {
			return $this->text->make_item($row->lowid, $row->highid, $ql, $row->name);
		}
	}
	
	public function getItemAndIcon($name, $ql = null) {
		$row = $this->findByName($name, $ql);
		$ql = ($ql === null ? $row->highql : $ql);
		if ($row === null) {
			$this->logger->log("WARN", "Could not find item '$name' at QL '$ql'");
		} else {
			return $this->text->make_image($row->icon) . "\n" .
				$this->text->make_item($row->lowid, $row->highid, $ql, $row->name);
		}
	}
}

?>
