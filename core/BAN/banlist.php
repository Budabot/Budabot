<?php

$banInstance = Registry::getInstance('ban');

if (preg_match("/^banlist$/i", $message)) {
  	if (count($banInstance = Registry::getInstance('ban')->getBanlist()) == 0) {
	    $chatBot->send("No one is currently banned from this bot.", $sendto);
	    return;
	}
	
	$list .= "<header> :::::: Banlist :::::: <end>\n\n";
	forEach ($banInstance = Registry::getInstance('ban')->getBanlist() as $ban) {
		$list .= "<highlight>Name:<end> {$ban->name}\n";
		$list .= "<highlight><tab>Date:<end> ".date(DATE_RFC850, $ban->time)."\n";
		$list .= "<highlight><tab>By:<end> {$ban->admin}\n";
		if ($ban->banend != null) {
			$list .= "<highlight><tab>Ban ends:<end> ". Util::unixtime_to_readable($ban->banend - time(), false)."\n";
		} else {
			$list .= "<highlight><tab>Ban ends:<end> Never.\n";
		}
		
		if ($ban->reason != '') {
			$list .= "<highlight><tab>Reason:<end> {$ban->reason}\n";
		}
		$list .= "\n";
	}
	$link = Text::make_blob('Banlist', $list);
	$chatBot->send($link, $sendto);
} else {
	$syntax_error = true;
}

?>