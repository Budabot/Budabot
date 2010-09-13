<?php
	function matches($probe, $comp) {
		$bits = explode(" ", $comp);
		$match = true;
		forEach ($bits as $substr) {
			if (stripos($probe, $substr) === false) {
				$match = false;
			}
		}
		return $match;
	}

	function contains($ary, $str) {
		$match = false;
		forEach ($ary as $probe) {
			if (matches($probe, $str)) {
				$match = true;
				break;
			}
		}
		return $match;
	}

	function duplicate($str, $ary) {
		$result = false;
		forEach ($ary as $value) {
			if ($value[0] == $str) {
				$result = true;
			}
		}
		return $result;
	}

	function get_alias($ary, $comp) {
		//print_r($ary);
		forEach ($ary as $alias) {
			if (matches($alias, $comp)) {
				$result = $alias;
				return $result;
			}
		} 
		
	}

	function make_info($n, $ary) {
		$result = "<green><u>".$n."</u><end>:\n\n".
				  "<font color=#33ff66>Category</font>: ".array_shift($ary)."\n".
				  "<font color=#33ff66>Boosts</font>: ".array_shift($ary)."\n".
				  "<font color=#33ff66>QL range</font>: ".array_shift($ary)."\n".
				  "<font color=#33ff66>Aquisition</font>:\n<tab>".array_shift($ary)."\n".
				  "<font color=#33ff66>Buff Break points</font>:\n";
		forEach ($ary as $breakpoint) {
			$result .= "<tab>QL ".$breakpoint."\n";
		}
		return $result;
	}

	function is_unique($str, $list) {
		$unique = true;
		$count = 0;
		forEach ($list as $probe) {
			if (matches($probe, $str)) {
				$count++;
			}
		}
		$unique = ($count == 1 ? true : false);
		return $unique;
	}
?>

