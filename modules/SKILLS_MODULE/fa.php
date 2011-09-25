<?php

list($command, $AttTim, $RechT, $FARecharge, $FullAutoSkill) = explode(" ", $message);

if ((!$AttTim) || (!$RechT) || (!$FARecharge) || (!$FullAutoSkill)) {
	$syntax_error = true;
} else {
	list($FACap, $FA_Skill_Cap) = cap_full_auto($AttTim, $RechT, $FARecharge);
	
	$FA_Recharge = round(($RechT * 40) + ($FARecharge / 100) - ($FullAutoSkill / 25) + round($AttTim - 1));
	if ($FA_Recharge < $FACap) {
		$FA_Recharge = $FACap;
	}
	
	$MaxBullets = 5 + floor($FullAutoSkill / 100);
	
	$blob = "<header> :::::: Full Auto Calculator :::::: <end>\n\n";
	$blob .= "Results:\n";
	$blob .= "Weapon Attack: <orange>". $AttTim ."<end>s\n";
	$blob .= "Weapon Recharge: <orange>". $RechT ."<end>s\n";
	$blob .= "Full Auto Recharge value: <orange>". $FARecharge ."<end>\n";
	$blob .= "FA Skill: <orange>". $FullAutoSkill ."<end>\n\n";
	$blob .= "Your Full Auto recharge:<orange> ". $FA_Recharge ."s<end>.\n";
	$blob .= "Your Full Auto can fire a maximum of <orange>".$MaxBullets." bullets<end>.\n";
	$blob .= "Full Auto recharge always caps at <orange>".$FACap."<end>s.\n";
	$blob .= "You will need at least <orange>".$FA_Skill_Cap."<end> Full Auto skill to cap your recharge.\n\n";
	$blob .= "From <orange>0 to 10K<end> damage, the bullet damage is unchanged.\n";
	$blob .= "From <orange>10K to 11.5K<end> damage, each bullet damage is halved.\n";
	$blob .= "From <orange>11K to 15K<end> damage, each bullet damage is halved again.\n";
	$blob .= "<orange>15K<end> is the damage cap.\n\n";

	$msg = Text::make_blob("::Your Full Auto Recharge Results::", $blob);
	$chatBot->send($msg, $sendto);
}

?>
