<?php
	if (!function_exists(matches)){
		function matches($probe, $comp) {
			$bits = explode(" ", $comp);
			$match = true;
			foreach ($bits as $substr) {
				if (stripos($probe, $substr) === false) {
					$match = false;
				}
			}
			return $match;
		}
	}

	if (!function_exists(contains)){
		function contains($ary, $str) {
			$match = false;
			foreach ($ary as $probe) {
				if (matches($probe, $str)) {
					$match = true;
					break;
				}
			}
			return $match;
		}
	}
	
	if (!function_exists(duplicate)){
		function duplicate($str, $ary) {
			$result = false;
			foreach ($ary as $value) {
				if ($value[0] == $str)
					$result = true;			
			}
			return $result;
		}
	}
	
	if (!function_exists(get_alias)) {
		function get_alias($ary, $comp) {
			//print_r($ary);
			foreach($ary as $alias) {
				if (matches($alias, $comp)) {
					$result = $alias;
					return $result;
				}
			} 
			
		}
	}
	
	if (!function_exists(make_info)) {
		function make_info($n, $ary) {
			$result = "<green><u>".$n."</u><end>:\n\n".
			 		  "<font color=#33ff66>Category</font>: ".array_shift($ary)."\n".
					  "<font color=#33ff66>Boosts</font>: ".array_shift($ary)."\n".
				      "<font color=#33ff66>QL range</font>: ".array_shift($ary)."\n".
				      "<font color=#33ff66>Aquisition</font>:\n<tab>".array_shift($ary)."\n".
				      "<font color=#33ff66>Buff Break points</font>:\n";
			foreach ($ary as $breakpoint) {
				$result .= "<tab>QL ".$breakpoint."\n";
			}
			return $result;
		}
	}
	
	if (!function_exists(is_unique)) {
		function is_unique($str, $list) {
			$unique = true;
			$count = 0;
			foreach ($list as $probe) {
				if (matches($probe, $str)) 
					$count++;
			}
			$unique = ($count == 1 ? true : false);
			return $unique;
		}
	}
?>

