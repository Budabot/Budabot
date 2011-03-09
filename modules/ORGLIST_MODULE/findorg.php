<?php

if (preg_match("/^findorg (.+)$/i", $message, $arr)) {
    $guild_name = str_replace("'", "''", $arr[1]);
	
    $sql = "SELECT DISTINCT guild, guild_id, CASE WHEN guild_id = '' THEN 0 ELSE 1 END AS sort FROM players WHERE guild LIKE '%{$guild_name}%' ORDER BY sort DESC, guild ASC LIMIT 30";
	$db->query($sql);
	if ($db->numrows() == 0) {
		$msg = "No matches found.";
	} else {
		$blob = "<header> :::::: Org Search Results for '{$arr[1]}' :::::: <end>\n\n";
		$data = $db->fObject('all');
		forEach ($data as $row) {
			if ($row->guild_id != '') {
				$whoisorg = Text::make_link('Whoisorg', "/tell <myname> whoisorg {$row->guild_id}", 'chatcmd');
				$orglist = Text::make_link('Orglist', "/tell <myname> orglist {$row->guild_id}", 'chatcmd');
				$orgranks = Text::make_link('Orgranks', "/tell <myname> orgranks {$row->guild_id}", 'chatcmd');
				$orgmembers = Text::make_link('Orgmembers', "/tell <myname> orgmembers {$row->guild_id}", 'chatcmd');
				$blob .= "<green>{$row->guild} ({$row->guild_id})<end> [$whoisorg] [$orglist] [$orgranks] [$orgmembers]\n";
			} else {
				$blob .= "<green>{$row->guild}<end>\n";
			}
		}
		
		$msg = Text::make_link("Org Search Results for '{$arr[1]}'", $blob, 'blob');
	}
	$chatBot->send($msg, $sendto);	
} else {
    $syntax_error = true;
}

?>
