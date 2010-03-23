<?
$info = explode(" ", $message);
list($msg, $AttTim, $RechT, $InitS) = $info;

if($type == "msg")
    $sendto = $sender;
elseif($type == "priv")
	$sendto = "";
elseif($type == "guild")
	$sendto = "guild";

$header = "<header>::::: Aimed Shot Calculator - Version 1.00 :::::<end>\n\n";
$footer = "";

$help = $header;
$help .= "<font color=#3333CC>Aimed Shot Usage:</font>\n";
$help .= "/tell <myname> <symbol>as [<orange>A<end>] [<orange>R<end>] [<orange>AS<end>]\n";
$help .= "[<orange>A<end>] = Weapon Attack Time\n";
$help .= "[<orange>R<end>] = Weapon Recharge Time\n";
$help .= "[<orange>AS<end>] = Your Aimed Shot skill\n\n";
$help .= "Example:\n";
$help .= "Your weapon has a recharge time of <orange>1.5<end> seconds.\n";
$help .= "You have <orange>1200<end> Aimed Shot.\n";
$help .= "<a href='chatcmd:///tell <myname> <symbol>as 1.5 1200'>/tell <myname> <symbol>as 1.5 1200</a>\n\n";
$help .= $footer;

$helplink = bot::makeLink("::How to use Aimed Shot::", $help);

if((!$AttTim) || (!$RechT) || (!$InitS))
	bot::send($helplink, $sendto);
else{
	$cap = floor($AttTim+10);
	$ASRech	= ceil(($RechT*40) - ($InitS*3/100) + $AttTim - 1);
	if($ASRech < $cap) $ASRech = $cap; 
	$MultiP	= round($InitS/95,0);
	$ASCap = ceil(((4000 * $RechT) - 1100)/3);

	$inside = $header;
	$inside .= "Results:\n\n";
	$inside	.= "Attack: <orange>". $AttTim ." <end>second(s).\n";
	$inside	.= "Recharge: <orange>". $RechT ." <end>second(s).\n";
	$inside	.= "AS Skill: <orange>". $InitS ."<end>\n\n";
	$inside	.= "AS Multiplier:<orange> 1-". $MultiP ."x<end>\n";
	$inside	.= "AS Recharge: <orange>". $ASRech ."<end> seconds.\n";
	$inside .= "With your weap, your AS recharge will cap at <orange>".$cap."<end>s.\n";
	$inside	.= "You need <orange>".$ASCap."<end> AS skill to cap your recharge.";
	$inside .= $footer;

	$windowlink = bot::makeLink("::Your Aimed Shot Results::", $inside);
	bot::send($windowlink, $sendto);
	}