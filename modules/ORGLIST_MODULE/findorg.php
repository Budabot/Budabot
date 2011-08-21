<?php

if (preg_match("/^findorg (.+)$/i", $message, $arr)) {
    $guild_name = str_replace("'", "''", $arr[1]);
	
    $sql = "SELECT DISTINCT guild, guild_id, CASE WHEN guild_id = '' THEN 0 ELSE 1 END AS sort FROM players WHERE guild LIKE '%{$guild_name}%' ORDER BY sort DESC, guild ASC LIMIT 30";
	$db->query($sql);
	if ($db->numrows() == 0) {
		$msg = "No matches found.";
	} else {
		$blob = "";
		$data = $db->fObject('all');
		forEach ($data as $row) {
			if ($row->guild_id != '') {
				$whoisorg = Text::make_chatcmd('Whoisorg', "/tell <myname> whoisorg {$row->guild_id}");
				$orglist = Text::make_chatcmd('Orglist', "/tell <myname> orglist {$row->guild_id}");
				$orgranks = Text::make_chatcmd('Orgranks', "/tell <myname> orgranks {$row->guild_id}");
				$orgmembers = Text::make_chatcmd('Orgmembers', "/tell <myname> orgmembers {$row->guild_id}");
				$tower_attacks = Text::make_chatcmd('Tower Attacks', "/tell <myname> attacks org {$row->guild}");
				$tower_victories = Text::make_chatcmd('Tower Victories', "/tell <myname> victory org {$row->guild}");
				$blob .= "<green>{$row->guild} ({$row->guild_id})<end> [$whoisorg] [$orglist] [$orgranks] [$orgmembers] [$tower_attacks] [$tower_victories]\n";
			} else {
				$blob .= "<green>{$row->guild}<end>\n";
			}
		}
		
		$msg = Text::make_blob("Org Search Results for '{$arr[1]}'", array("header" => "<header> :::::: Org Search Results for '{$arr[1]}' :::::: <end>\n\n", "content" => $blob));
	}
	$chatBot->send($msg, $sendto);	
} else {
    $syntax_error = true;
}

?>
