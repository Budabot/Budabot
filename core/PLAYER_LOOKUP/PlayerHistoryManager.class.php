<?php

/**
 * @Instance
 */
class PlayerHistoryManager {

	/** @Inject */
	public $cacheManager;
	
	public function lookup($name, $rk_num) {
		$name = ucfirst(strtolower($name));
		$url = "http://auno.org/ao/char.php?output=xml&dimension=$rk_num&name=$name";
		$groupName = "player_history";
		$filename = "$name.$rk_num.history.xml";
		$maxCacheAge = 86400;
		$cb = create_function('$data',
			'if (xml::spliceData($data, "<nick>", "</nick>") != "") {
				return true;
			} else {
				return false;
			}');

		$cacheResult = $this->cacheManager->lookup($url, $groupName, $filename, $cb, $maxCacheAge);
		
		$obj = new PlayerHistory();
		$obj->name = $name;

		//if there is still no valid data available give an error back
		if ($cacheResult->success !== true) {
			return null;
		} else {
			//parsing of the xml file
			$data = xml::spliceData($cacheResult->data, "<history>", "</history>");
			$data = xml::splicemultidata($data, "<entry", "/>");
			forEach ($data as $hdata) {
				preg_match("/date=\"(.+)\" level=\"(.+)\" ailevel=\"(.*)\" faction=\"(.+)\" guild=\"(.*)\" rank=\"(.*)\"/i", $hdata, $arr);
				$entry = new stdClass;
				$entry->date = $arr[1];
				$entry->level = $arr[2];
				$entry->aiLevel = $arr[3];
				$entry->faction = $arr[4];
				$entry->guild = $arr[5];
				$entry->rank = $arr[6];
				$obj->data []= $entry;
			}
		}
		
		return $obj;
	}
}

class PlayerHistory {
	public $name;
	public $data;
}
