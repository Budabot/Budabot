<?php

if (preg_match("/^fling ([0-9]*\.?[0-9]+) ([0-9]+)$/i", $message, $arr)) {
	$AttTim = trim($arr[1]);
	$FlingSkill = trim($arr[2]);
	
	list($flinghardcap, $flingskillcap) = cap_fling_shot($AttTim);

	$flingrech =  round(($AttTim * 16) - ($FlingSkill / 100));

	if ($flingrech < $flinghardcap) {
		$flingrech = $flinghardcap;
	}

	$inside = "<header>::::: Fling Calculator - Version 1.00 :::::<end>\n\n";
	$inside .= "Results:\n";
	$inside	.= "Attack: <orange>". $AttTim ." <end>second(s).\n";
	$inside	.= "Fling Skill: <orange>". $FlingSkill ."<end>\n";
	$inside	.= "Fling Recharge:<orange> ". $flingrech ."<end>s\n";
	$inside	.= "You need <orange>".$flingskillcap."<end> Fling Skill to cap your fling at: <orange>".$flinghardcap."<end>s";

	$windowlink = Text::make_blob("::Your Fling Results::", $inside);
	$chatBot->send($windowlink, $sendto);
} else {
	$syntax_error = true;
}