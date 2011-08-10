<?php

if (preg_match("/^system$/i", $message, $arr)) {
	global $version;
	
	$sql = "SELECT count(*) AS count FROM players";
	$db->query($sql);
	$count = $db->fObject()->count;

	$blob = "<header>::::: System Info :::::<end>\n\n";
	$blob .= "<highlight>Budabot:<end> $version\n";
	$blob .= "<highlight>PHP:<end> " . phpversion() . "\n";
	$blob .= "<highlight>OS:<end> " . php_uname('s') . ' ' . php_uname('r') . ' ' . php_uname('m') . "\n";
	$blob .= "<highlight>Database:<end> " . $db->get_type() . "\n\n";
	
	$blob .= "<highlight>SuperAdmin:<end> '{$chatBot->vars['SuperAdmin']}'\n";
	$blob .= "<highlight>Guild:<end> '<myguild>' (" . $chatBot->vars['my_guild_id'] . ")\n\n";
	
	$blob .= "<highlight>Current Memory Usage:<end> " . Util::bytes_convert(memory_get_usage()) . "\n";
	$blob .= "<highlight>Current Memory Usage (Real):<end> " . Util::bytes_convert(memory_get_usage(1)) . "\n";
	$blob .= "<highlight>Peak Memory Usage:<end> " . Util::bytes_convert(memory_get_usage()) . "\n";
	$blob .= "<highlight>Peak Memory Usage (Real):<end> " . Util::bytes_convert(memory_get_peak_usage(1)) . "\n\n";
	
	$date_string = Util::unixtime_to_readable(time() - $chatBot->vars['startup']);
	$blob .= "<highlight>Uptime:<end> $date_string.\n\n";
	
	$blob .= "<highlight>Number of active tell commands:<end> " . count($chatBot->commands['msg']) . "\n";
	$blob .= "<highlight>Number of active private channel commands:<end> " . count($chatBot->commands['priv']) . "\n";
	$blob .= "<highlight>Number of active guild channel commands:<end> " . count($chatBot->commands['guild']) . "\n";
	$blob .= "<highlight>Number of active subcommands:<end> " . count($chatBot->subcommands) . "\n";
	$blob .= "<highlight>Number of active command aliases:<end> " . count($chatBot->cmd_aliases) . "\n";
	$blob .= "<highlight>Number of active events:<end> " . count($chatBot->events) . "\n";
	$blob .= "<highlight>Number of help commands:<end> " . count($chatBot->helpfiles) . "\n\n";
	
	$blob .= "<highlight>Number of characters on the friendlist:<end> " . count($chatBot->buddyList) . "\n";
	$blob .= "<highlight>Number of characters in the private channel:<end> " . count($chatBot->chatlist) . "\n";
	$blob .= "<highlight>Number of guild members:<end> " . count($chatBot->guildmembers) . "\n";
	$blob .= "<highlight>Number of character infos in cache:<end> " . $count . "\n";
	$blob .= "<highlight>Number of messages in the chat queue:<end> " . count($chatBot->chatqueue->queue) . "\n\n";
	
	$blob .= "<highlight>Public Channels:<end>\n";
	forEach ($chatBot->grp as $gid => $status) {
		$string = unpack("N", substr($gid, 1));
		$blob .= "<tab>'{$chatBot->gid[$gid]}' (" . ord(substr($gid, 0, 1)) . " " . $string[1] . ")\n";
	}

	$msg = Text::make_blob('System Info', $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>