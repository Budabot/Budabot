<?php
	// help screen
	$header = "<header>::::: Mob Loot QL Calculator - Version 1.00 :::::<end>\n\n";
	$footer = "";

	$help = $header;
	$help .= "<font color=#3333CC>Mob Loot Usage:</font>\n";
	$help .= "/tell <myname> <symbol>mobloot [<orange>lvl<end>]\n";
	$help .= "[<orange>lvl<end>] = monster level\n";
	$help .= "Example:\n";
	$help .= "You have Lvl 150 Monster.\n";
	$help .= "<a href='chatcmd:///tell <myname> <symbol>mobloot 150'>/tell <myname> <symbol>mobloot 150</a>\n\n";
	$help .= $footer;

	$helplink = bot::makeLink("::How to use mobloot::", $help);

	if (preg_match("/^mobloot ([0-9]+)$/i", $message, $arr)) {
		$lvl = trim($arr[1]);
		
		if ($lvl > 300 || $lvl < 1) {
			bot::send("Level entered is out of range... please enter a number between <highlight>1 and 300<end>.",$sendto);
		} else {
			$high = floor($lvl * 1.25); $low = ceil($lvl * 0.75);
			
			$inside = $header;
			$inside .= "<u>Results</u>:\n";
			$inside	.= "Monster level: <orange>". $lvl ."<end>\n";
			$inside .= "Loot QL range: <orange>".$low."<end> - <orange>".$high."<end>\n";
			$inside .= $footer;
			
			$windowlink = bot::makeLink("::Your loot QL results::", $inside);
			bot::send($windowlink, $sendto);
		}
	
	} else {
		bot::send($helplink, $sendto);
	}

?>