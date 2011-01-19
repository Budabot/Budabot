<?php
	$MODULE_NAME = "GUILD_MODULE";

	//Setup of the Basic Guild Modules
	bot::regevent("setup", "$MODULE_NAME/setup.php");
	
	//Verifies the Onlinelist every hour
	bot::event($MODULE_NAME, "1hour", "online_check.php", "online", "Online check");
	
	// Logon Handling
	bot::command("", "$MODULE_NAME/logon_msg.php", "logon", "guild", "Sets a Logon Msg");
	
	// Afk Check
	bot::event($MODULE_NAME, "guild", "afk_check.php", "none", "Afk check");
	bot::command("guild", "$MODULE_NAME/afk.php", "afk", "all", "Sets a member afk");
	bot::command("guild", "$MODULE_NAME/kiting.php", "kiting", "all", "Sets a member afk kiting");
	
	//Tell and Tellall
	bot::command("guild msg", "$MODULE_NAME/tell.php", "tell", "leader", "Repeats an message 3 times in Orgchat");
	bot::command("guild msg", "$MODULE_NAME/tell.php", "tellall", "leader", "Sends a tell to all online guildmembers");
	
	//Helpfile
	bot::help($MODULE_NAME, "afk_kiting", "afk_kiting.txt", "guild", "Set yourself AFK/Kiting");
	bot::help($MODULE_NAME, "tell", "tell.txt", "guild", "How to use tell and tellall");

    // Org Roster list creation and Notify on/off handling
	bot::regevent("24hrs", "$MODULE_NAME/roster_guild.php");
	bot::regevent("orgmsg", "$MODULE_NAME/notify_auto.php");
    bot::regevent("logOn", "$MODULE_NAME/notify_auto.php");
    bot::regevent("logOff", "$MODULE_NAME/notify_auto.php");
	bot::command("guild", "$MODULE_NAME/notify.php", "notify", "mod", "Adding a member man. to the notify list");
	bot::command("msg", "$MODULE_NAME/notify.php", "notify", "mod", "Adding a member man. to the notify list");
	bot::command("priv", "$MODULE_NAME/notify.php", "notify", "mod", "Adding a member man. to the notify list");
	
	bot::command("", "$MODULE_NAME/inactive_mem.php", "inactivemem", "admin", "Check for inactive members");
	bot::command("", "$MODULE_NAME/updateorg.php", "updateorg", "mod", "Forcing an update of the org roster");
	
	// Show orgmembers
	bot::command("", "$MODULE_NAME/orgmembers.php", "orgmembers", "guild", "Show the Members(sorted by name) of the org");
	bot::command("", "$MODULE_NAME/orgranks.php", "orgranks", "guild", "Show the Members(sorted by rank) of the org");

	bot::addsetting($MODULE_NAME, "bot_notify", "Show/Hide Logoffs in Org Chat (Spam Prevention)", "edit", "1", "Show Logoffs;Hide Logoffs", '1;0', "mod", "$MODULE_NAME/botnotify.txt");
	
	//Helpfile
    bot::help($MODULE_NAME, "notify", "notify.txt", "mod", "Add or remove a player from the notify list.");
	bot::help($MODULE_NAME, "inactivemem", "manage_guild.txt", "admin", "Help on Checking for Inactive Members");
	bot::help($MODULE_NAME, "updateorg", "updateorg.txt", "mod", "Force an update of org roster");
	bot::help($MODULE_NAME, "logonmsg", "logonmsg.txt", "guild", "Changing your logon message");
	bot::help($MODULE_NAME, "orgmembers", "orgmembers_orgranks.txt", "guild", "Show current OrgMembers");
?>