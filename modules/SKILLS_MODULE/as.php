<?
$info = explode(" ", $message);
list($msg, $AttTim, $RechT, $InitS) = $info;

if($type == "msg")
    $sendto = $sender;
elseif($type == "priv")
	$sendto = "";
elseif($type == "guild")
	$sendto = "guild";

$header = "\n<header>::::: Aimed Shot Calculator - Version 1.00 :::::<end>\n\n";
$footer = "";

$help = $header;
$help .= "<font color=#3333CC>Aimed Shot Usage:</font>\n";
$help .= "/tell <myname> <symbol>as [<orange>A<end>] [<orange>R<end>] [<orange>AS<end>]\n";
$help .= "[<orange>A<end>] = Weapon Attack Time\n";
$help .= "[<orange>R<end>] = Weapon Recharge Time\n";
$help .= "[<orange>AS<end>] = Your Aimed Shot skill\n\n";
$help .= "Example:\n";
$help .= "Your weapon has an attack time of <orange>1.2<end> seconds and a recharge time\n";
$help .= "of <orange>1.5<end> seconds.  You have <orange>1200<end> Aimed Shot.\n";
$help .= "<a href='chatcmd:///tell <myname> <symbol>as 1.2 1.5 1200'>/tell <myname> <symbol>as 1.2 1.5 1200</a>\n\n";
$help .= $footer;

$helplink = bot::makeLink("::How to use Aimed Shot::", $help);

if((!$AttTim) || (!$RechT) || (!$InitS))
	bot::send($helplink, $sendto);
else{
	$capped = round($RechT+10,0);
	$ASRech	= round(($RechT*40)-($InitS*3)/100, 0);
	if($ASRech < $capped) { $ASRech = round($RechT+10,0); }
	$MultiP	= round($InitS/95,0);
	$ASCap = (-10+(39*$RechT))*100/3;

	$inside = $header;
	$inside .= "Results:\n";
	$inside	.= "Attack: <orange>". $AttTim ." <end>second(s).\n";
	$inside	.= "Recharge: <orange>". $RechT ." <end>second(s).\n";
	$inside	.= "AS Skill: <orange>". $InitS ."<end>\n";
	$inside	.= "AS Multiplier:<orange> 1-". $MultiP ."x<end>\n";
	$inside	.= "AS Recharge: <orange>". $ASRech ."<end>\n";
	$inside	.= "AS Skill needed to cap: <orange>". round($ASCap,0) ." <end>for <orange>". round($RechT+10,0) ."<end>s.";
	$inside .= $footer;

	$windowlink = bot::makeLink("::Your Aimed Shot Results::", $inside);
	bot::send($windowlink, $sendto);
	}