<?php
	function matches($probe, $comp) {
		$bits = explode(" ", $comp);
		$match = true;
		forEach ($bits as $substr) {
			if (stripos($probe, $substr) === false) {
				$match = false;
				break;
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
				break;
			}
		}
		return $result;
	}

	function get_alias($ary, $comp) {
		forEach ($ary as $alias) {
			if (matches($alias, $comp)) {
				return $alias;
			}
		}
		return null;
	}

	function make_info($row) {
		$result = "<green><u>$row->item_name</u><end>:\n\n".
				  "<font color=#33ff66>Category</font>: $row->category\n".
				  "<font color=#33ff66>Boosts</font>: $row->boosts\n".
				  "<font color=#33ff66>QL range</font>: $row->ql_range\n".
				  "<font color=#33ff66>Aquisition</font>:\n<tab>$row->acquisition\n".
				  "<font color=#33ff66>Buff Break points</font>:\n";
		
		forEach (explode("\\n", $row->buff_break_points) as $breakpoint) {
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
