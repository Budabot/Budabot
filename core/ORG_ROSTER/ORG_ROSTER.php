<?php
	$MODULE_NAME = "ORG_ROSTER";

	//Setup of the Basic Guild Modules
	bot::regevent("setup", "$MODULE_NAME/setup.php");

    // Org Roster list creation and Notify on/off handling
	bot::regevent("24hrs", "$MODULE_NAME/roster_guild.php");
	bot::regevent("orgmsg", "$MODULE_NAME/notify_auto.php");
    bot::regevent("logOn", "$MODULE_NAME/notify_auto.php");
    bot::regevent("logOff", "$MODULE_NAME/notify_auto.php");
	bot::command("guild", "$MODULE_NAME/notify.php", "notify", "mod", "Adding a member man. to the notify list");
	bot::command("msg", "$MODULE_NAME/notify.php", "notify", "mod", "Adding a member man. to the notify list");
	bot::command("priv", "$MODULE_NAME/notify.php", "notify", "mod", "Adding a member man. to the notify list");

	bot::addsetting("bot_notify", "Show/Hide Logoffs in Org Chat (Spam Prevention)", "edit", "1", "Show Logoffs;Hide Logoffs", '1;0', "mod", "$MODULE_NAME/botnotify.txt");
	
	//Helpfile
    bot::help("notify", "$MODULE_NAME/notify.txt", "mod", "Add or remove a player from the notify list.", "Notify List");
?>