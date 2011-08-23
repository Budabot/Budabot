<?php
/*
 ** Author: Mindrila (RK1)
 ** Description: Function file for the ONLINE_MODULE
 ** Version: 1.0
 **
 ** Under BudaBot's license.
 */

function get_online_list($prof = "all") {
	$db = DB::get_instance();
	global $chatBot;
	
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
	$db->query("SELECT p.*, o.name, o.channel, o.afk FROM `online` o LEFT JOIN players p ON o.name = p.name WHERE o.channel_type = 'guild' {$prof_query} {$order_by}");

	$oldprof = "";
	$numonline = $db->numrows();
	if ($chatBot->vars['my_guild'] != '') {
		$guild_name = "[<myguild>] ";
	}
	if ($numonline == 1) {
		$list[] = array("content" => "<header>::::: 1 member online $guild_name:::::<end>\n");
	} else {
		$list[] = array("content" => "<header>::::: $numonline members online $guild_name:::::<end>\n");
	}
	
	$data = $db->fObject("all");
	// create the list with alts shown
	createList($data, $list, true);

	// Private Channel Part
	$db->query("SELECT p.*, o.name, o.channel, o.afk FROM `online` o LEFT JOIN players p ON o.name = p.name WHERE o.channel_type = 'priv' {$prof_query} {$order_by}");

	$numguest = $db->numrows();
	if ($numguest == 1) {
		$list[] = array("content" => "\n\n<highlight><u>1 User in Private Channel<end></u>\n");
	} else {
		$list[] = array("content" => "\n\n<highlight><u>$numonline Users in Private Channel<end></u>\n");
	}
	$data = $db->fObject("all");
	// create the list of guests, without showing alts
	createList($data, $list, true);
	$numonline += $numguest;

	if ($numonline == 1) {
		$msg .= "1 member online";
	} else {
		$msg .= "{$numonline} members online";
	}

	// BBIN part
	if (Setting::get("bbin_status") == 1) {
		// members
		$db->query("SELECT * FROM bbin_chatlist_<myname> WHERE (`guest` = 0) {$prof_query} ORDER BY `profession`, `level` DESC");
		$numbbinmembers = $db->numrows();
		$data = $db->fObject("all");
		if ($numbbinmembers == 1) {
			$list[] = array("content" => "\n\n<highlight><u>1 member in BBIN<end></u>\n");
		} else {
			$list[] = array("content" => "\n\n<highlight><u>$numbbinmembers members in BBIN<end></u>\n");
		}
		createListByProfession($data, $list, false);
		
		// guests
		$db->query("SELECT * FROM bbin_chatlist_<myname> WHERE (`guest` = 1) {$prof_query} ORDER BY `profession`, `level` DESC");
		$numbbinguests = $db->numrows();
		$data = $db->fObject("all");
		if ($numbbinguests == 1) {
			$list[] = array("content" => "\n\n<highlight><u>1 guest in BBIN<end></u>\n");
		} else {
			$list[] = array("content" => "\n\n<highlight><u>$numbbinguests guests in BBIN<end></u>\n");
		}
		createListByProfession($data, $list, false);
		
		$numonline += $numbbinguests + $numbbinmembers;
		
		$msg .= " <green>BBIN<end>:".($numbbinguests + $numbbinmembers)." online";
	}

	return array ($numonline, $msg, $list);
}

function createList(&$data, &$list, $show_alts) {
	if (Setting::get('online_group_by') == 'profession') {
		return createListByProfession($data, $list, $show_alts);
	} else if (Setting::get('online_group_by') == 'guild') {
		return createListByChannel($data, $list, $show_alts);
	}
}

function createListByChannel(&$data, &$list, $show_alts) {
	global $chatBot; //To access my_guild
	$db = DB::get_instance();

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

		if ($row->afk == "kiting") {
			$afk = " $fancyColon <red>KITING<end>";
		} else if ($row->afk == '1') {
			$afk = " $fancyColon <red>AFK<end>";
		} else if ($row->afk != '') {
			$afk = " $fancyColon <red>AFK - {$row->afk}<end>";
		} else {
			$afk = "";
		}
		
		if ($row->profession == "Unknown") {
			$current_content .= "<tab><tab>$name - Unknown";
			if ($show_alts == true) {
				$altinfo = Alts::get_alt_info($row->name);
				if (count($altinfo->alts) > 0) {
					if ($altinfo->main == $row->name) {
						$current_content .= " $fancyColon <a href='chatcmd:///tell <myname> alts $row->name'>Alts</a>";
					} else {
						$current_content .= " $fancyColon <a href='chatcmd:///tell <myname> alts $row->name'>Alt of {$altinfo->main}</a>";
					}
				}
			}
			
			$current_content .= "\n";
		} else {
			if ($show_alts == true) {
				$altinfo = Alts::get_alt_info($row->name);
				if (count($altinfo->alts) > 0) {
					if ($altinfo->main == $row->name) {
						$alt = " $fancyColon <a href='chatcmd:///tell <myname> alts $row->name'>Alts</a>";
					} else {
						$alt = " $fancyColon <a href='chatcmd:///tell <myname> alts $row->name'>Alt of {$altinfo->main}</a>";
					}
				}
				
				if (Setting::get("online_admin") == "1") { //When building list without alts, we don't show admin info
					$alvl = AccessLevel::get_admin_level($row->name);
					switch ($alvl) {
						case 4: $admin = " $fancyColon <red>Admin<end>"; break;
						case 3: $admin = " $fancyColon <green>Mod<end>"; break;
						case 2: $admin = " $fancyColon <orange>RL<end>"; break;
					}
					
					if (AccessLevel::check_access($row->name, 'superadmin')) {
						$admin = " $fancyColon <red>SuperAdmin<end>";
					}
				} else {
					$admin = "";
				}
			} else {
				$alt = "";
				$admin = "";
			}
			
			if ($orgShow == "2" || ($orgShow == "1" && ($row->guild != $chatBot->vars['my_guild'] || $chatBot->vars['my_guild'] != ''))) {
				if ($row->guild == "") { //No guild
					$guild = " $fancyColon Not in a guild";
				} else if ($orgShow == "2" && $row->guild == $chatBot->vars['my_guild']) {
					$guild = " $fancyColon " . $row->guild_rank; // If in same guild, shows rank
				} else if ($orgShow == "2") {
					$guild = " $fancyColon " . $row->guild . " (" . $row->guild_rank . ")"; // Not in guild, show guild name & rank (on all guild info)
				} else {
					$guild = " $fancyColon " . $row->guild; // Not in guild, show guild name (on limited guild info)
				}
			}
			
			$current_content .= "<tab><tab>$name (Lvl $row->level/<green>$row->ai_level<end>)$guild$afk$alt$admin\n";
		}
	}
	
	$list[] = array("header" => $current_header, "content" => $current_content); //And don't forget to store the last segment
}

function createListByProfession(&$data, &$list, $show_alts) {
	global $chatBot; //To access my_guild
	$db = DB::get_instance();

	//Colorful temporary var settings (avoid a mess of if statements later in the function)
	$fancyColon = "::";
	if (Setting::get("online_colorful") == "1") {
		$fancyColon = "<highlight>::<end>";
	}
	
	$orgShow = Setting::get("online_show_org");
	
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

		if ($row->afk == "kiting") {
			$afk = " $fancyColon <red>KITING<end>";
		} else if ($row->afk == '1') {
			$afk = " $fancyColon <red>AFK<end>";
		} else if ($row->afk != '') {
			$afk = " $fancyColon <red>AFK - {$row->afk}<end>";
		} else {
			$afk = "";
		}
		
		if ($row->profession == "Unknown") {
			$current_content .= "<tab><tab>$name - Unknown";
			if ($show_alts == true) {
				$altinfo = Alts::get_alt_info($row->name);
				if (count($altinfo->alts) > 0) {
					if ($altinfo->main == $row->name) {
						$current_content .= " $fancyColon <a href='chatcmd:///tell <myname> alts $row->name'>Alts</a>";
					} else {
						$current_content .= " $fancyColon <a href='chatcmd:///tell <myname> alts $row->name'>Alt of {$altinfo->main}</a>";
					}
				}
			}
			
			$current_content .= "\n";
		} else {
			if ($show_alts == true) {
				$altinfo = Alts::get_alt_info($row->name);
				if (count($altinfo->alts) > 0) {
					if ($altinfo->main == $row->name) {
						$alt = " $fancyColon <a href='chatcmd:///tell <myname> alts $row->name'>Alts</a>";
					} else {
						$alt = " $fancyColon <a href='chatcmd:///tell <myname> alts $row->name'>Alt of {$altinfo->main}</a>";
					}
				}
				
				//When building list without alts, we don't show admin info
				if (Setting::get("online_admin") == "1") {
					$alvl = AccessLevel::get_admin_level($row->name);
					switch ($alvl) {
						case 4: $admin = " $fancyColon <red>Admin<end>"; break;
						case 3: $admin = " $fancyColon <green>Mod<end>"; break;
						case 2: $admin = " $fancyColon <orange>RL<end>"; break;
					}
					
					if (AccessLevel::check_access($row->name, 'superadmin')) {
						$admin = " $fancyColon <red>SuperAdmin<end>";
					}
				} else {
					$admin = "";
				}
			} else {
				$alt = "";
				$admin = "";
			}
			
			$guild = '';
			if ($orgShow == "2" || ($orgShow == "1" && ($row->guild != $chatBot->vars['my_guild'] || $chatBot->vars['my_guild'] == ''))) {
				if ($row->guild == "") { //No guild
					$guild = " $fancyColon Not in a guild";
				} else if ($orgShow == "1" && $row->guild == $chatBot->vars['my_guild']) {
					$guild = " $fancyColon " . $row->guild_rank; // If in same guild, shows rank
				} else if ($orgShow == "2") {
					$guild = " $fancyColon " . $row->guild . " (" . $row->guild_rank . ")"; // Not in guild, show guild name & rank (on all guild info)
				} else {
					$guild = " $fancyColon " . $row->guild; // Not in guild, show guild name (on limited guild info)
				}
			}
			
			$current_content .= "<tab><tab>$name (Lvl $row->level/<green>$row->ai_level<end>)$guild$afk$alt$admin\n";
		}
	}
	
	$list[] = array("header" => $current_header, "content" => $current_content); //And don't forget to store the last segment
}

?>