<?php
$info = explode(" ", $message);
list($msg, $AttTim, $RechT, $FARecharge, $FullAutoSkill) = $info;

$header = "<header>::::: Full Auto Calculator - Version 1.00 :::::<end>\n\n";
$footer = "";

$help = $header;
$help .= "<font color=#3333CC>Full Auto Recharge Usage:</font>\n";
$help .= "/tell <myname> <symbol>fa [<orange>A<end>] [<orange>R<end>] [<orange>FA Rech<end>] [<orange>FA Skill<end>]\n";
$help .= "[<orange>A<end>] = Weapon Attack Time\n";
$help .= "[<orange>R<end>] = Weapon Recharge Time\n";
$help .= "[<orange>FA Rech<end>] = Weapon Full Auto recharge value*\n";
$help .= "[<orange>FA Skill<end>] = Your Full Auto skill\n\n";
$help .= "Example:\n";
$help .= "Your weapon has an attack and recharge time of <orange>1<end> second and a \n";
$help .= "Full Auto recharge value* of <orange>5000<end>.  You have <orange>1200<end> Full Auto skill.\n";
$help .= "<a href='chatcmd:///tell <myname> <symbol>fa 1 1 5000 1200'>/tell <myname> <symbol>fa 1 1 5000 1200</a>\n\n";
$help .= "* Your Full Auto recharge value (5000) can be found on <a href='chatcmd:///start http://www.auno.org'>auno.org</a> as FullAuto Cycle and <a href='chatcmd:///start http://aomainframe.net'>aomainframe.net</a> as FullAutoRecharge.";
$help .= $footer;

$helplink = $this->makeLink("::How to use Full Auto::", $help);

if((!$AttTim) || (!$RechT) || (!$FARecharge) || (!$FullAutoSkill)) {
	$this->send($helplink, $sendto);
} else {
	$FACap = floor(10+$AttTim);

	$FA_Recharge = ceil(($RechT*40)+($FARecharge/100)-($FullAutoSkill/25) + $AttTim - 1);
	if ($FA_Recharge<$FACap) {
		$FA_Recharge = $FACap;
	}
	$FA_Skill_Cap = ceil((40*$RechT + $FARecharge/100 - 11))*25;
	
	$MaxBullets = 5 + floor($FullAutoSkill/100);
	
	$inside = $header;
	$inside .= "Results:\n";
	$inside	.= "Weapon Attack: <orange>". $AttTim ."<end>s\n";
	$inside	.= "Weapon Recharge: <orange>". $RechT ."<end>s\n";
	$inside	.= "Full Auto Recharge value: <orange>". $FARecharge ."<end>\n";
	$inside	.= "FA Skill: <orange>". $FullAutoSkill ."<end>\n\n";
	$inside	.= "Your Full Auto recharge:<orange> ". $FA_Recharge ."s<end>.\n";
	$inside .= "Your Full Auto can fire a maximum of <orange>".$MaxBullets." bullets<end>.\n";
	$inside .= "With your weap, your Full Auto recharge will cap at <orange>".$FACap."<end>s.\n";
	$inside	.= "You will need at least <orange>".$FA_Skill_Cap."<end> Full Auto skill to cap your recharge.\n\n";
	$inside .= "From <orange>0 to 10K<end> damage, the bullet damage is unchanged.\n";
	$inside .= "From <orange>10K to 11.5K<end> damage, each bullet damage is halved.\n";
	$inside .= "From <orange>11K to 15K<end> damage, each bullet damage is halved again.\n";
	$inside .= "<orange>15K<end> is the damage cap.\n\n";
	$inside .= $footer;

	$windowlink = $this->makeLink("::Your Full Auto Recharge Results::", $inside);
	$this->send($windowlink, $sendto);
}