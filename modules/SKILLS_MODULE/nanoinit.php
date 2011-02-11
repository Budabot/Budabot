<?php

if (!function_exists('calc_attack_time_reduction')) {
	function calc_attack_time_reduction($init_skill) {
		if ($init_skill > 1200) {
			$RechTk = $init_skill - 1200;
			$attack_time_reduction = ($RechTk / 600) + 6;
		} else {
			$attack_time_reduction = ($init_skill / 200);
		}
		
		return $attack_time_reduction;
	}
}

if (!function_exists('calc_bar_setting')) {
	function calc_bar_setting($effective_attack_time) {
		if ($effective_attack_time < 0) {
			return 88 + (88 * $effective_attack_time);
		} else if ($effective_attack_time > 0) {
			return 88 + (12 * $effective_attack_time);
		} else {
			return 88;
		}
	}
}

if (!function_exists('calc_inits')) {
	function calc_inits($attack_time) {
		if ($attack_time < 0) {
			return 0;
		} else if ($attack_time < 6) {
			return round($attack_time * 200, 2);
		} else {
			return round(1200 + ($attack_time - 6) * 600, 2);
		}
	}
}

$info = explode(" ", $message);
list($msg, $attack_time, $init_skill) = $info;

if (!$attack_time || !$init_skill) {
	$syntax_error = true;
} else {

	$attack_time_reduction = calc_attack_time_reduction($init_skill);
	$effective_attack_time = $attack_time - $attack_time_reduction;

	$bar_setting = calc_bar_setting($effective_attack_time);
	if( $bar_setting < 0 ) $bar_setting = 0;
	if( $bar_setting > 100 ) $bar_setting = 100;

	$Init1 = calc_inits($attack_time - 1);
	$Init2 = calc_inits($attack_time);
	$Init3 = calc_inits($attack_time + 1);
			
	$inside  = "<header>::::: Nano Init Calculator - Version 1.00 :::::<end>\n\n";
	$inside .= "Results:\n";
	$inside	.= "Attack:<orange> ". $attack_time ." <end>second(s).\n";
	$inside	.= "Init Skill:<orange> ". $init_skill ."<end>\n";
	$inside	.= "Def/Agg:<orange> ". $bar_setting ."%<end>\n";
	$inside	.= "You must set your AGG bar at<orange> ". $bar_setting ."% (". round($bar_setting * 8 / 100,2) .") <end>to instacast your nano.\n\n";
	$inside	.= "NanoC. Init needed to instacast at Full Agg (100%):<orange> ". $Init1 ." <end>inits.\n";
	$inside	.= "NanoC. Init needed to instacast at Neutral (88%):<orange> ". $Init2 ." <end>inits.\n";
	$inside	.= "NanoC. Init needed to instacast at Full Def (0%):<orange> ". $Init3 ." <end>inits.";

	$windowlink = Text::make_link("::Nano Init Results::", $inside);
	bot::send($windowlink, $sendto);
}
?>