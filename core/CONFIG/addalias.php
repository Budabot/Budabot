<?php

if (preg_match("/^addalias ([a-z0-9]+) (.+)/i", $message, $arr)) {
	$alias = strtolower($arr[1]);
	$cmd = strtolower($arr[2]);
	$commandAlias = Registry::getInstance('commandAlias');
	
	$alias_obj = new stdClass;
	$alias_obj->module = '';
	$alias_obj->cmd = $cmd;
	$alias_obj->alias = $alias;
	$alias_obj->status = 1;

	$commands = Registry::getInstance('command')->get($alias);
	$enabled = false;
	forEach ($commands as $command) {
		if ($command->status == '1') {
			$enabled = true;
			break;
		}
	}
	$row = $commandAlias->get($alias);
	if ($enabled) {
		$msg = "Cannot add alias <highlight>{$alias}<end> since there is already an active command with that name.";
	} else if ($row === null) {
		$commandAlias->add($alias_obj);
		$commandAlias->activate($cmd, $alias);
		$msg = "Alias <highlight>{$alias}<end> for command <highlight>{$cmd}<end> added successfully.";
	} else if ($row->status == 0 || ($row->status == 1 && $row->cmd == $cmd)) {
		$commandAlias->update($alias_obj);
		$commandAlias->activate($cmd, $alias);
		$msg = "Alias <highlight>{$alias}<end> for command <highlight>{$cmd}<end> added successfully.";
	} else if ($row->status == 1 && $row->cmd != $cmd) {
		$msg = "Cannot add alias <highlight>{$alias}<end> since an alias with that name already exists.";
	}
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>