<?php

if (preg_match("/^addalias ([a-z0-9]+) (.+)/i", $message, $arr)) {
	$alias = strtolower($arr[1]);
	$cmd = strtolower($arr[2]);
	
	$alias_obj = new stdClass;
	$alias_obj->module = '';
	$alias_obj->cmd = $cmd;
	$alias_obj->alias = $alias;
	$alias_obj->status = 1;

	$commands = Command::get($alias);
	$enabled = false;
	forEach ($commands as $command) {
		if ($command->status == '1') {
			$enabled = true;
			break;
		}
	}
	$row = CommandAlias::get($alias);
	if ($enabled) {
		$msg = "Cannot add alias <highlight>{$alias}<end> since there is already an active command with that name.";
	} else if ($row === null) {
		CommandAlias::add($alias_obj);
		CommandAlias::activate($cmd, $alias);
		$msg = "Alias <highlight>{$alias}<end> for command <highlight>{$cmd}<end> added successfully.";
	} else if ($row->status == 0 || ($row->status == 1 && $row->cmd == $cmd)) {
		CommandAlias::update($alias_obj);
		CommandAlias::activate($cmd, $alias);
		$msg = "Alias <highlight>{$alias}<end> for command <highlight>{$cmd}<end> added successfully.";
	} else if ($row->status == 1 && $row->cmd != $cmd) {
		$msg = "Cannot add alias <highlight>{$alias}<end> since an alias with that name already exists.";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>