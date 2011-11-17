<?php

if (!function_exists('getNameHistory')) {
	function getNameHistory($charid, $dimension) {
		$db = DB::get_instance();
	
		$sql = "SELECT * FROM name_history WHERE charid = '{$charid}' AND dimension = {$dimension} ORDER BY dt DESC";
		$db->query($sql);
		$data = $db->fObject('all');
		
		$list = "<header> :::::: Name History :::::: <end>\n\n";
		if (count($data) > 0) {
			forEach ($data as $row) {
				$list .= "<green>{$row->name}<end> " . date("M j, Y, G:i", $row->dt) . "\n";
			}
		} else {
			$list .= "No name history available\n";
		}
		
		return $list;
	}
}

if (preg_match("/^whois (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
    $uid = $chatBot->get_uid($name);
    if ($uid) {
        $whois = Player::get_by_name($name);
        if ($whois === null) {
			$list = "<header> :::::: Basic Info for {$name} :::::: <end>\n\n";
			$list .= "<orange>Note: Could not retrieve detailed info for character.<end>\n\n";
	        $list .= "Name: <highlight>{$name}<end>\n";
			$list .= "Character ID: <highlight>{$whois->charid}<end>\n\n";
			$list .= "<pagebreak>" . getNameHistory($uid, "<dim>");
        	
			$msg = Text::make_blob("Basic Info for $name", $list);
        } else {
	        $list = "<header> :::::: Detailed Info for {$name} :::::: <end>\n\n";
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
			
			$list .= "<pagebreak>" . getNameHistory($uid, "<dim>");

			$list .= "\n<pagebreak><header> :::::: Options :::::: <end>\n\n";
			
	        $list .= Text::make_chatcmd('History', "/tell <myname> history $name") . "\n";
	        $list .= Text::make_chatcmd('Online Status', "/tell <myname> is $name") . "\n";
	        if (isset($whois->guild_id)) {
		        $list .= Text::make_chatcmd('Whoisorg', "/tell <myname> whoisorg $whois->guild_id") . "\n";
				$list .= Text::make_chatcmd('Orglist', "/tell <myname> orglist $whois->guild_id") . "\n";
			}
			
	        $msg = Player::get_info($whois) . " :: " . Text::make_blob("More Info", $list);

			$altInfo = Alts::get_alt_info($name);
			if (count($altInfo->alts) > 0) {
				$msg .= " :: " . $altInfo->get_alts_blob(false, true);
			}
	    }
    } else {
        $msg = "Character <highlight>{$name}<end> does not exist.";
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

			$list .= "<pagebreak><header> :::::: Options :::::: <end>\n\n";

            $list .= "<a href='chatcmd:///tell <myname> history {$name} {$i}'>History</a>\n";
			
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
