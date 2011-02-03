<?php
	$MODULE_NAME = "GUILD_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "guild_chatlist");

	//Setup
	bot::event($MODULE_NAME, "setup", "setup.php");
	
	// Logon Handling
	bot::command("", "$MODULE_NAME/logon_msg.php", "logon", "guild", "Sets a Logon Msg");
	
	//Lastseen
	bot::command("", "$MODULE_NAME/lastseen.php", "lastseen", "guild", "Shows the logoff time of a player");
	
	//Verifies the Onlinelist every hour
	bot::event($MODULE_NAME, "1hour", "online_check.php", "none", "Online check");
	
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
	bot::event($MODULE_NAME, "24hrs", "roster_guild.php", "none", "Download guild roster xml and update guild members");
	bot::event($MODULE_NAME, "orgmsg", "notify_auto.php", "none", "Automatically add and remove chars from the guild roster as they leave and join the guild");
	bot::command("guild", "$MODULE_NAME/notify.php", "notify", "mod", "Adding a char manually to the notify list");
	bot::command("msg", "$MODULE_NAME/notify.php", "notify", "mod", "Adding a char manually to the notify list");
	bot::command("priv", "$MODULE_NAME/notify.php", "notify", "mod", "Adding a char manually to the notify list");
	
	bot::command("", "$MODULE_NAME/inactive_mem.php", "inactivemem", "admin", "Check for inactive members");
	bot::command("", "$MODULE_NAME/updateorg.php", "updateorg", "mod", "Forcing an update of the org roster");
	
	// Show orgmembers
	bot::command("", "$MODULE_NAME/orgmembers.php", "orgmembers", "guild", "Show the Members(sorted by name) of the org");
	bot::command("", "$MODULE_NAME/orgranks.php", "orgranks", "guild", "Show the Members(sorted by rank) of the org");
	
	bot::event($MODULE_NAME, "logOn", "notify_logon_guild.php", "none", "Shows an org member login in chat");
	bot::event($MODULE_NAME, "logOff", "notify_logoff_guild.php", "none", "Shows an org member logoff in chat");
	
	bot::event($MODULE_NAME, "logOff", "record_lastseen.php", "none", "Records when each member of the org logs off for lastseen command");
	
	//Helpfile
	bot::help($MODULE_NAME, "inactivemem", "manage_guild.txt", "admin", "Help on Checking for Inactive Members");
	bot::help($MODULE_NAME, "updateorg", "updateorg.txt", "mod", "Force an update of org roster");
	bot::help($MODULE_NAME, "orgmembers", "orgmembers_orgranks.txt", "guild", "Show current OrgMembers");
	bot::help($MODULE_NAME, "orgranks", "orgmembers_orgranks.txt", "guild", "Show current OrgMembers");
	bot::help($MODULE_NAME, "lastseen", "lastseen.txt", "guild", "Check when an orgmember was online");
	bot::help($MODULE_NAME, "logon", "logon_msg.txt", "guild", "Changing your logon message");
	bot::help($MODULE_NAME, "notify", "notify.txt", "mod", "Add or remove a player from the notify list.");
?>