<?php

if (preg_match("/^findorg (.+) (\d)$/i", $message, $arr) || preg_match("/^findorg (.+)$/i", $message, $arr)) {
    $guild_name = $arr[1];

	$dimension = $chatBot->vars['dimension'];
	if (isset($arr[2])) {
		$dimension = $arr[2];
	}

    $sql = "SELECT DISTINCT guild, guild_id, CASE WHEN guild_id = '' THEN 0 ELSE 1 END AS sort FROM players WHERE guild LIKE ? AND dimension = ? ORDER BY sort DESC, guild ASC LIMIT 30";
	$data = $db->query($sql, '%'.$guild_name.'%', $dimension);
	if (count($data) > 0) {
		$blob = '';

		forEach ($data as $row) {
			if ($row->guild_id != '') {
				$whoisorg = Text::make_chatcmd('Whoisorg', "/tell <myname> whoisorg {$row->guild_id} $dimension");
				if ($dimension == $chatBot->vars['dimension']) {
					$orglist = Text::make_chatcmd('Orglist', "/tell <myname> orglist {$row->guild_id}");
					$orgranks = Text::make_chatcmd('Orgranks', "/tell <myname> orgranks {$row->guild_id}");
					$orgmembers = Text::make_chatcmd('Orgmembers', "/tell <myname> orgmembers {$row->guild_id}");
					$tower_attacks = Text::make_chatcmd('Tower Attacks', "/tell <myname> attacks org {$row->guild}");
					$tower_victories = Text::make_chatcmd('Tower Victories', "/tell <myname> victory org {$row->guild}");
					$blob .= "<green>{$row->guild} ({$row->guild_id})<end> [$whoisorg] [$orglist] [$orgranks] [$orgmembers] [$tower_attacks] [$tower_victories]\n";
				} else {
					$blob .= "<green>{$row->guild} ({$row->guild_id})<end> [$whoisorg]\n";
				}
			} else {
				$blob .= "<green>{$row->guild}<end>\n";
			}
		}

		$msg = Text::make_blob("Org Search Results for '{$arr[1]}'", $blob);
	} else {
		$msg = "No matches found.";
	}
	$sendto->reply($msg);
} else {
    $syntax_error = true;
}

?>
