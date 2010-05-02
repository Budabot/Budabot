<?php
	// help screen
	$header = "<header>::::: Loot QL Calculator - Version 1.00 :::::<end>\n\n";
	$footer = "";

	$help = $header;
	$help .= "<font color=#3333CC>Loot Usage:</font>\n";
	$help .= "/tell <myname> <symbol>loot [<orange>lvl<end>]\n";
	$help .= "[<orange>lvl<end>] = monster level\n";
	$help .= "Example:\n";
	$help .= "You have Lvl 150 Monster.\n";
	$help .= "<a href='chatcmd:///tell <myname> <symbol>loot 150'>/tell <myname> <symbol>loot 150</a>\n\n";
	$help .= $footer;

	$helplink = bot::makeLink("::How to use loot::", $help);

	// sendto
	if($type == "msg")
		$sendto = $sender;
	elseif($type == "priv")
		$sendto = "";
	elseif($type == "guild")
		$sendto = "guild";

	if(eregi("^loot ([0-9]+)$", $message, $arr)) {
		$lvl = trim($arr[1]);
		
		if ($lvl > 300) {
			bot::send("Level entered is out of range... please enter a number between <highlight>1 and 300<end>.",$sendto);
			return;
		}
		$high = floor($lvl * 1.25); $low = ceil($lvl * 0.75);
		
		$inside = $header;
		$inside .= "<u>Results</u>:\n";
		$inside	.= "Monster level: <orange>". $lvl ."<end>\n";
		$inside .= "Loot QL range: <orange>".$low."<end> - <orange>".$high."<end>\n";
		$inside .= $footer;
		
		$windowlink = bot::makeLink("::Your loot QL results::", $inside);
		bot::send($windowlink, $sendto);
	
	
	} else bot::send($helplink, $sendto);

?>