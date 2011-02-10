<?php

class Util {
	public static function bytesConvert($bytes) {
		$ext = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$unitCount = 0;
		for (; $bytes > 1024; $unitCount++) {
			$bytes /= 1024;
		}
		return round($bytes, 2) ." ". $ext[$unitCount];
	}
	
	// taken from http://www.php.net/manual/en/function.date-diff.php
	public static function unixtime_to_readable($time, $show_seconds = true) {
		$days = round($time / 86400);
		$remainder = $time % 86400;
		
		$hours = round($remainder / 3600);
		$remainder = $remainder % 3600;
		
		$minutes = round($remainder / 60);
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
}

?>
