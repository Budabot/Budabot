<?php

/**
 * @Instance
 */
class PlayerHistoryManager {

	/** @Inject */
	public $cacheManager;
	
	public function lookup($name, $rk_num = 0) {
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
			$obj->errorCode = 1;
			$obj->errorInfo = "Could not get History of $name on RK $rk_num";
		} else {
			//parsing of the xml file
			$data = xml::spliceData($cacheResult->data, "<history>", "</history>");
			$data = xml::splicemultidata($data, "<entry", "/>");
			forEach ($data as $hdata) {
				preg_match("/date=\"(.+)\" level=\"(.+)\" ailevel=\"(.*)\" faction=\"(.+)\" guild=\"(.*)\" rank=\"(.*)\"/i", $hdata, $arr);
				$obj->data[$arr[1]]["level"] = $arr[2];
				$obj->data[$arr[1]]["ailevel"] = $arr[3];
				$obj->data[$arr[1]]["faction"] = $arr[4];
				$obj->data[$arr[1]]["guild"] = $arr[5];
				$obj->data[$arr[1]]["rank"] = $arr[6];
			}
		}
		
		return $obj;
    }
}

class PlayerHistory {
	public $name;
	public $data;
	public $errorInfo;
	public $errorCode = 0;
}