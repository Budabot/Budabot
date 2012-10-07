<?php

/**
 * @Instance
 */
class PlayerHistoryManager extends Annotation {

	/** @Inject */
	public $chatBot;
	
	private $cache;

	/** @Setup */
	public function setup() {
		$this->cache = $this->chatBot->vars["cachefolder"];

		//Making sure that the cache folder exists
        if (!dir($this->cache)) {
	        mkdir($this->cache, 0777);
		}
	}

	public function lookup($name, $rk_num = 0) {
		$data_found = false;
		$data_save = false;
		$name = ucfirst(strtolower($name));
		$cacheFile = "$this->cache/$name.$rk_num.history.xml";

		//Check if a xml file of the person exists and if it is uptodate
		if (file_exists($cacheFile)) {
	        $mins = (time() - filemtime($cacheFile)) / 60;
            $hours = floor($mins/60);
            if ($hours < 24) {
				$playerhistory = file_get_contents($cacheFile);
				if (xml::spliceData($playerhistory, '<nick>', '</nick>') == $name) {
					$data_found = true;
				} else {
					$data_found = false;
					unset($playerhistory);
					@unlink($cacheFile);
				}
			}
        }

		//If no old history file was found or it was invalid try to update it from auno.org
		if (!$data_found) {
			$playerhistory = xml::getUrl("http://auno.org/ao/char.php?output=xml&dimension=$rk_num&name=$name", 20);
			if (xml::spliceData($playerhistory, '<nick>', '</nick>') == $name) {
				$data_found = true;
				$data_save = true;
			} else {
				$data_found = false;
				unset($playerhistory);
			}
		}

		//If the site was not responding or the data was invalid and a xml file exists get that one
		if (!$data_found && file_exists($cacheFile)) {
			$playerhistory = file_get_contents($cacheFile);
			if (xml::spliceData($playerhistory, '<nick>', '</nick>') == $name) {
				$data_found = true;
			} else {
				$data_found = false;
				unset($playerhistory);
				@unlink($cacheFile);
			}
		}
		
		$obj = new PlayerHistory();
		$obj->name = $name;

		//if there is still no valid data available give an error back
		if (!$data_found) {
			$obj->errorCode = 1;
			$obj->errorInfo = "Couldn't get History of $name on RK $rk_num";
			return;
		}

		//parsing of the xml file
		$data = xml::spliceData($playerhistory, "<history>", "</history>");
		$data = xml::splicemultidata($data, "<entry", "/>");
		forEach ($data as $hdata) {
			preg_match("/date=\"(.+)\" level=\"(.+)\" ailevel=\"(.*)\" faction=\"(.+)\" guild=\"(.*)\" rank=\"(.*)\"/i", $hdata, $arr);
			$obj->data[$arr[1]]["level"] = $arr[2];
			$obj->data[$arr[1]]["ailevel"] = $arr[3];
			$obj->data[$arr[1]]["faction"] = $arr[4];
			$obj->data[$arr[1]]["guild"] = $arr[5];
			$obj->data[$arr[1]]["rank"] = $arr[6];
		}

		//if he downloaded a new xml file save it in the cache folder
		if ($data_save) {
	        $fp = fopen($cacheFile, "w");
	        fwrite($fp, $playerbio);
	        fclose($fp);
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