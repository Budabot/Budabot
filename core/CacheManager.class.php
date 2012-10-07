<?php

/**
 * @Instance
 */
class CacheManager {

	/** @Inject */
	public $chatBot;
	
	private $cache;

	/** @Setup */
	public function __construct() {
		global $vars;
		$this->cache = $vars["cachefolder"];

		//Making sure that the cache folder exists
        if (!dir($this->cache)) {
	        mkdir($this->cache, 0777);
		}
	}

	public function lookup($url, $groupName, $filename, $isValidCallback, $maxCacheAge = 86400) {
		if (empty($groupName)) {
			throw new Exception("Cache group name cannot be empty");
		}
		
		if (!dir($this->cache . '/' . $groupName)) {
	        mkdir($this->cache . '/' . $groupName, 0777);
		}
	
		$data_found = false;
		$data_save = false;
		$cacheFile = "$this->cache/$groupName/$filename";

		// Check if a xml file of the person exists and if it is uptodate
		if (file_exists($cacheFile)) {
            if (time() - filemtime($cacheFile) < $maxCacheAge) {
				$data = file_get_contents($cacheFile);
				if (call_user_func($isValidCallback, $data)) {
					$data_found = true;
				} else {
					$data_found = false;
					unset($data);
					@unlink($cacheFile);
				}
			}
        }

		//If no old history file was found or it was invalid try to update it from auno.org
		if (!$data_found) {
			$data = xml::getUrl($url, 20);
			if (call_user_func($isValidCallback, $data)) {
				$data_found = true;
				$data_save = true;
			} else {
				$data_found = false;
				unset($data);
			}
		}

		//If the site was not responding or the data was invalid and a xml file exists get that one
		if (!$data_found && file_exists($cacheFile)) {
			$data = file_get_contents($cacheFile);
			if (call_user_func($isValidCallback, $data)) {
				$data_found = true;
			} else {
				$data_found = false;
				unset($data);
				@unlink($cacheFile);
			}
		}
		
		// if a new file was downloaded, save it in the cache
		if ($data_save) {
			// at least in windows, modifcation timestamp will not change unless this is done
			// not sure why that is the case --Tyrence
			@unlink($cacheFile);

	        $fp = fopen($cacheFile, "w");
	        fwrite($fp, $data);
	        fclose($fp);
	    }
		
		return $data;
    }
}
