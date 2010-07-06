<?php
	$MODULE_NAME = "ONLINE_MODULE";

	//Lastseen
	bot::command("", "$MODULE_NAME/lastseen.php", "lastseen", GUILDMEMBER, "Shows the logoff time of a player");

	//Online
	bot::command("", "$MODULE_NAME/online.php", "online", MEMBER, "Shows who is the PrivChan");
	bot::command("", "$MODULE_NAME/chatlist.php", "sm", MEMBER, "Shows who is the PrivChan");
	bot::command("", "$MODULE_NAME/chatlist.php", "chatlist", MEMBER, "Shows who is the PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "count", MEMBER, "Shows who is the PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "adv", MEMBER, "Shows Adventurers in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "agent", MEMBER, "Shows Agents in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "crat", MEMBER, "Shows Bureaucrats in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "doc", MEMBER, "Shows Doctors in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "enf", MEMBER, "Shows Enforcers in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "eng", MEMBER, "Shows Engineers in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "fix", MEMBER, "Shows Fixers in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "keep", MEMBER, "Shows Keepers in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "ma", MEMBER, "Shows Martial-Artists in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "mp", MEMBER, "Shows Meta-Physicists in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "nt", MEMBER, "Shows Nano-Technicians in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "sol", MEMBER, "Shows Soldiers in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "shade", MEMBER, "Shows Shades in PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "trader", MEMBER, "Shows Traders in PrivChan");

	//Group
	bot::regGroup("online", $MODULE_NAME, "Show who is online(guild or privatechat)", "adv", "agent", "crat", "doc", "enf", "eng", "fix", "keep", "ma", "mp", "nt", "sol", "shade", "trader", "sm", "chatlist", "online", "count");

	//Settings
	bot::addsetting("relaydb", "Database for merging online lists", "edit", "0", "text", '0', MODERATOR);
	bot::addsetting("online_tell", "Mode for Online Cmd in tells", "edit", "0", "Shows online privatechat members;Shows online guild members", "1;0", MODERATOR);
	bot::addsetting("count_tell", "Mode for Count Cmd in tells", "edit", "1", "Shows online privatechat members;Shows online guild members", "1;0", MODERATOR);
	bot::addsetting("chatlist_tell", "Mode for Chatlist Cmd in tells", "edit", "1", "Shows online privatechat members;Shows online guild members", "1;0", MODERATOR);
	bot::addsetting("logonline_tell", "Enables the Online tell on logon", "edit", "0", "On;Off", "1;0", MODERATOR);
	bot::addsetting("fancy_online", "Enables the fancy delimiters for the online display", "edit", "1", "On;Off", "1;0", MODERATOR);
	bot::addsetting("icon_fancy_online", "Enables the use of icons in fancy delimiter mode", "edit", "1", "On;Off", "1;0", MODERATOR);

	bot::event("logOn", "$MODULE_NAME/logonline.php", "none", "Sends a tell to players on logon showing who is online in org");
	bot::event("logOn", "$MODULE_NAME/logon_guild.php", "none", "Shows a logon from a member");
	bot::event("logOff", "$MODULE_NAME/logoff_guild.php", "none", "Shows a logoff from a member");

	//Help files
	bot::help("chatlist", "$MODULE_NAME/chatlist.txt", MEMBER, "Showing who is in the private group");
	bot::help("online", "$MODULE_NAME/online.txt", MEMBER, "Show who is on from the guild");
	bot::help("lastseen", "$MODULE_NAME/lastseen.txt", MEMBER, "Check when an orgmember was online");
?>