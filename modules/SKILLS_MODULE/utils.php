<?php

function calc_attack_time_reduction($init_skill) {
	if ($init_skill > 1200) {
		$RechTk = $init_skill - 1200;
		$attack_time_reduction = ($RechTk / 600) + 6;
	} else {
		$attack_time_reduction = ($init_skill / 200);
	}

	return $attack_time_reduction;
}

function calc_bar_setting($effective_attack_time) {
	if ($effective_attack_time < 0) {
		return 88 + (88 * $effective_attack_time);
	} else if ($effective_attack_time > 0) {
		return 88 + (12 * $effective_attack_time);
	} else {
		return 88;
	}
}

function calc_inits($attack_time) {
	if ($attack_time < 0) {
		return 0;
	} else if ($attack_time < 6) {
		return round($attack_time * 200, 2);
	} else {
		return round(1200 + ($attack_time - 6) * 600, 2);
	}
}

function interpolate($x1, $x2, $y1, $y2, $x) {
	$result = ($y2 - $y1)/($x2 - $x1) * ($x - $x1) + $y1;
	$result = round($result,0);
	return $result;
}

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

function cap_full_auto($attack_time, $recharge_time, $full_auto_recharge) {
	$hard_cap = floor(10 + $attack_time);
	$skill_cap = ((40 * $recharge_time) + ($full_auto_recharge / 100) - 11) * 25;

	return array($hard_cap, $skill_cap);
}

function cap_burst($attack_time, $recharge_time, $burst_recharge) {
	$hard_cap = round($attack_time + 8,0);
	$skill_cap = floor((($recharge_time * 20) + ($burst_recharge / 100) - 8) * 25);

	return array($hard_cap, $skill_cap);
}

function cap_fling_shot($attack_time) {
	$hard_cap = 5 + $attack_time;
	$skill_cap = (($attack_time * 16) - $hard_cap) * 100;

	return array($hard_cap, $skill_cap);
}

function cap_fast_attack($attack_time) {
	$hard_cap = 5 + $attack_time;
	$skill_cap = (($attack_time * 16) - $hard_cap) * 100;

	return array($hard_cap, $skill_cap);
}

function cap_aimed_shot($attack_time, $recharge_time) {
	$hard_cap = floor($attack_time + 10);
	$skill_cap = ceil((4000 * $recharge_time - 1100) / 3);
	//$skill_cap = round((($recharge_time * 4000) - ($attack_time * 100) - 1000) / 3);
	//$skill_cap = ceil(((4000 * $recharge_time) - 1000) / 3);

	return array($hard_cap, $skill_cap);
}

?>
