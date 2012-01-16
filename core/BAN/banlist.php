<?php

$banlist = Registry::getInstance('ban')->getBanlist();

if (preg_match("/^banlist$/i", $message)) {
  	if (count($banlist) == 0) {
	    $sendto->reply("No one is currently banned from this bot.");
	    return;
	}
	
	$blob = '';
	forEach ($banlist as $ban) {
		$blob .= "<highlight>Name:<end> {$ban->name}\n";
		$blob .= "<highlight><tab>Date:<end> ".date(Util::DATETIME, $ban->time)."\n";
		$blob .= "<highlight><tab>By:<end> {$ban->admin}\n";
		if ($ban->banend != null) {
			$blob .= "<highlight><tab>Ban ends:<end> ". Util::unixtime_to_readable($ban->banend - time(), false)."\n";
		} else {
			$blob .= "<highlight><tab>Ban ends:<end> Never.\n";
		}
		
		if ($ban->reason != '') {
			$blob .= "<highlight><tab>Reason:<end> {$ban->reason}\n";
		}
		$blob .= "\n";
	}
	$link = Text::make_blob('Banlist', $blob);
	$sendto->reply($link);
} else {
	$syntax_error = true;
}

?>