<?php
	require_once "online_func.php";

	$MODULE_NAME = "ONLINE_MODULE";

	//Online
	Command::register($MODULE_NAME, "", "online.php", "online", "all", "Shows who is the private channel");
	Command::register($MODULE_NAME, "msg guild", "count.php", "count", "all", "Shows who is the private channel");
	Command::register($MODULE_NAME, "msg guild", "count.php", "adv", "all", "Shows Adventurers in private channel");
	Command::register($MODULE_NAME, "msg guild", "count.php", "agent", "all", "Shows Agents in private channel");
	Command::register($MODULE_NAME, "msg guild", "count.php", "crat", "all", "Shows Bureaucrats in private channel");
	Command::register($MODULE_NAME, "msg guild", "count.php", "doc", "all", "Shows Doctors in private channel");
	Command::register($MODULE_NAME, "msg guild", "count.php", "enf", "all", "Shows Enforcers in private channel");
	Command::register($MODULE_NAME, "msg guild", "count.php", "eng", "all", "Shows Engineers in private channel");
	Command::register($MODULE_NAME, "msg guild", "count.php", "fix", "all", "Shows Fixers in private channel");
	Command::register($MODULE_NAME, "msg guild", "count.php", "keep", "all", "Shows Keepers in private channel");
	Command::register($MODULE_NAME, "msg guild", "count.php", "ma", "all", "Shows Martial-Artists in private channel");
	Command::register($MODULE_NAME, "msg guild", "count.php", "mp", "all", "Shows Meta-Physicists in private channel");
	Command::register($MODULE_NAME, "msg guild", "count.php", "nt", "all", "Shows Nano-Technicians in private channel");
	Command::register($MODULE_NAME, "msg guild", "count.php", "sol", "all", "Shows Soldiers in private channel");
	Command::register($MODULE_NAME, "msg guild", "count.php", "shade", "all", "Shows Shades in private channel");
	Command::register($MODULE_NAME, "msg guild", "count.php", "trader", "all", "Shows Traders in private channel");
	
	Event::register($MODULE_NAME, "logOn", "record_logon_guild.php", "none", "Records an org member login in db");
	Event::register($MODULE_NAME, "logOff", "record_logoff_guild.php", "none", "Records an org member logoff in db");

	//Settings
	Setting::add($MODULE_NAME, "relaydb", "Database for merging online lists", "edit", "text", "0", "", '', "mod");
	Setting::add($MODULE_NAME, "chatlist_tell", "Mode for Chatlist Cmd in tells", "edit", "options", "1", "Shows online privatechat members;Shows online guild members", "1;0");
	Setting::add($MODULE_NAME, "fancy_online", "Show fancy delimiters on the online display", "edit", "options", "1", "true;false", "1;0");
	Setting::add($MODULE_NAME, "icon_fancy_online", "Show profession icons in the online display", "edit", "options", "1", "true;false", "1;0");

	Event::register($MODULE_NAME, "logOn", "logonline.php", "none", "Sends a tell to players on logon showing who is online in org");

	//Help files
	Help::register($MODULE_NAME, "online", "online.txt", "guild", "Show who is on from the guild");
?>