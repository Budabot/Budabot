<?php
	require_once "online_func.php";
	
	DB::loadSQLFile($MODULE_NAME, "online");

	// Online
	Command::register($MODULE_NAME, "", "online.php", "online", "all", "Shows who is the private channel");
	
	Event::register($MODULE_NAME, "logOn", "record_logon_guild.php", "none", "Records an org member login in db");
	Event::register($MODULE_NAME, "logOff", "record_logoff_guild.php", "none", "Records an org member logoff in db");
	
	// Verifies the online list
	Event::register($MODULE_NAME, "10mins", "online_check.php", "none", "Online check");
	
	// Afk Check
	Event::register($MODULE_NAME, "priv", "afk_check.php", "none", "Afk check");
	Event::register($MODULE_NAME, "guild", "afk_check.php", "none", "Afk check");
	Event::register($MODULE_NAME, "guild", "afk.php", "none", "Sets a member afk");
	Event::register($MODULE_NAME, "priv", "afk.php", "none", "Sets a member afk");

	// Settings
	Setting::add($MODULE_NAME, "online_expire", "How long to wait before clearing online list", "edit", "time", "15m", "2m;5m;10m;15m;20m", '', "mod");
	Setting::add($MODULE_NAME, "chatlist_tell", "Mode for Chatlist Cmd in tells", "edit", "options", "1", "Shows online privatechat members;Shows online guild members", "1;0");
	Setting::add($MODULE_NAME, "fancy_online", "Show fancy delimiters on the online display", "edit", "options", "1", "true;false", "1;0");
	Setting::add($MODULE_NAME, "icon_fancy_online", "Show profession icons in the online display", "edit", "options", "1", "true;false", "1;0");
	Setting::add($MODULE_NAME, "online_group_by", "How to group online list", "edit", "options", "profession", "profession;guild");
	Setting::add($MODULE_NAME, "online_show_org_guild", "Show org/rank for players in guild channel", "edit", "options", "1", "Show org and rank;Show rank only;Show no org info", "2;1;0");
	Setting::add($MODULE_NAME, "online_show_org_priv", "Show org/rank for players in private channel", "edit", "options", "2", "Show org and rank;Show rank only;Show no org info", "2;1;0");
	Setting::add($MODULE_NAME, "online_colorful", "Use fancy coloring for online list", "edit", "options", "1", "true;false", "1;0");
	Setting::add($MODULE_NAME, "online_admin", "Show admin levels in online list", "edit", "options", "0", "true;false", "1;0");

	Event::register($MODULE_NAME, "logOn", "logonline.php", "none", "Sends a tell to players on logon showing who is online in org");

	// Help files
	Help::register($MODULE_NAME, "online", "online.txt", "guild", "Show who is on from the guild");
	Help::register($MODULE_NAME, "afk", "afk.txt", "all", "Going AFK");
?>