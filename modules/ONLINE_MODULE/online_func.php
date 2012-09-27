<?php
/*
 ** Author: Mindrila (RK1)
 ** Description: Function file for the ONLINE_MODULE
 ** Version: 1.0
 **
 ** Under BudaBot's license.
 */

function get_online_list($prof = "all") {
	$chatBot = Registry::getInstance('chatBot');
	$db = Registry::getInstance('db');
	$setting = Registry::getInstance('setting');

	if ($prof != 'all') {
		$prof_query = "AND `profession` = '$prof'";
	}

	if ($setting->get('online_group_by') == 'profession') {
		$order_by = "ORDER BY `profession`, `level` DESC";
	} else if ($setting->get('online_group_by') == 'guild') {
		$order_by = "ORDER BY `channel` ASC, `name` ASC";
	}

	$blob = '';

	// Guild Channel Part
	$data = $db->query("SELECT p.*, o.name, o.channel, o.afk FROM `online` o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE o.channel_type = 'guild' {$prof_query} {$order_by}");
	$numguild = count($data);

	if ($numguild >= 1) {
		$blob .= "<header2> :::::: $numguild ".($numguild == 1 ? "Member":"Members")." online ".($chatBot->vars['my_guild'] != '' ? "[<myguild>] ":"")." ::::::<end>\n";

		// create the list with alts shown
		$blob .= createList($data, $list, true, $setting->get("online_show_org_guild"));
	}

	// Private Channel Part
	$data = $db->query("SELECT p.*, o.name, o.channel, o.afk FROM `online` o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE o.channel_type = 'priv' {$prof_query} {$order_by}");
	$numguest = count($data);

	if ($numguest >= 1) {
		if ($numguild >= 1) {
			$blob .= "\n\n<header2>$numguest ".($numguest == 1 ? "User":"Users")." in Private Channel<end>\n";
		} else {
			$blob .= "<header2> :::::: $numguest ".($numguest == 1 ? "User":"Users")." in Private Channel ::::::<end>\n";
		}

		// create the list of guests, without showing alts
		$blob .= createList($data, $list, true, $setting->get("online_show_org_priv"));
	}

	// IRC part
	$data = $db->query("SELECT o.name, o.afk, o.channel, o.channel_type, '' AS profession FROM `online` o WHERE o.channel_type = 'irc' AND o.name <> '<myname>' ORDER BY `name` ASC");
	$numirc = count($data);

	if ($numirc >= 1) {
		if ($numguild + $numguest >= 1) {
			$blob .= "\n\n<header2>$numirc ".($numirc == 1 ? "User":"Users")." in IRC Channel(s) <end>\n";
		} else {
			$blob .= "<header2> :::::: $numirc ".($numirc == 1 ? "User":"Users")." in IRC Channel(s) :::::: end>\n";
		}

		// create the list of guests
		$blob .= createListByChannel($data, $list, false, false);
	}

	$numonline = $numguild + $numguest + $numirc;

	$msg .= "$numonline ".($numonline == 1 ? "member":"members")." online";

	// BBIN part
	if ($setting->get("bbin_status") == 1) {
		// members
		$data = $db->query("SELECT * FROM bbin_chatlist_<myname> WHERE (`guest` = 0) {$prof_query} ORDER BY `profession`, `level` DESC");
		$numbbinmembers = count($data);

		if ($numbbinmembers >= 1) {
			$blob .= "\n\n<header2>$numbbinmembers ".($numbbinmembers == 1 ? "Member":"Members")." in BBIN<end>\n";

			$blob .= createListByProfession($data, $list, false, true);
		}

		// guests
		$data = $db->query("SELECT * FROM bbin_chatlist_<myname> WHERE (`guest` = 1) {$prof_query} ORDER BY `profession`, `level` DESC");
		$numbbinguests = count($data);

		if ($numbbinguests >= 1) {
			$blob .= "\n\n<header2>$numbbinguests ".($numbbinguests == 1 ? "Guest":"Guests")." in BBIN<end>\n";

			$blob .= createListByProfession($data, $list, false, true);
		}

		$numonline += $numbbinguests + $numbbinmembers;

		$msg .= " <green>BBIN<end>:".($numbbinguests + $numbbinmembers)." online";
	}

	return array($numonline, $msg, $blob);
}

function createList(&$data, &$list, $show_alts, $show_org_info) {
	$setting = Registry::getInstance('setting');

	if ($setting->get('online_group_by') == 'profession') {
		return createListByProfession($data, $list, $show_alts, $show_org_info);
	} else if ($setting->get('online_group_by') == 'guild') {
		return createListByChannel($data, $list, $show_alts, $show_org_info);
	}
}

function createListByChannel(&$data, &$list, $show_alts, $show_org_info) {
	$setting = Registry::getInstance('setting');

	//Colorful temporary var settings (avoid a mess of if statements later in the function)
	$fancyColon = ($setting->get("online_colorful") == "1") ? "<highlight>::<end>":"::";

	$blob = '';
	forEach ($data as $row) {
		$name = Text::make_chatcmd($row->name, "/tell $row->name");

		if ($current_channel != $row->channel) {
			$current_channel = $row->channel;
			$blob .= "\n<tab><highlight>$current_channel<end>\n";
		}

		$afk = get_afk_info($row->afk, $fancyColon);
		$alt = ($show_alts == true) ? get_alt_char_info($row->name, $fancyColon):"";

		switch ($row->profession) {
			case "":
				$blob .= "<tab><tab>$name - Unknown$alt\n";
				break;
			default:
				$admin = ($show_alts == true) ? get_admin_info($row->name, $fancyColon):"";
				$guild = get_org_info($show_org_info, $fancyColon, $row->guild, $row->guild_rank);
				$blob .= "<tab><tab>$name (Lvl $row->level/<green>$row->ai_level<end>)$guild$afk$alt$admin\n";
		}
	}

	return $blob;
}

function createListByProfession(&$data, &$list, $show_alts, $show_org_info) {
	$setting = Registry::getInstance('setting');

	//Colorful temporary var settings (avoid a mess of if statements later in the function)
	$fancyColon = ($setting->get("online_colorful") == "1") ? "<highlight>::<end>":"::";

	$current_profession = "";

	$blob = '';
	forEach ($data as $row) {
		if ($current_profession != $row->profession) {
			if ($setting->get("fancy_online") == 0) {
				// old style delimiters
				$blob .= "\n<tab><highlight>$row->profession<end>\n";
			} else {
				// fancy delimiters
				$blob .= "\n<img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER>\n";

				if ($setting->get("icon_fancy_online") == 1) {
					if ($row->profession == "Adventurer") {
						$blob .= "<img src=rdb://84203>";
					} else if ($row->profession == "Agent") {
						$blob .= "<img src=rdb://16186>";
					} else if ($row->profession == "Bureaucrat") {
						$blob .= "<img src=rdb://46271>";
					} else if ($row->profession == "Doctor") {
						$blob .= "<img src=rdb://44235>";
					} else if ($row->profession == "Enforcer") {
						$blob .= "<img src=rdb://117926>";
					} else if ($row->profession == "Engineer") {
						$blob .= "<img src=rdb://16307>";
					} else if ($row->profession == "Fixer") {
						$blob .= "<img src=rdb://16300>";
					} else if ($row->profession == "Keeper") {
						$blob .= "<img src=rdb://38911>";
					} else if ($row->profession == "Martial Artist") {
						$blob .= "<img src=rdb://16289>";
					} else if ($row->profession == "Meta-Physicist") {
						$blob .= "<img src=rdb://16283>";
					} else if ($row->profession == "Nano-Technician") {
						$blob .= "<img src=rdb://45190>";
					} else if ($row->profession == "Soldier") {
						$blob .= "<img src=rdb://16195>";
					} else if ($row->profession == "Shade") {
						$blob .= "<img src=rdb://39290>";
					} else if ($row->profession == "Trader") {
						$blob .= "<img src=rdb://118049>";
					} else {
						$blob .= "<img src=rdb://46268>";
					}
				}

				$blob .= " <highlight>$row->profession<end>\n<img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER>\n";
			}

			$current_profession = $row->profession;
		}

		$name = Text::make_chatcmd($row->name, "/tell $row->name");
		$afk  = get_afk_info($row->afk, $fancyColon);
		$alt  = ($show_alts == true) ? get_alt_char_info($row->name, $fancyColon):"";

		switch ($row->profession) {
			case "":
				$blob .= "<tab><tab>$name - Unknown$alt\n";
				break;
			default:
				$admin = ($show_alts == true) ? get_admin_info($row->name, $fancyColon):"";
				$guild = get_org_info($show_org_info, $fancyColon, $row->guild, $row->guild_rank);
				$blob .= "<tab><tab>$name (Lvl $row->level/<green>$row->ai_level<end>)$guild$afk$alt$admin\n";
		}
	}

	return $blob;
}

function get_org_info($show_org_info, $fancyColon, $guild, $guild_rank) {
	switch ($show_org_info) {
		case  3: return $guild != "" ? " $fancyColon {$guild}":" $fancyColon Not in a guild";
		case  2: return $guild != "" ? " $fancyColon {$guild} ({$guild_rank})":" $fancyColon Not in a guild";
		case  1: return $guild != "" ? " $fancyColon {$guild_rank}":"";
		default: return "";
	}
}

function get_admin_info($name, $fancyColon) {
	$setting = Registry::getInstance('setting');

	if ($setting->get("online_admin") != 1) return "";

	$accessLevel = Registry::getInstance('accessLevel');

	switch ($accessLevel->getAccessLevelForCharacter($name)) {
		case 'superadmin': return " $fancyColon <red>SuperAdmin<end>";
		case 'admin'     : return " $fancyColon <red>Admin<end>";
		case 'mod'       : return " $fancyColon <green>Mod<end>";
		case 'rl'        : return " $fancyColon <orange>RL<end>";
	}
}

function get_afk_info($afk, $fancyColon) {
	switch ($afk) {
		case       "": return "";
		case "kiting": return " $fancyColon <red>KITING<end>";
		case      "1": return " $fancyColon <red>AFK<end>";
		default      : return " $fancyColon <red>AFK - {$afk}<end>";
	}
}

function get_alt_char_info($name, $fancyColon) {
	$alts = Registry::getInstance('alts');
	$altinfo = $alts->get_alt_info($name);

	if (count($altinfo->alts) > 0) {
		$alt = " $fancyColon <a href='chatcmd:///tell <myname> alts {$name}'>".($altinfo->main == $name ? "Alts":"Alt of {$altinfo->main}")."</a>";
	}
	return $alt;
}

?>
