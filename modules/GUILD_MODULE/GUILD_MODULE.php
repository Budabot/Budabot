<?php
	$db->add_table_replace('#__org_history', 'org_history');	
	$db->loadSQLFile($MODULE_NAME, "org_members");
	$db->loadSQLFile($MODULE_NAME, "org_history");

	$event->register($MODULE_NAME, "setup", "setup.php");
	
	$command->register($MODULE_NAME, "", "logon_msg.php", "logon", "guild", "Sets a Logon Msg");
	$command->register($MODULE_NAME, "", "logoff_msg.php", "logoff", "guild", "Sets a Logoff Msg");
	$command->register($MODULE_NAME, "", "logonadmin.php", "logonadmin", "mod", "Admin command for editing another person's logon message");
	$command->register($MODULE_NAME, "", "logoffadmin.php", "logoffadmin", "mod", "Admin command for editing another person's logoff message");
	$command->register($MODULE_NAME, "", "org_history.php", "orghistory", "guild", "Shows the org history (invites and kicks and leaves) for a player");
	$command->register($MODULE_NAME, "", "lastseen.php", "lastseen", "guild", "Shows the logoff time of a player");
	$command->register($MODULE_NAME, "", "tellall.php", "tellall", "rl", "Sends a tell to all online guild members");
	$command->register($MODULE_NAME, "", "notify.php", "notify", "rl", "Adding a char manually to the notify list");
	$command->register($MODULE_NAME, "", "inactive_mem.php", "inactivemem", "rl", "Check for inactive members");
	$command->register($MODULE_NAME, "", "updateorg.php", "updateorg", "rl", "Forcing an update of the org roster");

	$event->register($MODULE_NAME, "24hrs", "roster_guild.php", "Download guild roster xml and update guild members");
	$event->register($MODULE_NAME, "orgmsg", "notify_auto.php", "Automatically add and remove chars from the guild roster as they leave and join the guild");
	$event->register($MODULE_NAME, "logOn", "notify_logon_guild.php", "Shows an org member login in chat");
	$event->register($MODULE_NAME, "logOff", "notify_logoff_guild.php", "Shows an org member logoff in chat");
	$event->register($MODULE_NAME, "logOff", "record_lastseen.php", "Records when each member of the org logs off for lastseen command");
	$event->register($MODULE_NAME, "orgmsg", "org_action_listener.php", "Capture Org Invite/Kick/Leave messages for orghistory");
	
	$setting->add($MODULE_NAME, "max_logon_msg_size", "Maximum characters a logon message can have", "edit", "number", "200", "100;200;300;400", '', "mod");
	$setting->add($MODULE_NAME, "max_logoff_msg_size", "Maximum characters a logoff message can have", "edit", "number", "200", "100;200;300;400", '', "mod");
	$setting->add($MODULE_NAME, "first_and_last_alt_only", "Show logon/logoff for first/last alt only", "edit", "options", "0", "true;false", "1;0");
	
	$help->register($MODULE_NAME, "inactivemem", "inactivemem.txt", "rl", "Help on Checking for Inactive Members");
	$help->register($MODULE_NAME, "updateorg", "updateorg.txt", "rl", "Force an update of org roster");
	$help->register($MODULE_NAME, "lastseen", "lastseen.txt", "guild", "Check when an orgmember was online");
	$help->register($MODULE_NAME, "logon", "logon_msg.txt", "guild", "Changing your logon message");
	$help->register($MODULE_NAME, "logoff", "logoff_msg.txt", "guild", "Changing your logoff message");
	$help->register($MODULE_NAME, "logonadmin", "logonadmin.txt", "mod", "Changing another character's logon message");
	$help->register($MODULE_NAME, "logoffadmin", "logoffadmin.txt", "mod", "Changing another character's logoff message");
	$help->register($MODULE_NAME, "notify", "notify.txt", "rl", "Add or remove a player from the notify list");
	$help->register($MODULE_NAME, "tellall", "tellall.txt", "rl", "Send a tell to all online guild members");
	$help->register($MODULE_NAME, "orghistory", "org_history.txt", "guild", "How to use orghistory");
?>