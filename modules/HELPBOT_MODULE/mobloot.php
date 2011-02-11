<?php

if (preg_match("/^mobloot ([0-9]+)$/i", $message, $arr)) {
	$lvl = trim($arr[1]);
	
	if ($lvl > 300 || $lvl < 1) {
		bot::send("Level entered is out of range... please enter a number between <highlight>1 and 300<end>.",$sendto);
	} else {
		$high = floor($lvl * 1.25); $low = ceil($lvl * 0.75);
		
		$inside = "<header>::::: Mob Loot QL Calculator :::::<end>\n\n";
		$inside .= "<u>Results</u>:\n";
		$inside	.= "Monster level: <orange>". $lvl ."<end>\n";
		$inside .= "Loot QL range: <orange>".$low."<end> - <orange>".$high."<end>\n";
		
		$windowlink = Text::make_link("::Your loot QL results::", $inside);
		bot::send($windowlink, $sendto);
	}

} else {
	$syntax_error = true;
}

?>