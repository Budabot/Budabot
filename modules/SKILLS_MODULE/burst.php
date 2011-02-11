<?php
$info = explode(" ", $message);
list($msg, $AttTim, $RechT, $BurstDelay, $BurstSkill) = $info;

if ((!$AttTim) || (!$RechT) || (!$BurstDelay) || (!$BurstSkill)) {
	$syntax_error = true;
} else {
	list($cap, $burstskillcap) = cap_burst($AttTim, $RechT, $BurstDelay);
	
	$burstrech = floor(($RechT * 20) + ($BurstDelay / 100) - ($BurstSkill / 25) + $AttTim);
	if ($burstrech <= $cap) {
		$burstrech = $cap;
	}

	$inside = "<header>::::: Burst Calculator - Version 1.00 :::::<end>\n\n";
	$inside .= "Results:\n";
	$inside .= "Attack: <orange>". $AttTim ." <end>second(s).\n";
	$inside .= "Recharge: <orange>". $RechT ." <end>second(s).\n";
	$inside .= "Burst Delay: <orange>". $BurstDelay ."<end>\n";
	$inside .= "Burst Skill: <orange>". $BurstSkill ."<end>\n\n";
	$inside .= "Your Burst Recharge:<orange> ". $burstrech ."<end>s\n";
	$inside .= "With your weap, your burst recharge will cap at <orange>".$cap."<end>s.\n";
	$inside .= "You need <orange>".$burstskillcap."<end> Burst Skill to cap your recharge.";

	$windowlink = Text::make_link("::Your Burst Results::", $inside);
	$chatBot->send($windowlink, $sendto);
}