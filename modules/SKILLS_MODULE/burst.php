<?php
$info = explode(" ", $message);
list($msg, $AttTim, $RechT, $BurstDelay, $BurstSkill) = $info;

$header = "<header>::::: Burst Calculator - Version 1.00 :::::<end>\n\n";
$footer = "";

$help = $header;
$help .= "<font color=#3333CC>Burst Usage:</font>\n";
$help .= "/tell <myname> <symbol>burst [<orange>A<end>] [<orange>R<end>] [<orange>BD<end>] [<orange>BS<end>]\n";
$help .= "[<orange>A<end>] = Weapon Attack Time\n";
$help .= "[<orange>R<end>] = Weapon Recharge Time\n";
$help .= "[<orange>BD<end>] = Your Burst Delay*\n";
$help .= "[<orange>BS<end>] = Your Burst Skill\n\n";
$help .= "Example:\n";
$help .= "Your weapon has an attack time of <orange>1.2<end> seconds and a recharge time of <orange>1.5<end> seconds.\n";
$help .= "Your weapon has a Burst Delay* of <orange>1600<end>.\nYou have <orange>900<end> Burst Skill.\n";
$help .= "<a href='chatcmd:///tell <myname> <symbol>burst 1.2 1.5 1600 900'>/tell <myname> <symbol>burst 1.2 1.5 1600 900</a>\n\n";
$help .= "* Your Burst Delay value (1600) can be found on <a href='chatcmd:///start http://www.auno.org'>auno.org</a> as Burst Cycle.";
$help .= $footer;

$helplink = bot::makeLink("::How to use Burst::", $help);

if((!$AttTim) || (!$RechT) || (!$BurstDelay) || (!$BurstSkill))
	bot::send($helplink, $sendto);
else{
	$cap = round($AttTim+8,0);
	$burstrech = floor(($RechT*20) + ($BurstDelay/100) - ($BurstSkill/25) + $AttTim);
	if($burstrech <= $cap)
		$burstrech = $cap;

	$burstskillcap = floor((($RechT*20) + ($BurstDelay/100) - 8)*25);

	$inside = $header;
	$inside .= "Results:\n";
	$inside	.= "Attack: <orange>". $AttTim ." <end>second(s).\n";
	$inside	.= "Recharge: <orange>". $RechT ." <end>second(s).\n";
	$inside	.= "Burst Delay: <orange>". $BurstDelay ."<end>\n";
	$inside	.= "Burst Skill: <orange>". $BurstSkill ."<end>\n\n";
	$inside	.= "Your Burst Recharge:<orange> ". $burstrech ."<end>s\n";
	$inside .= "With your weap, your burst recharge will cap at <orange>".$cap."<end>s.\n";
	$inside	.= "You need <orange>".$burstskillcap."<end> Burst Skill to cap your recharge.";
	$inside .= $footer;

	$windowlink = bot::makeLink("::Your Burst Results::", $inside);
	bot::send($windowlink, $sendto);
}