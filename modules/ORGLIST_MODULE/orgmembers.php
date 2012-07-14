<?php

if (preg_match("/^orgmembers$/i", $message)) {
	if ($chatBot->vars["my_guild_id"] == "") {
		$msg = "The Bot needs to be in a org to show the orgmembers.";
	    $sendto->reply($msg);
		return;
	}

	$data = $db->query("SELECT * FROM org_members_<myname> o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE `mode` != 'del' ORDER BY o.name");
	$members = count($data);
	if ($members == 0) {
		$msg = "No members recorded.";
	    $sendto->reply($msg);
		return;
	}

	$msg = "Getting guild info. Please wait...";
    $sendto->reply($msg);

    $first_char = "";
	$blob = array("<header>::::: Members of the org <myguild> :::::<end>\n\n");
	$l = "";
	forEach ($data as $row) {
		if ($buddylistManager->is_online($row->name) == 1) {
			$logged_off = " :: <highlight>Last logoff:<end> <green>Online<end>";
        } else if ($row->logged_off != "0") {
	        $logged_off = " :: <highlight>Last logoff:<end> " . date(Util::DATETIME, $row->logged_off)."(GMT)";
	    } else {
		$logged_off = " :: <highlight>Last logoff:<end> <orange>Unknown<end>";
		}

	    if ($row->name[0] != $first_char) {
			if ($first_char != "") {
				$blob[] = array("header" => $lh, "content" => $l, "footer" => "\n\n");
				$l = "";
			}
		$first_char = $row->name[0];
			$lh = "<white><u>$first_char</u><end>\n";
		}

		$prof = Util::get_profession_abbreviation($row->profession);

		$l .= "<tab><highlight>$row->name<end> (Lvl $row->level/<green>$row->ai_level<end>/$prof/$row->guild_rank)$logged_off\n";
	}

	$blob[] = array("header" => $lh, "content" => $l);

	$msg = Text::make_structured_blob("<myguild> has $members members currently.", $blob);
	$sendto->reply($msg);
} else if (preg_match("/^orgmembers ([0-9]+)$/i", $message, $arr1) || preg_match("/^orgmembers ([a-z0-9-]+)$/i", $message, $arr2)) {
	if ($arr2) {
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
	} else {
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

	$sql = "SELECT * FROM players WHERE guild_id = ? AND dimension = '<dim>' ORDER BY name ASC";
	$data = $db->query($sql, $guild_id);
	$numrows = count($data);

	$blob = array("{$org->orgname} has {$numrows} members.\n\n");

	$l = "";
	$current_letter = '';
	forEach ($data as $row) {
		if ($current_letter != $row->name[0]) {
			if ($current_letter != "") {
				$blob[] = array("header" => $lh, "content" => $l, "footer" => "\n\n");
				$l = "";
			}
			$current_letter = $row->name[0];
			$lh = "<white><u>{$row->name[0]}</u><end>\n";
		}

		$l .= "<tab><highlight>{$row->name}, {$row->guild_rank} (Level {$row->level}";
		if ($row->ai_level > 0) {
			$l .= "<green>/{$row->ai_level}<end>";
		}
		$l .= ", {$row->gender} {$row->breed} {$row->profession})<end>\n";
	}

	$blob[] = array("header" => $lh, "content" => $l);

	$msg = Text::make_structured_blob("Org members for '$org->orgname'", $blob);
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
