<?php
	// interpolation function.
	if (!function_exists(interpolate)){
		function interpolate($x1, $x2, $y1, $y2, $x) {
			$result = ($y2 - $y1)/($x2 - $x1) * ($x - $x1) + $y1;
			$result = round($result,0);
			return $result;
		}
	}
	
	if (!function_exists(timestamp)){
		function timestamp($sec_value) {
			$stamp = "";
			if ($sec_value > 3599) {
				$hours = floor($sec_value/3600);
				$sec_value = $sec_value - $hours*3600;
				$stamp .= "<orange>".$hours."<end> hour(s) ";
			}
			if ($sec_value > 59) {
				$minutes = floor($sec_value/60);
				$sec_value = $sec_value - $minutes*60;
				$stamp .= "<orange>".$minutes."<end> minute(s) ";
			}
			if (!($sec_value == 0))
				$stamp .= "<orange>".$sec_value."<end> second(s)";
			return $stamp;
		}
	}
?>