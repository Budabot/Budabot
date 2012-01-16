<?php

if (preg_match("/^mobloot ([0-9]+)$/i", $message, $arr)) {
	$lvl = trim($arr[1]);
	
	if ($lvl > 300 || $lvl < 1) {
		$msg = "Level entered is out of range... please enter a number between <highlight>1 and 300<end>.";
	} else {
		$high = floor($lvl * 1.25);
		$low = ceil($lvl * 0.75);
		
		$blob = "<u>Results</u>:\n";
		$blob .= "Monster level: <orange>". $lvl ."<end>\n";
		$blob .= "Loot QL range: <orange>".$low."<end> - <orange>".$high."<end>\n";
		
		$msg = Text::make_blob("Your loot QL results", $blob);
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>