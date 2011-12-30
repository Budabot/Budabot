<?php
	$db->add_table_replace('#__org_history', 'org_history');	
	$db->loadSQLFile($MODULE_NAME, "org_members");
	$db->loadSQLFile($MODULE_NAME, "org_history");

	Event::register($MODULE_NAME, "setup", "setup.php");
	
	$command->register($MODULE_NAME, "", "logon_msg.php", "logon", "guild", "Sets a Logon Msg");
	$command->register($MODULE_NAME, "", "logoff_msg.php", "logoff", "guild", "Sets a Logoff Msg");
	$command->register($MODULE_NAME, "", "logonadmin.php", "logonadmin", "mod", "Admin command for editing another person's logon message");
	$command->register($MODULE_NAME, "", "logoffadmin.php", "logoffadmin", "mod", "Admin command for editing another person's logoff message");
	$command->register($MODULE_NAME, "", "org_history.php", "orghistory", "guild", "Shows the org history (invites and kicks and leaves) for a player");
	$command->register($MODULE_NAME, "", "lastseen.php", "lastseen", "guild", "Shows the logoff time of a player");
	$command->register($MODULE_NAME, "", "tellall.php", "tellall", "leader", "Sends a tell to all online guild members");
	$command->register($MODULE_NAME, "", "notify.php", "notify", "rl", "Adding a char manually to the notify list");
	$command->register($MODULE_NAME, "", "inactive_mem.php", "inactivemem", "rl", "Check for inactive members");
	$command->register($MODULE_NAME, "", "updateorg.php", "updateorg", "rl", "Forcing an update of the org roster");

	Event::register($MODULE_NAME, "24hrs", "roster_guild.php", "Download guild roster xml and update guild members");
	Event::register($MODULE_NAME, "orgmsg", "notify_auto.php", "Automatically add and remove chars from the guild roster as they leave and join the guild");
	Event::register($MODULE_NAME, "logOn", "notify_logon_guild.php", "Shows an org member login in chat");
	Event::register($MODULE_NAME, "logOff", "notify_logoff_guild.php", "Shows an org member logoff in chat");
	Event::register($MODULE_NAME, "logOff", "record_lastseen.php", "Records when each member of the org logs off for lastseen command");
	Event::register($MODULE_NAME, "orgmsg", "org_action_listener.php", "Capture Org Invite/Kick/Leave messages for orghistory");
	
	Setting::add($MODULE_NAME, "max_logon_msg_size", "Maximum characters a logon message can have", "edit", "number", "200", "100;200;300;400", '', "mod");
	Setting::add($MODULE_NAME, "max_logoff_msg_size", "Maximum characters a logoff message can have", "edit", "number", "200", "100;200;300;400", '', "mod");
	Setting::add($MODULE_NAME, "first_and_last_alt_only", "Show logon/logoff for first/last alt only", "edit", "options", "0", "true;false", "1;0");
	
	Help::register($MODULE_NAME, "inactivemem", "inactivemem.txt", "rl", "Help on Checking for Inactive Members");
	Help::register($MODULE_NAME, "updateorg", "updateorg.txt", "rl", "Force an update of org roster");
	Help::register($MODULE_NAME, "lastseen", "lastseen.txt", "guild", "Check when an orgmember was online");
	Help::register($MODULE_NAME, "logon", "logon_msg.txt", "guild", "Changing your logon message");
	Help::register($MODULE_NAME, "logoff", "logoff_msg.txt", "guild", "Changing your logoff message");
	Help::register($MODULE_NAME, "logonadmin", "logonadmin.txt", "mod", "Changing another character's logon message");
	Help::register($MODULE_NAME, "logoffadmin", "logoffadmin.txt", "mod", "Changing another character's logoff message");
	Help::register($MODULE_NAME, "notify", "notify.txt", "rl", "Add or remove a player from the notify list");
	Help::register($MODULE_NAME, "tellall", "tellall.txt", "leader", "Send a tell to all online guild members");
	Help::register($MODULE_NAME, "orghistory", "org_history.txt", "guild", "How to use orghistory");
?>