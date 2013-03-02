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
		$name = ucfirst(strtolower($name));
		$url = "http://budabot.jkbff.com/pork/history.php?server=$rk_num&name=$name";
		$groupName = "player_history";
		$filename = "$name.$rk_num.history.json";
		$maxCacheAge = 86400;
		$cb = function($data) {
			if ($data == "[]") {
				return false;
			} else {
				return true;
			}
		};
		
		$cacheResult = $this->cacheManager->lookup($url, $groupName, $filename, $cb, $maxCacheAge);
		
		if ($cacheResult->success !== true) {
			return null;
		} else {
			$obj = new PlayerHistory();
			$obj->name = $name;
			$obj->data = json_decode($cacheResult->data);
			return $obj;
		}
	}
}

class PlayerHistory {
	public $name;
	public $data;
}
