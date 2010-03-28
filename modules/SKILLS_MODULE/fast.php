<?
/* $info = explode(" ", $message);
list($msg, $AttTim, $fastSkill) = $info; */

if($type == "msg")
    $sendto = $sender;
elseif($type == "priv")
	$sendto = "";
elseif($type == "guild")
	$sendto = "guild";

$header = "\n<header>::::: Fast Attack Calculator - Version 1.00 :::::<end>\n\n";
$footer = "";

$help = $header;
$help .= "<font color=#3333CC>fast Usage:</font>\n";
$help .= "/tell <myname> <symbol>fast [<orange>A<end>] [<orange>F<end>]\n";
$help .= "[<orange>A<end>] = Weapon Attack Time\n";
$help .= "[<orange>F<end>] = Your Fast Attack Skill\n\n";
$help .= "Example:\n";
$help .= "Your weapon has an attack time of <orange>1.2<end> seconds.\n";
$help .= "You have <orange>900<end> fast attack Skill.\n";
$help .= "<a href='chatcmd:///tell <myname> <symbol>fast 1.2 900'>/tell <myname> <symbol>fast 1.2 900</a>\n\n";
$help .= $footer;

$helplink = bot::makeLink("::How to use fast attack::", $help);

/* if((!$AttTim) || (!$fastSkill))
	bot::send($helplink, $sendto); 
else{*/
//eregi("^fast ([0-9]+) ([0-9]+)", $message, $arr)
if (eregi("^fast ([0-9]*\.?[0-9]+) ([0-9]+)$", $message, $arr)) {
	$AttTim = trim($arr[1]);
	$fastSkill = trim($arr[2]);
	
	$fasthardcap = 4+$AttTim;

	$fastrech =  round(($AttTim*16)-($fastSkill/100));

	if($fastrech < $fasthardcap)
		$fastrech = $fasthardcap;

	$fastskillcap = (($AttTim*16)-$fasthardcap)*100;

	$inside = $header;
	$inside .= "Results:\n";
	$inside	.= "Attack: <orange>". $AttTim ." <end>second(s).\n";
	$inside	.= "Fast Atk Skill: <orange>". $fastSkill ."<end>\n";
	$inside	.= "Fast Atk Recharge:<orange> ". $fastrech ."<end>s\n";
	$inside	.= "You need <orange>".$fastskillcap."<end> Fast Atk Skill to cap your fast attack at: <orange>".$fasthardcap."<end>s";
	$inside .= $footer;

	$windowlink = bot::makeLink("::Your Fast Attack Results::", $inside);
	bot::send($windowlink, $sendto);
} else bot::send($helplink, $sendto); 