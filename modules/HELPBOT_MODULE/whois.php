<?php


if (preg_match("/^whois (.+)$/i", $message, $arr)) {
    $uid = $chatBot->get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    if ($uid) {
        $whois = Player::get_by_name($arr[1]);
        if ($whois === null) {
        	$msg = "Could not find character info for {$name}.";
        } else {
	        $msg = Player::get_info($whois);

	        $list = "<header> :::::: Detailed info for {$name} :::::: <end>\n\n";
	        $list .= "Name: <highlight>{$whois->firstname} \"{$name}\" {$whois->lastname}<end>\n";
			if ($whois->guild) {
				$list .= "Guild: <highlight>{$whois->guild} ({$whois->guild_id})<end>\n";
				$list .= "Guild Rank: <highlight>{$whois->guild_rank} ({$whois->guild_rank_id})<end>\n";
			}
			$list .= "Breed: <highlight>{$whois->breed}<end>\n";
			$list .= "Gender: <highlight>{$whois->gender}<end>\n";
			$list .= "Profession: <highlight>{$whois->profession} ({$whois->prof_title})<end>\n";
			$list .= "Level: <highlight>{$whois->level}<end>\n";
			$list .= "AI Level: <highlight>{$whois->ai_level} ({$whois->ai_rank})<end>\n";
			$list .= "Faction: <highlight>{$whois->faction}<end>\n";
			$list .= "Character ID: <highlight>{$whois->charid}<end>\n\n";
			
			$list .= "Source: $whois->source\n\n";
			
	        $list .= "<a href='chatcmd:///tell <myname> history $name'>Check $name's History</a>\n";
	        $list .= "<a href='chatcmd:///tell <myname> is $name'>Check $name's online status</a>\n";
	        if ($whois->guild) {
		        $list .= "<a href='chatcmd:///tell <myname> whoisorg $whois->guild_id'>Show info about {$whois->guild}</a>\n";
				$list .= "<a href='chatcmd:///tell <myname> orglist $whois->guild_id'>Orglist for {$whois->guild}</a>\n";
			}
	        $list .= "<a href='chatcmd:///cc addbuddy $name'>Add to buddylist</a>\n";
	        $list .= "<a href='chatcmd:///cc rembuddy $name'>Remove from buddylist</a>";
			
	        $msg .= " :: " . Text::make_blob("More info", $list);

			$altInfo = Alts::get_alt_info($name);
			if (count($altInfo->alts) > 0) {
				$msg .= " :: " . Alts::get_alts_blob($name);
			}
	    }
    } else {
        $msg = "Player <highlight>{$name}<end> does not exist.";
	}

    $chatBot->send($msg, $sendto);
} else if (preg_match("/^whoisall (.+)$/i", $message, $arr)) {
    $name = ucfirst(strtolower($arr[1]));
    for ($i = 1; $i <= 2; $i ++) {
        if ($i == 1) {
            $server = "Atlantean";
        } else if ($i == 2) {
            $server = "Rimor";
		}

        $whois = Player::lookup($name, $i);
        if ($whois !== null) {
            $msg = Player::get_info($whois);

			$list = "<header> :::::: Detailed info for {$name} :::::: <end>\n\n";
	        $list .= "Name: <highlight>{$whois->firstname} \"{$name}\" {$whois->lastname}<end>\n";
			if ($whois->guild) {
				$list .= "Guild: <highlight>{$whois->guild} ({$whois->guild_id})<end>\n";
				$list .= "Guild Rank: <highlight>{$whois->guild_rank} ({$whois->guild_rank_id})<end>\n";
			}
			$list .= "Breed: <highlight>{$whois->breed}<end>\n";
			$list .= "Gender: <highlight>{$whois->gender}<end>\n";
			$list .= "Profession: <highlight>{$whois->profession} ({$whois->prof_title})<end>\n";
			$list .= "Level: <highlight>{$whois->level}<end>\n";
			$list .= "AI Level: <highlight>{$whois->ai_level} ({$whois->ai_rank})<end>\n";
			$list .= "Faction: <highlight>{$whois->faction}<end>\n\n";
			
			$list .= "Source: $whois->source\n\n";

            $list .= "<a href='chatcmd:///tell <myname> history $name'>Check $name's History</a>\n";
            $list .= "<a href='chatcmd:///tell <myname> is $name'>Check $name's online status</a>\n";
            $list .= "<a href='chatcmd:///cc addbuddy $name'>Add to buddylist</a>\n";
            $list .= "<a href='chatcmd:///cc rembuddy $name'>Remove from buddylist</a>";
			
            $msg .= " :: ".Text::make_blob("More info", $list);
            $msg = "<highlight>Server $server:<end> ".$msg;
        } else {
            $msg = "Server $server: Player <highlight>{$name}<end> does not exist.";
		}

        $chatBot->send($msg, $sendto);
    }
} else {
	$syntax_error = true;
}

?>
