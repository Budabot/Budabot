<?php

/**
 * @Instance
 */
class Util {

	/** @Inject */
	public $chatBot;

	const DATETIME = "d-M-Y H:i T";

	public function bytes_convert($bytes) {
		$ext = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$unitCount = 0;
		for (; $bytes > 1024; $unitCount++) {
			$bytes /= 1024;
		}
		return round($bytes, 2) ." ". $ext[$unitCount];
	}

	// taken from http://www.php.net/manual/en/function.date-diff.php
	public function unixtime_to_readable($time, $show_seconds = true) {
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

	public function parseTime($budatime) {
		$unixtime = 0;

		$matches = array();
		$pattern = '/([0-9]+)([a-z]+)/';
		preg_match_all($pattern, $budatime, $matches, PREG_SET_ORDER);

		forEach ($matches as $match) {
			switch ($match[2]) {
				case 'y':
				case 'yr':
				case 'year':
				case 'years':
					$unixtime += $match[1] * 31536000;
					break;
				case 'mo':
				case 'month':
				case 'months':
					$unixtime += $match[1] * 2592000;
					break;
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
	public function compare_version_numbers($ver1, $ver2) {
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
	public function get_profession_name($search) {
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

	public function get_profession_abbreviation($profession) {
		switch ($profession) {
			case "Adventurer":
				$prof = "Adv";
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
				$prof = "Eng";
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
	 * @name: verify_filename
	 * @description: returns true if filename matches budabot naming convention and false otherwise
	 */
	public function verify_filename($filename) {
		//Replace all \ characters with /
		$filename = str_replace("\\", "/", $filename);

		//check if the file exists
		forEach (array_reverse($this->chatBot->vars['module_load_paths']) as $modulePath) {
			if (file_exists("$modulePath/$filename")) {
				return "$modulePath/$filename";
			}
		}
		if (file_exists("./core/$filename")) {
			return "./core/$filename";
		}
		if (file_exists($filename)) {
			return $filename;
		}
		return "";
	}

	public function get_ability($ability, $getFullName = false) {
		$abilities = array(
			'agi' => 'Agility',
			'int' => 'Intelligence',
			'psy' => 'Psychic',
			'sta' => 'Stamina',
			'str' => 'Strength',
			'sen' => 'Sense'
		);

		$ability = strtolower(substr($ability, 0, 3));

		if (isset($abilities[$ability])) {
			if ($getFullName) {
				return $abilities[$ability];
			} else {
				return $ability;
			}
		} else {
			return null;
		}
	}

	public function rand_array_value($array) {
		return $array[rand(0, count($array) - 1)];
	}

	// checks to see if user is valid
	// invalid values:
	// $sender = -1 on 32bit
	// $sender = 4294967295
	// this function handles both 32 and 64 bit
	public function isValidSender($sender) {
		return (int)0xFFFFFFFF == $sender ? false : true;
	}

	// taken from: http://www.lost-in-code.com/programming/php-code/php-random-string-with-numbers-and-letters/
	public function genRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$string = '';
		for ($p = 0; $p < $length; $p++) {
			$string .= $characters[mt_rand(0, strlen($characters))];
		}
		return $string;
	}

	public function getStackTrace() {
		$trace = debug_backtrace();
		$arr1 = array();
		$arr2 = array();
		forEach ($trace as $obj) {
			$file = str_replace(getcwd(), "", $obj['file']);
			$arr1 []= "{$file}({$obj['line']})";
			$arr2 []= "{$obj['function']}()";
		}

		array_shift($arr2);

		$str = '';
		for ($i = 0; $i < count($arr1); $i++) {
			$str .= "$arr1[$i] : $arr2[$i]\n";
		}
		return $str;
	}

	/**
	 * Finds all occurrences of multiple strings in a string and returns them in an array
	 * with the key indicating the offset and the value indicating the string found.
	 *
	 * @param string   $haystack the string to search
	 * @param array    $needles an array of strings to search for
	 */
	public function strpos_r($haystack, $needles) {
		$seeks = array();
		
		forEach ($needles as $needle) {
			$search = $haystack;
			while ($seek = strrpos($search, $needle)) {
				$seeks[$seek] = $needle;
				$search = substr($search, 0, $seek);
			}
		}
		ksort($seeks);
		return $seeks;
	}
	
	public function getFilesInDirectory($path) {
		$files = array();
		if ($dir = dir($path)) {
			while (false !== ($entry = $dir->read())) {
				if (!is_dir($path . "/" . $entry)) {
					$files []= $entry;
				}
			}
			$dir->close();
		}
		return $files;
	}
	
	public function date($unixtime) {
		return date(self::DATETIME, $unixtime);
	}
	
	public function endsWith($string, $test) {
		$strlen = strlen($string);
		$testlen = strlen($test);
		if ($testlen > $strlen) {
			return false;
		}
		return substr_compare($string, $test, -$testlen) === 0;
	}
	
	// taken from: http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions
	function startsWith($haystack, $needle) {
		return !strncmp($haystack, $needle, strlen($needle));
	}
	
	public function stripColors($msg) {
		$msg = preg_replace("~<font color=#.{6}>~", "", $msg);
		$msg = preg_replace("~</font>~", "", $msg);
		return $msg;
	}
	
	public function generateQueryFromParams($params, $column) {
		$queryParams = array();
		$first = true;
		forEach ($params as $key => $value) {
			if ($value[0] == "-") {
				$value = substr($value, 1);
				$op = "NOT LIKE";
			} else {
				$op = "LIKE";
			}
			if ($first) {
				$query .= "$column $op ?";
				$first = false;
			} else {
				$query .= " AND $column $op ?";
			}
			$queryParams []= '%' . $value . '%';
		}
		return array($query, $queryParams);
	}
	
	// taken from http://php.net/manual/en/function.usort.php
	public function mergesort(&$array, $cmp_function) {
		// Arrays of size < 2 require no action.
		if (count($array) < 2) {
			return;
		}
		// Split the array in half
		$halfway = count($array) / 2;
		$array1 = array_slice($array, 0, $halfway);
		$array2 = array_slice($array, $halfway);
		// Recurse to sort the two halves
		$this->mergesort($array1, $cmp_function);
		$this->mergesort($array2, $cmp_function);
		// If all of $array1 is <= all of $array2, just append them.
		if ($cmp_function(end($array1), $array2[0]) < 1) {
			$array = array_merge($array1, $array2);
			return;
		}
		// Merge the two sorted arrays into a single sorted array
		$array = array();
		$ptr1 = $ptr2 = 0;
		while ($ptr1 < count($array1) && $ptr2 < count($array2)) {
			if ($cmp_function($array1[$ptr1], $array2[$ptr2]) < 1) {
				$array[] = $array1[$ptr1++];
			}
			else {
				$array[] = $array2[$ptr2++];
			}
		}
		// Merge the remainder
		while ($ptr1 < count($array1)) {
			$array[] = $array1[$ptr1++];
		}
		while ($ptr2 < count($array2)) {
			$array[] = $array2[$ptr2++];
		}
		return;
	}
}

?>
