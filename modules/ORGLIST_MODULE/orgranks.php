<?php

if (preg_match("/^orgranks$/i", $message, $arr) || preg_match("/^orgranks ([0-9]+)$/i", $message, $arr1) || preg_match("/^orgranks ([a-z0-9-]+)$/i", $message, $arr2)) {
	if (isset($arr[0])) {
		if ($chatBot->vars["my_guild_id"] == "") {
			$msg = "The bot does not belong to an org.";
			$chatBot->send($msg, $sendto);
		}
		
		$sql = "SELECT * FROM org_members_<myname> o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE `mode` != 'del' ORDER BY `guild_rank_id` ASC, o.name ASC";
		$data = $db->query($sql);
		$orgname = "<myguild>";
	} else {
		if (isset($arr2[0])) {
			// Someone's name.  Doing a whois to get an orgID.
			$name = ucfirst(strtolower($arr2[1]));
			$whois = Player::get_by_name($name);

			if ($whois === null) {
				$msg = "Could not find character info for $name.";
				$chatBot->send($msg, $sendto);
				return;
			} else if (!$whois->guild_id) {
				$msg = "Player <highlight>$name<end> does not seem to be in any org.";
				$chatBot->send($msg, $sendto);
				return;
			} else {
				$guild_id = $whois->guild_id;
			}
		} else if (isset($arr1[0])) {
			$guild_id = $arr1[1];
		}

		$msg = "Getting guild info. Please wait...";
		$chatBot->send($msg, $sendto);
		
		$org = Guild::get_by_id($guild_id);
		if ($org === null) {
			$msg = "Error in getting the Org info. Either org does not exist or AO's server was too slow to respond.";
			$chatBot->send($msg, $sendto);
			return;
		}
		
		$sql = "SELECT * FROM players WHERE guild_id = ? AND dimension = '<dim>' ORDER BY guild_rank_id ASC, name ASC";
		$data = $db->query($sql, $guild_id);
		$orgname = $org->orgname;
	}

	$count = count($data);
	if ($count == 0) {
	  	$msg = "No org members found.";
        $chatBot->send($msg, $sendto);
		return;
	}
	
	$blob = array("{$orgname} has {$count} members.\n\n");
	
	$current_rank_id = '';
	$l = "";
	$lh = "";
	forEach ($data as $row) {
		if ($current_rank_id != $row->guild_rank_id) {
			if ($current_rank_id != '') {
				$blob []= array('header' => $lh, 'content' => $l, 'footer' => "\n");
				$l = "";
				$lh = "";
			}
			$current_rank_id = $row->guild_rank_id;
			$lh = "<white>{$row->guild_rank}\n";
		}
		
		$l .= "<tab><highlight>{$row->name} (Level {$row->level}";
		if ($row->ai_level > 0) {
			$l .= "<green>/{$row->ai_level}<end>";
		}
		$l .= ", {$row->gender} {$row->breed} {$row->profession})<end>";
		
		if (isset($row->logged_off)) {
			if (Buddylist::is_online($row->name) == 1) {
				$logged_off = "<green>Online<end>";
			} else if ($row->logged_off != "0") {
				$logged_off = date("l F d, Y - H:i", $row->logged_off)."(GMT)";
			} else {
				$logged_off = "<orange>Unknown<end>";
			}
			$l .= " :: Last logoff: $logged_off";
		}
		
		$l .= "\n";
	}
	
	$blob[] = array('header' => $lh, 'content' => $l);
	
	$msg = Text::make_structured_blob("Org ranks for '$orgname' ($count)", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>