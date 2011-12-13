<?php

if (preg_match("/^remalias ([a-z0-9]+)/i", $message, $arr)) {
	$alias = strtolower($arr[1]);
	
	$row = CommandAlias::get($alias);
	if ($row === null || $row->status != 1) {
		$msg = "Could not find alias <highlight>{$alias}<end>!";
	} else {
		$row->status = 0;
		CommandAlias::update($row);
		CommandAlias::deactivate($alias);
		
		$msg = "Alias <highlight>{$alias}<end> removed successfully.";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>