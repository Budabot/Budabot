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
	
	if ($prof != 'all') {
		$prof_query = "AND `profession` = '$prof'";
	}
	
	if (Setting::get('online_group_by') == 'profession') {
		$order_by = "ORDER BY `profession`, `level` DESC";
	} else if (Setting::get('online_group_by') == 'guild') {
		$order_by = "ORDER BY `channel` ASC, `name` ASC";
	}

	//$list = "";
	$list = array();
	$data = $db->query("SELECT p.*, o.name, o.channel, o.afk FROM `online` o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE o.channel_type = 'guild' {$prof_query} {$order_by}");

	$oldprof = "";
	$numonline = count($data);
	if ($chatBot->vars['my_guild'] != '') {
		$guild_name = "[<myguild>] ";
	}
	if ($numonline == 1) {
		$list[] = array("content" => "<header> :::::: 1 member online $guild_name:::::: <end>\n");
	} else {
		$list[] = array("content" => "<header> :::::: $numonline members online $guild_name:::::: <end>\n");
	}
	
	// create the list with alts shown
	createList($data, $list, true, Setting::get("online_show_org_guild"));

	// Private Channel Part
	$data = $db->query("SELECT p.*, o.name, o.channel, o.afk FROM `online` o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE o.channel_type = 'priv' {$prof_query} {$order_by}");

	$numguest = count($data);
	if ($numguest == 1) {
		$list[] = array("content" => "\n\n<highlight><u>1 User in Private Channel</u><end>\n");
	} else {
		$list[] = array("content" => "\n\n<highlight><u>$numguest Users in Private Channel</u><end>\n");
	}

	// create the list of guests, without showing alts
	createList($data, $list, true, Setting::get("online_show_org_priv"));
	$numonline += $numguest;

	if ($numonline == 1) {
		$msg .= "1 member online";
	} else {
		$msg .= "{$numonline} members online";
	}

	// BBIN part
	if (Setting::get("bbin_status") == 1) {
		// members
		$data = $db->query("SELECT * FROM bbin_chatlist_<myname> WHERE (`guest` = 0) {$prof_query} ORDER BY `profession`, `level` DESC");
		$numbbinmembers = count($data);
		if ($numbbinmembers == 1) {
			$list[] = array("content" => "\n\n<highlight><u>1 member in BBIN</u><end>\n");
		} else {
			$list[] = array("content" => "\n\n<highlight><u>$numbbinmembers members in BBIN</u><end>\n");
		}
		createListByProfession($data, $list, false, true);
		
		// guests
		$data = $db->query("SELECT * FROM bbin_chatlist_<myname> WHERE (`guest` = 1) {$prof_query} ORDER BY `profession`, `level` DESC");
		$numbbinguests = count($data);
		if ($numbbinguests == 1) {
			$list[] = array("content" => "\n\n<highlight><u>1 guest in BBIN<end></u>\n");
		} else {
			$list[] = array("content" => "\n\n<highlight><u>$numbbinguests guests in BBIN<end></u>\n");
		}
		createListByProfession($data, $list, false, true);
		
		$numonline += $numbbinguests + $numbbinmembers;
		
		$msg .= " <green>BBIN<end>:".($numbbinguests + $numbbinmembers)." online";
	}

	return array ($numonline, $msg, $list);
}

function createList(&$data, &$list, $show_alts, $show_org_info) {
	if (Setting::get('online_group_by') == 'profession') {
		return createListByProfession($data, $list, $show_alts, $show_org_info);
	} else if (Setting::get('online_group_by') == 'guild') {
		return createListByChannel($data, $list, $show_alts, $show_org_info);
	}
}

function createListByChannel(&$data, &$list, $show_alts, $show_org_info) {
	//Colorful temporary var settings (avoid a mess of if statements later in the function)
	$fancyColon = "::";
	if (Setting::get("online_colorful") == "1") {
		$fancyColon = "<highlight>::<end>";
	}
	
	$orgShow = Setting::get("online_show_org");
	
	$current_channel = "";
	$current_header = "";
	$current_content = "";
	forEach ($data as $row) {
		$name = Text::make_chatcmd($row->name, "/tell $row->name");
		 
		if ($row->profession == "") {
			$row->profession = "Unknown";
		}
		
		if ($current_channel != $row->channel) {
			if (!empty($current_channel)) {
				$list[] = array("header" => $current_header, "content" => $current_content); //And don't forget to store the last segment
			}
			$current_header = "\n<tab><highlight>$row->channel<end>\n";
			$current_content = "";
			$current_channel = $row->channel;
		}

		$afk = get_afk_info($row->afk, $fancyColon);
		
		if ($row->profession == "Unknown") {
			$current_content .= "<tab><tab>$name - Unknown";
			if ($show_alts == true) {
				$alt = get_alt_char_info($row->name, $fancyColon);
			}
			
			$current_content .= "$alt\n";
		} else {
			if ($show_alts == true) {
				$alt = get_alt_char_info($row->name, $fancyColon);
				$admin = get_admin_info($row->name, $fancyColon);
			} else {
				$alt = "";
				$admin = "";
			}

			$guild = get_org_info($show_org_info, $fancyColon, $row->guild, $row->guild_rank);
			
			$current_content .= "<tab><tab>$name (Lvl $row->level/<green>$row->ai_level<end>)$guild$afk$alt$admin\n";
		}
	}
	
	$list[] = array("header" => $current_header, "content" => $current_content); //And don't forget to store the last segment
}

function createListByProfession(&$data, &$list, $show_alts, $show_org_info) {
	//Colorful temporary var settings (avoid a mess of if statements later in the function)
	$fancyColon = "::";
	if (Setting::get("online_colorful") == "1") {
		$fancyColon = "<highlight>::<end>";
	}
	
	$current_profession = "";
	$current_header = "";
	$current_content = "";
	forEach ($data as $row) {
		$name = Text::make_chatcmd($row->name, "/tell $row->name");
		
		if ($row->profession == "") {
			$row->profession = "Unknown";
		}
		
		if ($current_profession != $row->profession) {
			if (!empty($current_profession)) {
				$list[] = array("header" => $current_header, "content" => $current_content, "incomplete_footer" => "\nContinued...", "incomplete_header" => $current_header);
				$current_header = "";
				$current_content = "";
			}
			if (Setting::get("fancy_online") == 0) {
				// old style delimiters
				$current_header = "\n<tab><highlight>$row->profession<end>\n";
				$current_profession = $row->profession;
			} else {
				// fancy delimiters
				$current_header = "\n<img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER>\n";
				if (Setting::get("icon_fancy_online") == 1) {
					if ($row->profession == "Adventurer")
						$current_header .= "<img src=rdb://84203>";
					else if ($row->profession == "Agent")
						$current_header .= "<img src=rdb://16186>";
					else if ($row->profession == "Bureaucrat")
						$current_header .= "<img src=rdb://46271>";
					else if ($row->profession == "Doctor")
						$current_header .= "<img src=rdb://44235>";
					else if ($row->profession == "Enforcer")
						$current_header .= "<img src=rdb://117926>";
					else if ($row->profession == "Engineer")
						$current_header .= "<img src=rdb://16307>";
					else if ($row->profession == "Fixer")
						$current_header .= "<img src=rdb://16300>";
					else if ($row->profession == "Keeper")
						$current_header .= "<img src=rdb://38911>";
					else if ($row->profession == "Martial Artist")
						$current_header .= "<img src=rdb://16289>";
					else if ($row->profession == "Meta-Physicist")
						$current_header .= "<img src=rdb://16283>";
					else if ($row->profession == "Nano-Technician")
						$current_header .= "<img src=rdb://45190>";
					else if ($row->profession == "Soldier")
						$current_header .= "<img src=rdb://16195>";
					else if ($row->profession == "Shade")
						$current_header .= "<img src=rdb://39290>";
					else if ($row->profession == "Trader")
						$current_header .= "<img src=rdb://118049>";
					else {
						$current_header .= "<img src=rdb://46268>";
					}
				}
				$current_header .= " <highlight>$row->profession<end>";
				$current_profession = $row->profession;

				$current_header .= "\n<img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER>\n";
			}
		}

		$afk = get_afk_info($row->afk, $fancyColon);
		
		if ($row->profession == "Unknown") {
			$current_content .= "<tab><tab>$name - Unknown";
			if ($show_alts == true) {
				$alt = get_alt_char_info($row->name, $fancyColon);
			}
			
			$current_content .= "$alt\n";
		} else {
			if ($show_alts == true) {
				$alt = get_alt_char_info($row->name, $fancyColon);
				$admin = get_admin_info($row->name, $fancyColon);
			} else {
				$alt = "";
				$admin = "";
			}
			
			$guild = get_org_info($show_org_info, $fancyColon, $row->guild, $row->guild_rank);
			
			$current_content .= "<tab><tab>$name (Lvl $row->level/<green>$row->ai_level<end>)$guild$afk$alt$admin\n";
		}
	}
	
	$list[] = array("header" => $current_header, "content" => $current_content); //And don't forget to store the last segment
}

function get_org_info($show_org_info, $fancyColon, $guild, $guild_rank) {
	if ($show_org_info == 2) {
		if ($guild == "") {
			return " $fancyColon Not in a guild";
		} else {
			return " $fancyColon {$guild} ({$guild_rank})";
		}
	} else if ($show_org_info == 1) {
		if ($guild != "") {
			return " $fancyColon {$guild_rank}";
		}
	}
	return '';
}

function get_admin_info($name, $fancyColon) {
	$chatBot = Registry::getInstance('chatBot');
	$accessLevel = Registry::getInstance('accessLevel');
	if (Setting::get("online_admin") == 1) {
		$alvl = $accessLevel->getAccessLevelForCharacter($name);
		switch ($alvl) {
			case 'superadmin': $admin = " $fancyColon <red>SuperAdmin<end>";
			case 'admin': $admin = " $fancyColon <red>Admin<end>"; break;
			case 'mod': $admin = " $fancyColon <green>Mod<end>"; break;
			case 'rl': $admin = " $fancyColon <orange>RL<end>"; break;
		}
	} else {
		$admin = "";
	}

	return $admin;
}

function get_afk_info($afk, $fancyColon) {
	if ($afk == "kiting") {
		$ret = " $fancyColon <red>KITING<end>";
	} else if ($afk == '1') {
		$ret = " $fancyColon <red>AFK<end>";
	} else if ($afk != '') {
		$ret = " $fancyColon <red>AFK - {$afk}<end>";
	} else {
		$ret = "";
	}
	return $ret;
}

function get_alt_char_info($name, $fancyColon) {
	$altinfo = Alts::get_alt_info($name);
	if (count($altinfo->alts) > 0) {
		if ($altinfo->main == $name) {
			$alt = " $fancyColon <a href='chatcmd:///tell <myname> alts {$name}'>Alts</a>";
		} else {
			$alt = " $fancyColon <a href='chatcmd:///tell <myname> alts {$name}'>Alt of {$altinfo->main}</a>";
		}
	}
	return $alt;
}

?>