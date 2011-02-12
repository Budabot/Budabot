<?php
	$MODULE_NAME = "GUILD_MODULE";
	
	DB::loadSQLFile($MODULE_NAME, "guild_chatlist");

	//Setup
	Event::register($MODULE_NAME, "setup", "setup.php");
	
	// Logon Handling
	Command::register($MODULE_NAME, "", "logon_msg.php", "logon", "guild", "Sets a Logon Msg");
	
	//Lastseen
	Command::register($MODULE_NAME, "", "lastseen.php", "lastseen", "guild", "Shows the logoff time of a player");
	
	//Verifies the Onlinelist every hour
	Event::register($MODULE_NAME, "1hour", "online_check.php", "none", "Online check");
	
	//Tell and Tellall
	Command::register($MODULE_NAME, "guild msg", "tell.php", "tell", "leader", "Repeats an message 3 times in Orgchat");
	Command::register($MODULE_NAME, "guild msg", "tell.php", "tellall", "leader", "Sends a tell to all online guildmembers");

    // Org Roster list creation and Notify on/off handling
	Event::register($MODULE_NAME, "24hrs", "roster_guild.php", "none", "Download guild roster xml and update guild members");
	Event::register($MODULE_NAME, "orgmsg", "notify_auto.php", "none", "Automatically add and remove chars from the guild roster as they leave and join the guild");
	Command::register($MODULE_NAME, "guild", "notify.php", "notify", "mod", "Adding a char manually to the notify list");
	Command::register($MODULE_NAME, "msg", "notify.php", "notify", "mod", "Adding a char manually to the notify list");
	Command::register($MODULE_NAME, "priv", "notify.php", "notify", "mod", "Adding a char manually to the notify list");
	
	Command::register($MODULE_NAME, "", "inactive_mem.php", "inactivemem", "admin", "Check for inactive members");
	Command::register($MODULE_NAME, "", "updateorg.php", "updateorg", "mod", "Forcing an update of the org roster");
	
	// Show orgmembers
	Command::register($MODULE_NAME, "", "orgmembers.php", "orgmembers", "guild", "Show the Members(sorted by name) of the org");
	Command::register($MODULE_NAME, "", "orgranks.php", "orgranks", "guild", "Show the Members(sorted by rank) of the org");
	
	Event::register($MODULE_NAME, "logOn", "notify_logon_guild.php", "none", "Shows an org member login in chat");
	Event::register($MODULE_NAME, "logOff", "notify_logoff_guild.php", "none", "Shows an org member logoff in chat");
	
	Event::register($MODULE_NAME, "logOff", "record_lastseen.php", "none", "Records when each member of the org logs off for lastseen command");
	
	//Helpfile
	Help::register($MODULE_NAME, "inactivemem", "inactivemem.txt", "admin", "Help on Checking for Inactive Members");
	Help::register($MODULE_NAME, "updateorg", "updateorg.txt", "mod", "Force an update of org roster");
	Help::register($MODULE_NAME, "orgmembers", "orgmembers_orgranks.txt", "guild", "Show current OrgMembers");
	Help::register($MODULE_NAME, "orgranks", "orgmembers_orgranks.txt", "guild", "Show current OrgMembers");
	Help::register($MODULE_NAME, "lastseen", "lastseen.txt", "guild", "Check when an orgmember was online");
	Help::register($MODULE_NAME, "logon", "logon_msg.txt", "guild", "Changing your logon message");
	Help::register($MODULE_NAME, "notify", "notify.txt", "mod", "Add or remove a player from the notify list.");
	Help::register($MODULE_NAME, "tell", "tell.txt", "guild", "How to use tell and tellall");
?>