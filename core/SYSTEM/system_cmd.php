<?php

$event = Registry::getInstance('event');
$command = Registry::getInstance('command');
$subcommand = Registry::getInstance('subcommand');
$commandAlias = Registry::getInstance('commandAlias');
$help = Registry::getInstance('help');

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

	$blob = "Name: <highlight><myname><end>\n";
	$blob .= "Budabot: <highlight>$version<end>\n";
	$blob .= "PHP: <highlight>" . phpversion() . "<end>\n";
	$blob .= "OS: <highlight>" . php_uname('s') . ' ' . php_uname('r') . ' ' . php_uname('m') . "<end>\n";
	$blob .= "Database: <highlight>" . $db->get_type() . "<end>\n\n";

	$blob .= "SuperAdmin: <highlight>'{$chatBot->vars['SuperAdmin']}'<end>\n";
	$blob .= "Guild: <highlight>'<myguild>' (" . $chatBot->vars['my_guild_id'] . ")<end>\n\n";

	$blob .= "Current Memory Usage: <highlight>" . Util::bytes_convert(memory_get_usage()) . "<end>\n";
	$blob .= "Current Memory Usage (Real): <highlight>" . Util::bytes_convert(memory_get_usage(1)) . "<end>\n";
	if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
		$blob .= "Peak Memory Usage: <highlight>" . Util::bytes_convert(memory_get_peak_usage()) . "<end>\n";
		$blob .= "Peak Memory Usage (Real): <highlight>" . Util::bytes_convert(memory_get_peak_usage(1)) . "<end>\n\n";
	}

	$date_string = Util::unixtime_to_readable(time() - $chatBot->vars['startup']);
	$blob .= "Uptime: <highlight>$date_string<end>\n\n";

	$eventnum = 0;
	forEach ($event->events as $type => $events) {
		$eventnum += count($events);
	}

	$blob .= "Number of active tell commands: <highlight>" . count($command->commands['msg']) . "<end>\n";
	$blob .= "Number of active private channel commands: <highlight>" . count($command->commands['priv']) . "<end>\n";
	$blob .= "Number of active guild channel commands: <highlight>" . count($command->commands['guild']) . "<end>\n";
	$blob .= "Number of active subcommands: <highlight>" . count($subcommand->subcommands) . "<end>\n";
	$blob .= "Number of active command aliases: <highlight>" . count($commandAlias->cmd_aliases) . "<end>\n";
	$blob .= "Number of active events: <highlight>" . $eventnum . "<end>\n";
	$blob .= "Number of help commands: <highlight>" . count($help->helpfiles) . "<end>\n\n";

	$blob .= "Number of characters on the friendlist: <highlight>$num_friendlist / " . count($chatBot->buddyList) . "<end>\n";
	$blob .= "Number of characters in the private channel: <highlight>" . count($chatBot->chatlist) . "<end>\n";
	$blob .= "Number of guild members: <highlight>" . count($chatBot->guildmembers) . "<end>\n";
	$blob .= "Number of character infos in cache: <highlight>" . $num_player_cache . "<end>\n";
	$blob .= "Number of messages in the chat queue: <highlight>" . count($chatBot->chatqueue->queue) . "<end>\n\n";

	$blob .= "Public Channels:\n";
	forEach ($chatBot->grp as $gid => $status) {
		$string = unpack("N", substr($gid, 1));
		$blob .= "<tab><highlight>'{$chatBot->gid[$gid]}' (" . ord(substr($gid, 0, 1)) . " " . $string[1] . ")<end>\n";
	}

	$msg = Text::make_blob('System Info', $blob);
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>