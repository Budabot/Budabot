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
		if ($time == 0) {
			return '0 secs';
		}
	
		if ($time > 0) {
			$days = floor($time / 86400);
		} else {
			$days = ceil($time / 86400);
		}
		$remainder = $time % 86400;
		
		if ($remainder > 0) {
			$hours = floor($remainder / 3600);
		} else {
			$hours = ceil($remainder / 3600);
		}
		$remainder = $remainder % 3600;
		
		if ($remainder > 0) {
			$minutes = floor($remainder / 60);
		} else {
			$minutes = ceil($remainder / 60);
		}
		$remainder = $remainder % 60;
		
		$seconds = $remainder;
		
		$timeshift = '';
		if ($days != 0) {
			$timeshift .= $days . ' days ';
		}
		if ($hours != 0) {
			$timeshift .= $hours . ' hrs ';
		}
		if ($minutes != 0) {
			$timeshift .= $minutes . ' mins ';
		}
		if ($seconds != 0 && ($show_seconds || $timeshift == '')) {
			$timeshift .= $seconds . ' secs';
		}
		return trim($timeshift);
	}
	
	public static function parseTime($budatime) {
		$unixtime = 0;
		
		$matches = array();
		$pattern = '/([0-9]+)([a-z]+)/';
		preg_match_all($pattern, $budatime, $matches, PREG_SET_ORDER);
		
		forEach ($matches as $match) {
			switch ($match[2]) {
				case 'weeks':
				case 'week':
				case 'w':
					$unixtime += $match[1] * 604800;
					break;
				case 'days':
				case 'day':
				case 'd':
					$unixtime += $match[1] * 86400;
					break;
				case 'hours':
				case 'hour':
				case 'hrs':
				case 'hr':
				case 'h':
					$unixtime += $match[1] * 3600;
					break;
				case 'mins':
				case 'min':
				case 'm':
					$unixtime += $match[1] * 60;
					break;
				case 'secs':
				case 'sec':
				case 's':
					$unixtime += $match[1];
					break;
				default:
					return 0;
			}
		}
		
		return $unixtime;
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
		$search = strtolower($search);
		switch ($search) {
			case "adv":
			case "advy":
			case "adventurer":
				$prof = "Adventurer";
				break;
			case "agent":
				$prof = "Agent";
				break;
			case "crat":
			case "bureaucrat":
				$prof = "Bureaucrat";
				break;
			case "doc":
			case "doctor":
				$prof = "Doctor";
				break;
			case "enf":
			case "enfo":
			case "enforcer":
				$prof = "Enforcer";
				break;
			case "eng":
			case "engi":
			case "engineer":
				$prof = "Engineer";
				break;
			case "fix":
			case "fixer":
				$prof = "Fixer";
				break;
			case "keep":
			case "keeper":
				$prof = "Keeper";
				break;
			case "ma":
			case "martialartist":
			case "martial artist":
				$prof = "Martial Artist";
				break;
			case "mp":
			case "meta":
			case "metaphysicist":
			case "meta-physicist":
				$prof = "Meta-Physicist";
				break;
			case "nt":
			case "nanotechnician":
			case "nano-technician":
				$prof = "Nano-Technician";
				break;
			case "sol":
			case "sold":
			case "soldier":
				$prof = "Soldier";
				break;
			case "trader":
				$prof = "Trader";
				break;
			case "shade":
				$prof = "Shade";
				break;
			default:
				$prof = '';
		}
		
		return $prof;
	}
	
	public static function get_profession_abbreviation($profession) {
		switch ($profession) {
			case "Adventurer":
				$prof = "Advy";
				break;
			case "agent":
				$prof = "Agent";
				break;
			case "Bureaucrat":
				$prof = "Crat";
				break;
			case "Doctor":
				$prof = "Doc";
				break;
			case "Enforcer":
				$prof = "Enf";
				break;
			case "Engineer":
				$prof = "Engy";
				break;
			case "Fixer":
				$prof = "Fixer";
				break;
			case "Keeper":
				$prof = "Keeper";
				break;
			case "Martial Artist":
				$prof = "MA";
				break;
			case "Meta-Physicist":
				$prof = "MP";
				break;
			case "Nano-Technician":
				$prof = "NT";
				break;
			case "Soldier":
				$prof = "Sol";
				break;
			case "Trader":
				$prof = "Trader";
				break;
			case "Shade":
				$prof = "Shade";
				break;
			default:
				$prof = "Unknown";
				break;
	    }
		
		return $prof;
	}
	
	/**
	 * @name: verify_name_convention
	 * @description: returns true if filename matches budabot naming convention and false otherwise
	 */
	public static function verify_name_convention($filename) {
		if (preg_match("/^(.+)\\/([0-9a-z_]+)\\.(php|txt)$/i", $filename, $arr) && $arr[2] == strtolower($arr[2])) {
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
	
	public static function get_ability($ability) {
		$abilities = array('agi', 'int', 'psy', 'sta', 'str', 'sen');
		
		$ability = strtolower(substr($ability, 0, 3));

		if (in_array($ability, $abilities)) {
			return $ability;
		} else {
			return null;
		}
	}
	
	public static function rand_array_value($array) {
		return $array[rand(0, sizeof($array) - 1)];
	}
	
	// checks to see if user is valid
	// invalid values:
	// $sender = -1 on 32bit
	// $sender = 4294967295
	// this function handles both 32 and 64 bit
	public static function isValidSender($sender) {
		return (int)0xFFFFFFFF == $sender ? false : true;
	}
	
	// taken from: http://www.lost-in-code.com/programming/php-code/php-random-string-with-numbers-and-letters/
	public static function genRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$string = '';    
		for ($p = 0; $p < $length; $p++) {
			$string .= $characters[mt_rand(0, strlen($characters))];
		}
		return $string;
	}
}

?>
