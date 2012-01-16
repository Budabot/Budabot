<?php

if (preg_match("/^burst ([0-9]*\\.?[0-9]+) ([0-9]*\\.?[0-9]+) (\\d+) (\\d+)$/i", $message, $arr)) {
	$AttTim = $arr[1];
	$RechT = $arr[2];
	$BurstDelay = $arr[3];
	$BurstSkill = $arr[4];

	list($cap, $burstskillcap) = cap_burst($AttTim, $RechT, $BurstDelay);
	
	$burstrech = floor(($RechT * 20) + ($BurstDelay / 100) - ($BurstSkill / 25) + $AttTim);
	if ($burstrech <= $cap) {
		$burstrech = $cap;
	}

	$blob = "Results:\n";
	$blob .= "Attack: <orange>". $AttTim ." <end>second(s).\n";
	$blob .= "Recharge: <orange>". $RechT ." <end>second(s).\n";
	$blob .= "Burst Delay: <orange>". $BurstDelay ."<end>\n";
	$blob .= "Burst Skill: <orange>". $BurstSkill ."<end>\n\n";
	$blob .= "Your Burst Recharge:<orange> ". $burstrech ."<end>s\n";
	$blob .= "With your weap, your burst recharge will cap at <orange>".$cap."<end>s.\n";
	$blob .= "You need <orange>".$burstskillcap."<end> Burst Skill to cap your recharge.";

	$msg = Text::make_blob("Burst Results", $blob);
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
