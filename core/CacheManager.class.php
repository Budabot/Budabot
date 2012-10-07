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
		
		$cacheResult = new CacheResult();
	
		$data_found = false;
		$cacheFile = "$this->cache/$groupName/$filename";

		// Check if a xml file of the person exists and if it is uptodate
		if (file_exists($cacheFile)) {
			$cacheAge = time() - filemtime($cacheFile);
            if ($cacheAge < $maxCacheAge) {
				$data = file_get_contents($cacheFile);
				if (call_user_func($isValidCallback, $data)) {
					$cacheResult->data = $data;
					$cacheResult->cacheAge = $cacheAge;
					$cacheResult->usedCache = true;
					$cacheResult->oldCache = false;
					$cacheResult->success = true;
				} else {
					unset($data);
					@unlink($cacheFile);
				}
			}
        }

		//If no old history file was found or it was invalid try to update it from auno.org
		if ($cacheResult->success !== true) {
			$data = xml::getUrl($url, 20);
			if (call_user_func($isValidCallback, $data)) {
				$cacheResult->data = $data;
				$cacheResult->cacheAge = 0;
				$cacheResult->usedCache = false;
				$cacheResult->oldCache = false;
				$cacheResult->success = true;
			} else {
				unset($data);
			}
		}

		//If the site was not responding or the data was invalid and a xml file exists get that one
		if ($cacheResult->success !== true && file_exists($cacheFile)) {
			$data = file_get_contents($cacheFile);
			if (call_user_func($isValidCallback, $data)) {
				$cacheResult->data = $data;
				$cacheResult->cacheAge = time() - filemtime($cacheFile);
				$cacheResult->usedCache = true;
				$cacheResult->oldCache = true;
				$cacheResult->success = true;
			} else {
				unset($data);
				@unlink($cacheFile);
			}
		}
		
		// if a new file was downloaded, save it in the cache
		if ($cacheResult->usedCache === false) {
			// at least in windows, modifcation timestamp will not change unless this is done
			// not sure why that is the case --Tyrence
			@unlink($cacheFile);

	        $fp = fopen($cacheFile, "w");
	        fwrite($fp, $cacheResult->data);
	        fclose($fp);
	    }
		
		return $cacheResult;
    }
}

class CacheResult {
	public $success = false;
	public $usedCache = false;
	public $oldCache = false;
	public $cacheAge = 0;
	public $data;
}
