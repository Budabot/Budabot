<?php

/**
 * @Instance
 */
class PlayerHistoryManager {

	/** @Inject */
	public $cacheManager;
	
	/** @Inject */
	public $http;
	
	public function lookup($name, $rk_num) {
		return $this->lookupAuno($name, $rk_num);
	}
	
	public function lookupAuno($name, $rk_num) {
		$name = ucfirst(strtolower($name));
		$url = "http://auno.org/ao/char.php?output=xml&dimension=$rk_num&name=$name";
		$groupName = "player_history";
		$filename = "$name.$rk_num.history.xml";
		$maxCacheAge = 86400;
		$cb = function($data) {
			if (xml::spliceData($data, "<nick>", "</nick>") != "") {
				return true;
			} else {
				return false;
			}
		};

		$cacheResult = $this->cacheManager->lookup($url, $groupName, $filename, $cb, $maxCacheAge);

		//if there is still no valid data available give an error back
		if ($cacheResult->success !== true) {
			return null;
		} else {
			$obj = new PlayerHistory();
			$obj->name = $name;
		
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
			return $obj;
		}
	}
	
	public function lookupBudabot($name, $rk_num) {
		$name = ucfirst(strtolower($name));
		$url = "http://budabot.jkbff.com/pork/history.php";
		$params = array('server' => $rk_num, 'name' => $name);
		
		$response = $this->http->get($url)->withQueryParams($params)->waitAndReturnResponse();
		
		if ($response->error) {
			return null;
		} else {
			$obj = new PlayerHistory();
			$obj->name = $name;
			forEach (json_decode($response->body) as $history) {
				$entry = new stdClass;
				$entry->date = date("d-M-Y", $history->last_changed / 1000);
				$entry->level = $history->level;
				$entry->aiLevel = $history->defender_rank;
				$entry->faction = $history->faction;
				$entry->guild = $history->guild_name;
				$entry->rank = $history->guild_rank_name;
				$obj->data []= $entry;
			}
			return $obj;
		}
	}
}

class PlayerHistory {
	public $name;
	public $data;
}
