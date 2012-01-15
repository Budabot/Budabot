<?php

if (preg_match("/^fling ([0-9]*\\.?[0-9]+) ([0-9]+)$/i", $message, $arr)) {
	$AttTim = $arr[1];
	$FlingSkill = $arr[2];
	
	list($flinghardcap, $flingskillcap) = cap_fling_shot($AttTim);

	$flingrech =  round(($AttTim * 16) - ($FlingSkill / 100));

	if ($flingrech < $flinghardcap) {
		$flingrech = $flinghardcap;
	}

	$blob = "Results:\n";
	$blob .= "Attack: <orange>{$AttTim}<end> second(s).\n";
	$blob .= "Fling Skill: <orange>{$FlingSkill}<end>\n";
	$blob .= "Fling Recharge: <orange>{$flingrech}<end> second(s)\n";
	$blob .= "You need <orange>{$flingskillcap}<end> Fling Skill to cap your fling at: <orange>{$flinghardcap}<end> second(s).";

	$msg = Text::make_blob("Fling Results", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
