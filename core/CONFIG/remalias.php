<?php

if (preg_match("/^remalias ([a-z0-9]+) (.+)/i", $message, $arr)) {
	$alias = strtolower($arr[1]);
	$cmd = strtolower($arr[2]);
	
	$row = CommandAlias::get($alias);
	if ($row === null || $row->status != 1) {
		$msg = "Could not find alias <highlight>{$alias}<end>!";
	} else if ($row->cmd != $cmd) {
		$msg = "Alias <highlight>{$alias}<end> does not exist for command <highlight>{$cmd}<end>!";
	} else {
		$alias_obj = new stdClass;
		$alias_obj->module = '';
		$alias_obj->cmd = $cmd;
		$alias_obj->alias = $alias;
		$alias_obj->status = 0;
		CommandAlias::update($alias_obj);
		CommandAlias::deactivate($cmd, $alias);
		
		$msg = "Alias <highlight>{$alias}<end> for command <highlight>{$cmd}<end> removed successfully.";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>