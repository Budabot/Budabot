<?php
	$MODULE_NAME = "BASIC_GUILD_MODULE";

	//Setup of the Basic Guild Modules
	bot::event("setup", "$MODULE_NAME/setup.php");
    
	// Logon Handling
	bot::command("", "$MODULE_NAME/logon_msg.php", "logon", "all", "Sets a Logon Msg");

    // Afk Check
	bot::event("guild", "$MODULE_NAME/afk_check.php", "none", "Afk check");
	bot::command("guild", "$MODULE_NAME/afk.php", "afk", "all", "Sets a member afk");
	bot::command("guild", "$MODULE_NAME/kiting.php", "kiting", "all", "Sets a member afk kiting");

	//Verifies the Onlinelist every 1hour
	bot::event("1hour", "$MODULE_NAME/online_check.php", "online", "Online check");

    // Alternative Characters
	bot::command("", "$MODULE_NAME/alts.php", "alts", "all", "Alt Char handling");
	bot::command("", "$MODULE_NAME/alts.php", "altsadmin", "mod", "Alt Char handling (admin)");

    // Show orgmembers
	bot::command("", "$MODULE_NAME/orgmembers.php", "orgmembers", "all", "Show the Members(sorted by name) of the org");
	bot::command("", "$MODULE_NAME/orgranks.php", "orgranks", "all", "Show the Members(sorted by rank) of the org");

	//Force an update of the org roster
	bot::command("msg", "$MODULE_NAME/updateorg.php", "updateorg", "mod", "Forcing an update of the org roster");
	
	//Tell and Tellall
	bot::command("guild msg", "$MODULE_NAME/tell.php", "tell", "rl", "Repeats an message 3 times in Orgchat");
	bot::command("guild msg", "$MODULE_NAME/tell.php", "tellall", "rl", "Sends a tell to all online guildmembers");
		
	//Helpfile
    bot::help("afk_kiting", "$MODULE_NAME/afk_kiting.txt", "guild", "Set yourself AFK/Kiting", "Basic Guild Commands");
    bot::help("alts", "$MODULE_NAME/alts.txt", "guild", "How to set alts", "Basic Guild Commands");
	bot::help("altsadmin", "$MODULE_NAME/altsadmin.txt", "guild", "How to set alts (admins)", "Basic Guild Commands");
    bot::help("LogOnMsg", "$MODULE_NAME/logonmsg.txt", "guild", "Changing your logon message", "Basic Guild Commands");
    bot::help("OrgMembers", "$MODULE_NAME/orgmembers_orgranks.txt", "guild", "Show current OrgMembers", "Basic Guild Commands");    
    bot::help("tell_guild", "$MODULE_NAME/tell.txt", "guild", "Repeat a msg 3times/Send a tell to online members", "Basic Guild Commands");
    bot::help("updateorg", "$MODULE_NAME/updateorg.txt", "mod", "Force an update of orgrooster", "Basic Guild Commands");    
?>