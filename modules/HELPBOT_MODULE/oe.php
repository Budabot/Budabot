<?php

if (preg_match("/^oe ([0-9]+)$/i", $message, $arr)) {
    $oe = $arr[1]; 
	$oevalue = (int)round($oe / 0.8); 
	$lowoevalue = (int)round($oe * 0.8); 
	$blob = "<header> :::::: Over-equipped Calculation :::::: <end>\n\n".
		"With a skill of <highlight>${oe}<end>, you will be OE above <highlight>${oevalue}<end> requirement. " . 
		"With a requirement of <highlight>${oe}<end> skill, you can have <highlight>${lowoevalue}<end> without being OE.";
	
	$msg = "<orange>{$lowoevalue}<end> - <yellow>{$oe}<end> - <orange>{$oevalue}<end> " . Text::make_link('More info', $blob, 'blob');
    
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
