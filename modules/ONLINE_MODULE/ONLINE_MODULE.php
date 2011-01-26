<?php
	require_once "online_func.php";

	$MODULE_NAME = "ONLINE_MODULE";
	
	//Setup
	bot::event($MODULE_NAME, "setup", "setup.php");

	//Online
	bot::command("", "$MODULE_NAME/online.php", "online", "all", "Shows who is the private channel");
	bot::command("msg guild", "$MODULE_NAME/count.php", "count", "all", "Shows who is the private channel");
	bot::command("msg guild", "$MODULE_NAME/count.php", "adv", "all", "Shows Adventurers in private channel");
	bot::command("msg guild", "$MODULE_NAME/count.php", "agent", "all", "Shows Agents in private channel");
	bot::command("msg guild", "$MODULE_NAME/count.php", "crat", "all", "Shows Bureaucrats in private channel");
	bot::command("msg guild", "$MODULE_NAME/count.php", "doc", "all", "Shows Doctors in private channel");
	bot::command("msg guild", "$MODULE_NAME/count.php", "enf", "all", "Shows Enforcers in private channel");
	bot::command("msg guild", "$MODULE_NAME/count.php", "eng", "all", "Shows Engineers in private channel");
	bot::command("msg guild", "$MODULE_NAME/count.php", "fix", "all", "Shows Fixers in private channel");
	bot::command("msg guild", "$MODULE_NAME/count.php", "keep", "all", "Shows Keepers in private channel");
	bot::command("msg guild", "$MODULE_NAME/count.php", "ma", "all", "Shows Martial-Artists in private channel");
	bot::command("msg guild", "$MODULE_NAME/count.php", "mp", "all", "Shows Meta-Physicists in private channel");
	bot::command("msg guild", "$MODULE_NAME/count.php", "nt", "all", "Shows Nano-Technicians in private channel");
	bot::command("msg guild", "$MODULE_NAME/count.php", "sol", "all", "Shows Soldiers in private channel");
	bot::command("msg guild", "$MODULE_NAME/count.php", "shade", "all", "Shows Shades in private channel");
	bot::command("msg guild", "$MODULE_NAME/count.php", "trader", "all", "Shows Traders in private channel");

	//Group
	bot::regGroup("online", $MODULE_NAME, "Show who is online(guild or privatechat)", "adv", "agent", "crat", "doc", "enf", "eng", "fix", "keep", "ma", "mp", "nt", "sol", "shade", "trader", "online", "count");
	
	bot::event($MODULE_NAME, "logOn", "record_logon_guild.php", "none", "Records an org member login in db");
	bot::event($MODULE_NAME, "logOff", "record_logoff_guild.php", "none", "Records an org member logoff in db");

	//Settings
	bot::addsetting($MODULE_NAME, "relaydb", "Database for merging online lists", "edit", "0", "text", '0', "mod");
	bot::addsetting($MODULE_NAME, "chatlist_tell", "Mode for Chatlist Cmd in tells", "edit", "1", "Shows online privatechat members;Shows online guild members", "1;0");
	bot::addsetting($MODULE_NAME, "fancy_online", "Enables the fancy delimiters for the online display", "edit", "1", "On;Off", "1;0");
	bot::addsetting($MODULE_NAME, "icon_fancy_online", "Enables the use of icons in fancy delimiter mode", "edit", "1", "On;Off", "1;0");

	bot::event($MODULE_NAME, "logOn", "logonline.php", "none", "Sends a tell to players on logon showing who is online in org");

	//Help files
	bot::help($MODULE_NAME, "online", "online.txt", "guild", "Show who is on from the guild");
?>