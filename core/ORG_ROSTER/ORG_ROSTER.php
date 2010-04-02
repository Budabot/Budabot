<?php
$MODULE_NAME = "ORG_ROSTER";
$PLUGIN_VERSION = 0.1;

	//Setup of the Basic Guild Modules
	bot::regevent("setup", "$MODULE_NAME/Setup.php");

    // Org Roster list creation and Notify on/off handling
	bot::regevent("24hrs", "$MODULE_NAME/Roster_Guild.php");
	bot::regevent("orgmsg", "$MODULE_NAME/Notify_auto.php");
    bot::regevent("logOn", "$MODULE_NAME/Notify_auto.php");
    bot::regevent("logOff", "$MODULE_NAME/Notify_auto.php");
	bot::command("guild", "$MODULE_NAME/Notify.php", "notify", "mod", "Adding a member man. to the notify list");
	bot::command("msg", "$MODULE_NAME/Notify.php", "notify", "mod", "Adding a member man. to the notify list");
	bot::command("priv", "$MODULE_NAME/Notify.php", "notify", "mod", "Adding a member man. to the notify list");

	//Helpfile
    bot::help("notify", "$MODULE_NAME/notify.txt", "mod", "Add or remove a player from the notify list.", "Notify List");
?>