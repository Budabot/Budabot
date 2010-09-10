<?php
$info = explode(" ", $message);
list($msg, $AttTim, $RechT, $FARecharge, $FullAutoSkill) = $info;

if((!$AttTim) || (!$RechT) || (!$FARecharge) || (!$FullAutoSkill)) {
	$syntax_error = true;
} else {
	list($FACap, $FA_Skill_Cap) = cap_full_auto($AttTim, $RechT, $FARecharge);
	
	$FA_Recharge = round(($RechT * 40) + ($FARecharge / 100) - ($FullAutoSkill / 25) + round($AttTim - 1));
	if ($FA_Recharge < $FACap) {
		$FA_Recharge = $FACap;
	}
	
	$MaxBullets = 5 + floor($FullAutoSkill / 100);
	
	$inside = "<header>::::: Full Auto Calculator - Version 1.00 :::::<end>\n\n";
	$inside .= "Results:\n";
	$inside	.= "Weapon Attack: <orange>". $AttTim ."<end>s\n";
	$inside	.= "Weapon Recharge: <orange>". $RechT ."<end>s\n";
	$inside	.= "Full Auto Recharge value: <orange>". $FARecharge ."<end>\n";
	$inside	.= "FA Skill: <orange>". $FullAutoSkill ."<end>\n\n";
	$inside	.= "Your Full Auto recharge:<orange> ". $FA_Recharge ."s<end>.\n";
	$inside .= "Your Full Auto can fire a maximum of <orange>".$MaxBullets." bullets<end>.\n";
	$inside .= "Full Auto recharge always caps at <orange>".$FACap."<end>s.\n";
	$inside	.= "You will need at least <orange>".$FA_Skill_Cap."<end> Full Auto skill to cap your recharge.\n\n";
	$inside .= "From <orange>0 to 10K<end> damage, the bullet damage is unchanged.\n";
	$inside .= "From <orange>10K to 11.5K<end> damage, each bullet damage is halved.\n";
	$inside .= "From <orange>11K to 15K<end> damage, each bullet damage is halved again.\n";
	$inside .= "<orange>15K<end> is the damage cap.\n\n";

	$windowlink = bot::makeLink("::Your Full Auto Recharge Results::", $inside);
	bot::send($windowlink, $sendto);
}