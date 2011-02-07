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
		if ($time >= 0 && $time <= 59) {
			// Seconds
			$timeshift = $time.' seconds';
		} else if ($time >= 60 && $time <= 3599) {
			// Minutes + Seconds
			$pmin = $time / 60;
			$premin = explode('.', $pmin);
			
			$presec = $pmin-$premin[0];
			$sec = $presec * 60;
			
			$timeshift = $premin[0].' min';
			if ($show_seconds) {
				$timeshift .=  ' ' . round($sec, 0).' sec';
			}
		} else if ($time >= 3600 && $time <= 86399) {
			// Hours + Minutes
			$phour = $time / 3600;
			$prehour = explode('.',$phour);
			
			$premin = $phour - $prehour[0];
			$min = explode('.', $premin*60);
			
			$presec = '0.'.$min[1];
			$sec = $presec * 60;

			$timeshift = $prehour[0].' hrs '.$min[0].' min';
			if ($show_seconds) {
				$timeshift .= ' ' . round($sec, 0).' sec';
			}
		} else if ($time >= 86400) {
			// Days + Hours + Minutes
			$pday = $time / 86400;
			$preday = explode('.',$pday);

			$phour = $pday-$preday[0];
			$prehour = explode('.',$phour*24); 

			$premin = ($phour*24)-$prehour[0];
			$min = explode('.',$premin*60);
			
			$presec = '0.'.$min[1];
			$sec = $presec * 60;
			
			$timeshift = $preday[0].' days '.$prehour[0].' hrs '.$min[0].' min';
			if ($show_seconds) {
				$timeshift .= ' ' . round($sec, 0).' sec';
			}
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
