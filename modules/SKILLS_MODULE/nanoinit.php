<?php

if (preg_match("/^nanoinit ([0-9]*\\.?[0-9]+) (\\d+)$/i", $message, $arr)) {
	$attack_time = $arr[1];
	$init_skill = $arr[2];

	$attack_time_reduction = calc_attack_time_reduction($init_skill);
	$effective_attack_time = $attack_time - $attack_time_reduction;

	$bar_setting = calc_bar_setting($effective_attack_time);
	if ($bar_setting < 0) {
		$bar_setting = 0;
	}
	if ($bar_setting > 100) {
		$bar_setting = 100;
	}

	$Init1 = calc_inits($attack_time - 1);
	$Init2 = calc_inits($attack_time);
	$Init3 = calc_inits($attack_time + 1);
			
	$blob = "<header> :::::: Nano Init Calculator :::::: <end>\n\n";
	$blob .= "Results:\n";
	$blob .= "Attack: <orange>". $attack_time ." <end>second(s).\n";
	$blob .= "Init Skill: <orange>". $init_skill ."<end>\n";
	$blob .= "Def/Agg: <orange>". $bar_setting ."%<end>\n";
	$blob .= "You must set your AGG bar at <orange>". $bar_setting ."% (". round($bar_setting * 8 / 100,2) .") <end>to instacast your nano.\n\n";
	$blob .= "NanoC. Init needed to instacast at Full Agg (100%):<orange> ". $Init1 ." <end>inits.\n";
	$blob .= "NanoC. Init needed to instacast at Neutral (88%):<orange> ". $Init2 ." <end>inits.\n";
	$blob .= "NanoC. Init needed to instacast at Full Def (0%):<orange> ". $Init3 ." <end>inits.";

	$msg = Text::make_blob("::Nano Init Results::", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
