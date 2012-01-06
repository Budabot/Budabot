<?php

if (preg_match("/^system$/i", $message, $arr)) {
	global $version;

	$sql = "SELECT count(*) AS count FROM players";
	$row = $db->queryRow($sql);
	$num_player_cache = $row->count;
	
	$num_friendlist = 0;
	forEach ($chatBot->buddyList as $key => $value) {
		if (!isset($value['name'])) {
			// skip the buddies that have been added but the server hasn't sent back an update yet
			continue;
		}

		$num_friendlist++;
	}

	$blob = "<header> :::::: System Info :::::: <end>\n\n";
	$blob .= "<highlight>Budabot:<end> $version\n";
	$blob .= "<highlight>PHP:<end> " . phpversion() . "\n";
	$blob .= "<highlight>OS:<end> " . php_uname('s') . ' ' . php_uname('r') . ' ' . php_uname('m') . "\n";
	$blob .= "<highlight>Database:<end> " . $db->get_type() . "\n\n";

	$blob .= "<highlight>SuperAdmin:<end> '{$chatBot->vars['SuperAdmin']}'\n";
	$blob .= "<highlight>Guild:<end> '<myguild>' (" . $chatBot->vars['my_guild_id'] . ")\n\n";

	$blob .= "<highlight>Current Memory Usage:<end> " . Util::bytes_convert(memory_get_usage()) . "\n";
	$blob .= "<highlight>Current Memory Usage (Real):<end> " . Util::bytes_convert(memory_get_usage(1)) . "\n";
	if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
		$blob .= "<highlight>Peak Memory Usage:<end> " . Util::bytes_convert(memory_get_peak_usage()) . "\n";
		$blob .= "<highlight>Peak Memory Usage (Real):<end> " . Util::bytes_convert(memory_get_peak_usage(1)) . "\n\n";
	}

	$date_string = Util::unixtime_to_readable(time() - $chatBot->vars['startup']);
	$blob .= "<highlight>Uptime:<end> $date_string.\n\n";

	$eventnum = 0;
	$event = Registry::getInstance('event');
	forEach ($event->events as $type => $events) {
		$eventnum += count($events);
	}
	
	$command = Registry::getInstance('command');

	$blob .= "<highlight>Number of active tell commands:<end> " . count($command->commands['msg']) . "\n";
	$blob .= "<highlight>Number of active private channel commands:<end> " . count($command->commands['priv']) . "\n";
	$blob .= "<highlight>Number of active guild channel commands:<end> " . count($command->commands['guild']) . "\n";
	$blob .= "<highlight>Number of active subcommands:<end> " . count($chatBot->subcommands) . "\n";
	$blob .= "<highlight>Number of active command aliases:<end> " . count($chatBot->cmd_aliases) . "\n";
	$blob .= "<highlight>Number of active events:<end> " . $eventnum . "\n";
	$blob .= "<highlight>Number of help commands:<end> " . count($chatBot->helpfiles) . "\n\n";

	$blob .= "<highlight>Number of characters on the friendlist:<end> $num_friendlist / " . count($chatBot->buddyList) . "\n";
	$blob .= "<highlight>Number of characters in the private channel:<end> " . count($chatBot->chatlist) . "\n";
	$blob .= "<highlight>Number of guild members:<end> " . count($chatBot->guildmembers) . "\n";
	$blob .= "<highlight>Number of character infos in cache:<end> " . $num_player_cache . "\n";
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