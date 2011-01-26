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

$info = explode(" ", $message);
list($msg, $attack_time, $init_skill) = $info;

if (!$attack_time || !$init_skill) {
	$syntax_error = true;
} else {

	$attack_time_reduction = calc_attack_time_reduction($init_skill);
	$effective_attack_time = $attack_time - $attack_time_reduction;
	bot::send($effective_attack_time, $sendto);

	$bar_setting = calc_bar_setting($effective_attack_time);
	if( $bar_setting < 0 ) $bar_setting = 0;
	if( $bar_setting > 100 ) $bar_setting = 100;

	$Initatta1 = round((((100 - 87.5) * 0.02) - $attack_time) * (-200),0);
	if($Initatta1 > 1200) { $Initatta1 = round((((((100-87.5)*0.02)-$attack_time+6)*(-600)))+1200,0); }
	$Init1 = $Initatta1;
		
	$Initatta2 = round((((87.5-87.5)*0.02)-$attack_time)*(-200),0);
	if($Initatta2 > 1200) { $Initatta2 = round((((((87.5-87.5)*0.02)-$attack_time+6)*(-600)))+1200,0); }
	$Init2 = $Initatta2;
			
	$Initatta3 = round((((0-87.5)*0.02)-$attack_time)*(-200),0);
	if($Initatta3 > 1200) { $Initatta3 = round((((((0-87.5)*0.02)-$attack_time+6)*(-600)))+1200,0); }
	$Init3 = $Initatta3;
			
	$inside  = "<header>::::: Nano Init Calculator - Version 1.00 :::::<end>\n\n";
	$inside .= "Results:\n";
	$inside	.= "Attack:<orange> ". $attack_time ." <end>second(s).\n";
	$inside	.= "Init Skill:<orange> ". $init_skill ."<end>\n";
	$inside	.= "Def/Agg:<orange> ". $bar_setting ."%<end>\n";
	$inside	.= "You must set your AGG bar at<orange> ". $bar_setting ."% (". round($bar_setting*8/100,2) .") <end>to instacast your nano.\n\n";
	$inside	.= "NanoC. Init needed to instacast at Full Agg:<orange> ". $Init1 ." <end>inits.\n";
	$inside	.= "NanoC. Init needed to instacast at neutral (88%bar):<orange> ". $Init2 ." <end>inits.\n";
	$inside	.= "NanoC. Init needed to instacast at Full Def:<orange> ". $Init3 ." <end>inits.";

	$windowlink = bot::makeLink("::Nano Init Results::", $inside);
	bot::send($windowlink, $sendto);
}
?>