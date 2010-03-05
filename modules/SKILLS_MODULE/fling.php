<?
$info = explode(" ", $message);
list($msg, $AttTim, $FlingSkill) = $info;

if($type == "msg")
    $sendto = $sender;
elseif($type == "priv")
	$sendto = "";
elseif($type == "guild")
	$sendto = "guild";

$header = "\n<header>::::: Fling Calculator - Version 1.00 :::::<end>\n\n";
$footer = "";

$help = $header;
$help .= "<font color=#3333CC>Fling Usage:</font>\n";
$help .= "/tell <myname> <symbol>fling [<orange>A<end>] [<orange>FS<end>]\n";
$help .= "[<orange>A<end>] = Weapon Attack Time\n";
$help .= "[<orange>FS<end>] = Your Fling Skill\n\n";
$help .= "Example:\n";
$help .= "Your weapon has an attack time of <orange>1.2<end> seconds.\n";
$help .= "You have <orange>900<end> Fling Skill.\n";
$help .= "<a href='chatcmd:///tell <myname> <symbol>fling 1.2 900'>/tell <myname> <symbol>burst 1.2 900</a>\n\n";
$help .= $footer;

$helplink = bot::makeLink("::How to use Fling::", $help);

if((!$AttTim) || (!$FlingSkill))
	bot::send($helplink, $sendto);
else{
	$flinghardcap = 4+$AttTim;

	$flingrech =  round(($AttTim*16)-($FlingSkill/100));

	if($flingrech < $flinghardcap)
		$flingrech = $flinghardcap;

	$flingskillcap = (($AttTim*16)-$flinghardcap)*100;

	$inside = $header;
	$inside .= "Results:\n";
	$inside	.= "Attack: <orange>". $AttTim ." <end>second(s).\n";
	$inside	.= "Fling Skill: <orange>". $FlingSkill ."<end>\n";
	$inside	.= "Fling Recharge:<orange> ". $flingrech ."<end>s\n";
	$inside	.= "You need <orange>".$flingskillcap."<end> Fling Skill to cap your burst at: <orange>".$flinghardcap."<end>s";
	$inside .= $footer;

	$windowlink = bot::makeLink("::Your Fling Results::", $inside);
	bot::send($windowlink, $sendto);
	}