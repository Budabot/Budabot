<?php

if (preg_match("/^system$/i", $message, $arr)) {
	global $version;

	$blob = "<header>::::: System Info :::::<end>\n\n";
	$blob .= "Budabot $version\n\n";
	
	$blob .= "<highlight>SuperAdmin:<end> '{$chatBot->vars['SuperAdmin']}'\n";
	$blob .= "<highlight>Guild:<end> '{$chatBot->vars['my guild']}' (" . $chatBot->vars['my guild id'] . ")\n\n";
	
	$blob .= "<highlight>Current Memory Usage:<end> " . Util::bytes_convert(memory_get_usage()) . "\n";
	$blob .= "<highlight>Current Memory Usage (Real):<end> " . Util::bytes_convert(memory_get_usage(1)) . "\n";
	$blob .= "<highlight>Peak Memory Usage:<end> " . Util::bytes_convert(memory_get_usage()) . "\n";
	$blob .= "<highlight>Peak Memory Usage (Real):<end> " . Util::bytes_convert(memory_get_peak_usage(1)) . "\n\n";
	
	$date_string = Util::unixtime_to_readable(time() - $chatBot->vars['startup']);
	$blob .= "<highlight>Uptime:<end> $date_string.\n\n";
	
	$blob .= "<highlight>Number of active tell commands:<end> " . count($chatBot->tellCmds) . "\n";
	$blob .= "<highlight>Number of active private channel commands:<end> " . count($chatBot->privCmds) . "\n";
	$blob .= "<highlight>Number of active guild channel commands:<end> " . count($chatBot->guildCmds) . "\n";
	$blob .= "<highlight>Number of active subcommands:<end> " . count($chatBot->subcommands) . "\n";
	$blob .= "<highlight>Number of active command aliases:<end> " . count($chatBot->cmd_aliases) . "\n";
	$blob .= "<highlight>Number of active events:<end> " . count($chatBot->events) . "\n";
	$blob .= "<highlight>Number of help commands:<end> " . count($chatBot->helpfiles) . "\n\n";
	
	$blob .= "<highlight>Number of characters on the friendlist:<end> " . count($chatBot->buddyList) . "\n";
	$blob .= "<highlight>Number of messages in the chat queue:<end> " . count($chatBot->chatqueue->queue) . "\n\n";
	
	$blob .= "<highlight>Public Channels:<end>\n";
	forEach ($chatBot->grp as $gid => $status) {
		$string = unpack("N", substr($gid, 1));
		$blob .= "<tab>'{$chatBot->gid[$gid]}' (" . ord(substr($gid, 0, 1)) . " " . $string[1] . ")\n";
	}

	$msg = Text::make_link('System Info', $blob, 'blob');
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>