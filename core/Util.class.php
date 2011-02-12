<?php

class Util {
	public static function bytes_convert($bytes) {
		$ext = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$unitCount = 0;
		for (; $bytes > 1024; $unitCount++) {
			$bytes /= 1024;
		}
		return round($bytes, 2) ." ". $ext[$unitCount];
	}
	
	// taken from http://www.php.net/manual/en/function.date-diff.php
	public static function unixtime_to_readable($time, $show_seconds = true) {
		$days = floor($time / 86400);
		$remainder = $time % 86400;
		
		$hours = floor($remainder / 3600);
		$remainder = $remainder % 3600;
		
		$minutes = floor($remainder / 60);
		$remainder = $remainder % 60;
		
		$seconds = $remainder;
		
		if ($days != 0) {
			$timeshift .= $days . ' days ';
		}
		if ($hours != 0) {
			$timeshift .= $hours . ' hrs ';
		}
		if ($minutes != 0) {
			$timeshift .= $minutes . ' min ';
		}
		if ($seconds != 0) {
			$timeshift .= $seconds . ' sec';
		}
		return $timeshift;
	}
	
	/**
	 * Takes two version numbers.  Returns 1 if the first is greater than the second.
	 * Returns -1 if the second is greater than the first.  Returns 0 if they are equal.
	 */
	public static function compare_version_numbers($ver1, $ver2) {
		$ver1Array = explode('.', $ver1);
		$ver2Array = explode('.', $ver2);
		
		for ($i = 0; $i < count($ver1Array) && $i < count($ver2Array); $i++) {
			if ($ver1Array[$i] > $ver2Array[$i]) {
				return 1;
			} else if ($ver1Array[$i] < $ver2Array[$i]) {
				return -1;
			}
		}
		
		if (count($ver1Array) > count($ver2Array)) {
			return 1;
		} else if (count($ver1Array) < count($ver2Array)) {
			return -1;
		} else {
			return 0;
		}
	}
	
	/**
	 * @name: get_profession_name
	 * @description: returns the full profession name given the search string passed in
	 */
	public static function get_profession_name($search) {
		$search = substr(strtolower($search), 0, 3);
		switch ($search) {
			case "adv":
				$prof = "Adventurer";
				break;
			case "age":
				$prof = "Agent";
				break;
			case "bur":
			case "cra":
				$prof = "Bureaucrat";
				break;
			case "doc":
				$prof = "Doctor";
				break;
			case "enf":
				$prof = "Enforcer";
				break;
			case "eng":
				$prof = "Engineer";
				break;
			case "fix":
				$prof = "Fixer";
				break;
			case "kee":
				$prof = "Keeper";
				break;
			case "mar":
			case "ma":
				$prof = "Martial Artist";
				break;
			case "met":
			case "mp":
				$prof = "Meta-Physicist";
				break;
			case "nan":
			case "nt":
				$prof = "Nano-Technician";
				break;
			case "sol":
				$prof = "Soldier";
				break;
			case "tra":
				$prof = "Trader";
				break;
			case "sha":
				$prof = "Shade";
				break;
			default:
				$prof = '';
		}
		
		return $prof;
	}
	
	/**
	 * @name: verify_name_convention
	 * @description: returns true if filename matches budabot naming convention and false otherwise
	 */
	public static function verify_name_convention($filename) {
		preg_match("/^(.+)/([0-9a-z_]+).php$/i", $filename, $arr);
		if ($arr[2] == strtolower($arr[2])) {
			return true;
		} else {
			Logger::log('ERROR', 'Core', "Warning: $filename does not match the nameconvention(All php files needs to be in lowercases except loading files)!");
			return false;
		}
	}
	
	/**
	 * @name: verify_filename
	 * @description: returns true if filename matches budabot naming convention and false otherwise
	 */
	public static function verify_filename($filename) {
		//Replace all \ characters with /
		$filename = str_replace("\\", "/", $filename);

		if (!Util::verify_name_convention($filename)) {
			return "";
		}

		//check if the file exists
	    if (file_exists("./core/$filename")) {
	        return "./core/$filename";
    	} else if (file_exists("./modules/$filename")) {
        	return "./modules/$filename";
		} else if (file_exists($filename)) {
        	return $filename;
	    } else {
	     	return "";
	    }
	}
}

?>
