<?
$info = explode(" ", $message);
list($msg, $AttTim, $RechT, $FARecharge, $FullAutoSkill) = $info;

if($type == "msg")
    $sendto = $sender;
elseif($type == "priv")
	$sendto = "";
elseif($type == "guild")
	$sendto = "guild";

$header = "\n<header>::::: Full Auto Calculator - Version 1.00 :::::<end>\n\n";
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
$help .= "* Your Full Auto recharge value (5000) can be found on <a href='chatcmd:///start http://www.auno.org'>auno.org</a> as FullAuto Cycle.";
$help .= $footer;

$helplink = bot::makeLink("::How to use Full Auto::", $help);

if((!$AttTim) || (!$RechT) || (!$FARecharge) || (!$FullAutoSkill))
	bot::send($helplink, $sendto);
else{
	$FACap = round(10+$AttTim);
	$weaponrech = floor($RechT);

	$rechval = round($weaponrech*40,0);
	$FADelay = round($FARecharge/100,0);
	$FA_Skill = round($FullAutoSkill/25);

	$FA_Recharge = $rechval+($FADelay-$FA_Skill);
	$FA_Skill_Cap = ($FACap+(40*$weaponrech) + ($FADelay))*25;

	$inside = $header;
	$inside .= "Results:\n";
	$inside	.= "Weapon Attack: <orange>". $AttTim ."<end>s\n";
	$inside	.= "Weapon Recharge: <orange>". $RechT ."<end>s\n";
	$inside	.= "Full Auto Recharge value: <orange>". $FARecharge ."<end>\n";
	$inside	.= "FA Skill: <orange>". $FullAutoSkill ."<end>\n";
	$inside	.= "Your Full Auto recharge:<orange> ". $FA_Recharge ."s<end>\n";
	$inside	.= "You will need <orange>".$FA_Skill_Cap."<end> Full Auto skill to cap your Full Auto at <orange>".$FACap."<end>s\n";
	$inside .= $footer;

	$windowlink = bot::makeLink("::Your Full Auto Recharge Results::", $inside);
	bot::send($windowlink, $sendto);
	}