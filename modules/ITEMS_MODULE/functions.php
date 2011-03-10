<?php

function download_newest_itemsdb() {
	$db = DB::get_instance();

	Logger::log('INFO', 'ITEMS_MODULE', "Starting items db update");

	// get list of files in ITEMS_MODULE
	$data = file_get_contents("http://budabot2.googlecode.com/svn/trunk/modules/ITEMS_MODULE");
	$data = str_replace("<hr noshade>", "", $data);  // not valid xml

	$xml = new SimpleXmlElement($data);

	// find the latest items db version on the server
	$latestVersion = null;
	forEach ($xml->body->ul->li as $item) {
		if (preg_match("/^aodb(.*)\\.sql$/i", $item->a, $arr)) {
			if ($latestVersion === null) {
				$latestVersion = $arr[1];
			} else if (Util::compare_version_numbers($arr[1], $currentVersion)) {
				$latestVersion = $arr[1];
			}
		}
	}

	if ($latestVersion !== null) {
		$currentVersion = Setting::get("aodb_db_version");
		
		// if server version is greater than current version, download and load server version
		if ($currentVersion === false || Util::compare_version_numbers($latestVersion, $currentVersion) > 0) {
			// download server version and save to ITEMS_MODULE directory
			$contents = file_get_contents("http://budabot2.googlecode.com/svn/trunk/modules/ITEMS_MODULE/aodb{$latestVersion}.sql");
			$fh = fopen("./modules/ITEMS_MODULE/aodb{$latestVersion}.sql", 'w');
			fwrite($fh, $contents);
			fclose($fh);
			
			$db->beginTransaction();
			
			// load the sql file into the db
			DB::loadSQLFile("ITEMS_MODULE", "aodb");
			
			$db->commit();
			
			Logger::log('INFO', 'ITEMS_MODULE', "Items db updated from '$currentVersion' to '$latestVersion'");
			$msg = "The items database has been updated to the latest version.  Version: $latestVersion";
		} else {
			Logger::log('INFO', 'ITEMS_MODULE', "Items db already up to date '$latestVersion'");
			$msg = "The items database is already up to date.  Version: $currentVersion";
		}
	} else {
		Logger::log('ERROR', 'ITEMS_MODULE', "Could not find latest items db on server");
		$msg = "There was a problem finding the latest version on the server";
	}
	
	Logger::log('INFO', 'ITEMS_MODULE', "Finished items db update");
	
	return $msg;
}

function find_items_from_xyphos($search, $ql = null) {
	$cidb_server = "http://cidb.xyphos.com";
	$url = $cidb_server;
	$url .= '?search=' . urlencode($search);
	$url .= "&output=aoml";

	if ($ql) {
		$url .= '&ql=' . $ql;
	}

	$msg = file_get_contents($url);
	if (empty($msg)) {
		$msg = "Unable to query Central Items Database.";
	}
	
	return $msg;
}

function find_items_from_local($search, $ql) {
	global $chatBot;
	$db = DB::get_instance();

	$tmp = explode(" ", $search);
	$first = true;
	forEach ($tmp as $key => $value) {
		// escape single quotes to prevent sql injection
		$value = str_replace("'", "''", $value);
		if ($first) {
			$query .= "`name` LIKE '%$value%'";
			$first = false;
		} else {
			$query .= " AND `name` LIKE '%$value%'";
		}
	}

	if ($ql) {
		$query .= " AND `lowql` <= $ql AND `highql` >= $ql";
	}

	$sql = "SELECT * FROM aodb WHERE $query ORDER BY `name` LIMIT 0, {$chatBot->settings["maxitems"]}";
	$db->query($sql);
	$num = $db->numrows();
	if ($num == 0) {
		if ($ql) {
			$msg = "No items found with QL <highlight>$ql<end>. Maybe try fewer keywords.";
		} else {
			$msg = "No items found. Maybe try fewer keywords.";
		}
		return $msg;
	}

	$countitems = 0;

	$data = $db->fObject('all');
	forEach ($data as $row) {
		if (!isset($itemlist[$row->name])) {
			$itemlist[$row->name] = array(array("lowid" => $row->lowid, "highid" => $row->highid, "lowql" => $row->lowql, "highql" => $row->highql, "icon" => $row->icon));
			$countitems++;
		} else if (isset($itemlist[$row->name])) {
			if ($itemlist[$row->name][0]["lowql"] > $row->lowql) {
				$itemlist[$row->name][0]["lowql"] = $row->lowql;
				$itemlist[$row->name][0]["lowid"] = $row->lowid;
			} else if ($itemlist[$row->name][0]["highql"] < $row->highql) {
				$itemlist[$row->name][0]["highql"] = $row->highql;
				$itemlist[$row->name][0]["highid"] = $row->highid;		    
			} else {
				$tmp = $itemlist[$row->name];
				$tmp[] = array("lowid" => $row->lowid, "highid" => $row->highid, "lowql" => $row->lowql, "highql" => $row->highql, "icon" => $row->icon);
				$itemlist[$row->name] = $tmp;
				$countitems++;
			}
		}
	}

	if ($countitems == 0) {
		if ($ql) {
			$msg = "No items found with QL <highlight>$ql<end>.";
		} else {
			$msg = "No items found.";
		}
		return $msg;
	}

	if ($countitems > 3) {
		forEach ($itemlist as $name => $item1) {
			forEach ($item1 as $key => $item) {
				$list .= "<img src='rdb://".$item["icon"]."'> \n";
				if ($ql) {
					$list .= "QL $ql ".Text::make_item($item["lowid"], $item["highid"], $ql, $name);
				} else {
					$list .= Text::make_item($item["lowid"], $item["highid"], $item["highql"], $name);		  
				}
		
				if ($item["lowql"] != $item["highql"]) {
					$list .= " (QL".$item["lowql"]." - ".$item["highql"].")\n\n";
				} else {
					$list .= " (QL".$item["lowql"].")\n\n";
				}
			}
		}

		$blob = "<header>::::: Item Search Result :::::<end>\n\n";
		$blob .= $list;
		$blob .= "\n\nItem DB Rip provided by MajorOutage (RK1)";
		$link = Text::make_link("$countitems results in total", $blob, 'blob');

		return $link;
	} else {
		forEach ($itemlist as $name => $item1) {
			forEach ($item1 as $key => $item) {
				if ($ql) {
					$link .= "\n QL $ql ".Text::make_item($item["lowid"], $item["highid"], $ql, $name);
				} else {
					$link .= "\n".Text::make_item($item["lowid"], $item["highid"], $item["highql"], $name);
				}
				
				if ($item["lowql"] != $item["highql"]) {
					$link .= " (QL".$item["lowql"]." - ".$item["highql"].")";
				} else {
					$link .= " (QL".$item["lowql"].")";
				}
			}
		}

		return $link;
	}
}

?>