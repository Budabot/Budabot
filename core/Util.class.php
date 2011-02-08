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
		// Days + Hours + Minutes
		$pday = $time / 86400;
		$preday = explode('.',$pday);

		$phour = $pday-$preday[0];
		$prehour = explode('.',$phour*24); 

		$premin = ($phour*24)-$prehour[0];
		$min = explode('.',$premin*60);
		
		$presec = '0.'.$min[1];
		$sec = round($presec * 60);
		
		if ($preday[0] != 0) {
			$timeshift .= $preday[0] . ' days ';
		}
		if ($prehour[0] != 0) {
			$timeshift .= $prehour[0] . ' hrs ';
		}
		if ($min[0] != 0) {
			$timeshift .= $min[0] . ' min ';
		}
		if ($sec != 0) {
			$timeshift .= $sec . ' sec';
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
}

?>
