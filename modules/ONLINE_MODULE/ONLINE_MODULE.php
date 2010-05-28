<?php
	$MODULE_NAME = "ONLINE_MODULE";

	//Lastseen
	bot::command("", "$MODULE_NAME/lastseen.php", "lastseen", "guild", "Shows the logoff time of a player");

	//Online
	bot::command("", "$MODULE_NAME/online.php", "online", "all", "Shows who is the PrivChan");
	bot::command("", "$MODULE_NAME/chatlist.php", "sm", "all", "Shows who is the PrivChan");
	bot::command("", "$MODULE_NAME/chatlist.php", "chatlist", "all", "Shows who is the PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "count", "all", "Shows who is the PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "adv", "all", "Shows Adventurers in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "agent", "all", "Shows Agents in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "crat", "all", "Shows Bureaucrats in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "doc", "all", "Shows Doctors in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "enf", "all", "Shows Enforcers in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "eng", "all", "Shows Engineers in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "fix", "all", "Shows Fixers in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "keep", "all", "Shows Keepers in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "ma", "all", "Shows Martial-Artists in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "mp", "all", "Shows Meta-Physicists in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "nt", "all", "Shows Nano-Technicians in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "sol", "all", "Shows Soldiers in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "shade", "all", "Shows Shades in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "trader", "all", "Shows Traders in PrivChan");

	//Group
	bot::regGroup("online", $MODULE_NAME, "Show who is online(guild or privatechat)", "adv", "agent", "crat", "doc", "enf", "eng", "fix", "keep", "ma", "mp", "nt", "sol", "shade", "trader", "sm", "chatlist", "online", "count");

	//Settings
	bot::addsetting("online_tell", "Mode for Online Cmd in tells", "edit", "0", "Shows online privatechat members;Shows online guild members", "1;0");
	bot::addsetting("count_tell", "Mode for Count Cmd in tells", "edit", "1", "Shows online privatechat members;Shows online guild members", "1;0");
	bot::addsetting("chatlist_tell", "Mode for Chatlist Cmd in tells", "edit", "1", "Shows online privatechat members;Shows online guild members", "1;0");
	bot::addsetting("logonline_tell", "Enables the Online tell on logon", "edit", "0", "On;Off", "1;0");
	bot::addsetting("fancy_online", "Enables the fancy delimiters for the online display", "edit", "1", "On;Off", "1;0");
	bot::addsetting("icon_fancy_online", "Enables the use of icons in fancy delimiter mode", "edit", "1", "On;Off", "1;0");

	bot::event("logOn", "$MODULE_NAME/logonline.php", "none", "Sends a tell to players on logon showing who is online in org");

	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");

	//Helpfiles
	bot::help("chatlist", "$MODULE_NAME/chatlist.txt", "all", "Showing who is in the private group", "Raidbot");
	bot::help("online", "$MODULE_NAME/online.txt", "guild", "Show who is on from the guild", "Basic Guild Commands");
	bot::help("lastseen", "$MODULE_NAME/lastseen.txt", "guild", "Check when an orgmember was online", "Basic Guild Commands");
?>