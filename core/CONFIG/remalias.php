<?php

if (preg_match("/^remalias ([a-z0-9]+)/i", $message, $arr)) {
	$alias = strtolower($arr[1]);
	$commandAlias = Registry::getInstance('commandAlias');
	
	$row = $commandAlias->get($alias);
	if ($row === null || $row->status != 1) {
		$msg = "Could not find alias <highlight>{$alias}<end>!";
	} else {
		$row->status = 0;
		$commandAlias->update($row);
		$commandAlias->deactivate($alias);
		
		$msg = "Alias <highlight>{$alias}<end> removed successfully.";
	}
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>