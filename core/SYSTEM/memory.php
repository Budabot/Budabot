<?php

if (preg_match("/^memory$/i", $message, $arr)) {
	$blob = "<header>::::: Memory Usage :::::<end>\n\n";
	$blob .= "Current Memory Usage: " . Util::bytes_convert(memory_get_usage()) . "\n";
	$blob .= "Current Memory Usage (Real): " . Util::bytes_convert(memory_get_usage(1)) . "\n";
	$blob .= "Peak Memory Usage: " . Util::bytes_convert(memory_get_usage()) . "\n";
	$blob .= "Peak Memory Usage (Real): " . Util::bytes_convert(memory_get_peak_usage(1)) . "\n";
	$msg = bot::makeLink('Memory Usage', $blob);	
	bot::send($msg, $sendto);
}

?>