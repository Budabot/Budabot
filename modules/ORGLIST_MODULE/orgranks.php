<?php

if (preg_match("/^orgranks$/i", $message, $arr) || preg_match("/^orgranks ([0-9]+)$/i", $message, $arr1) || preg_match("/^orgranks ([a-z0-9-]+)$/i", $message, $arr2)) {
	if (isset($arr[0])) {
		if ($chatBot->vars["my_guild_id"] == "") {
			$msg = "The bot does not belong to an org.";
			$sendto->reply($msg);
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
				$sendto->reply($msg);
				return;
			} else if (!$whois->guild_id) {
				$msg = "Character <highlight>$name<end> does not seem to be in an org.";
				$sendto->reply($msg);
				return;
			} else {
				$guild_id = $whois->guild_id;
			}
		} else if (isset($arr1[0])) {
			$guild_id = $arr1[1];
		}

		$msg = "Getting guild info. Please wait...";
		$sendto->reply($msg);

		$org = Guild::get_by_id($guild_id);
		if ($org === null) {
			$msg = "Error in getting the Org info. Either org does not exist or AO's server was too slow to respond.";
			$sendto->reply($msg);
			return;
		}

		$sql = "SELECT * FROM players WHERE guild_id = ? AND dimension = '<dim>' ORDER BY guild_rank_id ASC, name ASC";
		$data = $db->query($sql, $guild_id);
		$orgname = $org->orgname;
	}

	$count = count($data);
	if ($count == 0) {
		$msg = "No org members found.";
        $sendto->reply($msg);
		return;
	}

	$blob = '';

	$current_rank_id = '';
	forEach ($data as $row) {
		if ($current_rank_id != $row->guild_rank_id) {
			$current_rank_id = $row->guild_rank_id;
			$blob .= "\n<header2>{$row->guild_rank}<end>\n";
		}

		$blob .= "<tab><highlight>{$row->name} (Level {$row->level}";
		if ($row->ai_level > 0) {
			$blob .= "<green>/{$row->ai_level}<end>";
		}
		$blob .= ", {$row->gender} {$row->breed} {$row->profession})<end>";

		if (isset($row->logged_off)) {
			if ($buddylistManager->is_online($row->name) == 1) {
				$logged_off = "<green>Online<end>";
			} else if ($row->logged_off != "0") {
				$logged_off = date(Util::DATETIME, $row->logged_off)."(GMT)";
			} else {
				$logged_off = "<orange>Unknown<end>";
			}
			$blob .= " :: Last logoff: $logged_off";
		}

		$blob .= "\n";
	}

	$msg = Text::make_blob("Org ranks for '$orgname' ($count)", $blob);
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
