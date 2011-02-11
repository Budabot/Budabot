<?php
$info = explode(" ", $message);
list($msg, $AttTim, $RechT, $InitS) = $info;

if ((!$AttTim) || (!$RechT) || (!$InitS)) {
	$syntax_error = true;
} else {
	list($cap, $ASCap) = cap_aimed_shot($AttTim, $RechT);
	
	$ASRech	= ceil(($RechT * 40) - ($InitS * 3 / 100) + $AttTim - 1);
	if ($ASRech < $cap) {
		$ASRech = $cap;
	}
	$MultiP	= round($InitS / 95,0);

	$inside = "<header>::::: Aimed Shot Calculator - Version 1.00 :::::<end>\n\n";
	$inside .= "Results:\n\n";
	$inside	.= "Attack: <orange>". $AttTim ." <end>second(s).\n";
	$inside	.= "Recharge: <orange>". $RechT ." <end>second(s).\n";
	$inside	.= "AS Skill: <orange>". $InitS ."<end>\n\n";
	$inside	.= "AS Multiplier:<orange> 1-". $MultiP ."x<end>\n";
	$inside	.= "AS Recharge: <orange>". $ASRech ."<end> seconds.\n";
	$inside .= "With your weap, your AS recharge will cap at <orange>".$cap."<end>s.\n";
	$inside	.= "You need <orange>".$ASCap."<end> AS skill to cap your recharge.";

	$windowlink = Text::make_link("::Your Aimed Shot Results::", $inside);
	bot::send($windowlink, $sendto);
}