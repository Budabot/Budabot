<?php
	$MODULE_NAME = "ORG_ROSTER";
	$PLUGIN_VERSION = 0.1;

	//Setup of the Basic Guild Modules
	$this->regevent("setup", "$MODULE_NAME/setup.php");

    // Org Roster list creation and Notify on/off handling
	$this->regevent("24hrs", "$MODULE_NAME/roster_guild.php");
	$this->regevent("orgmsg", "$MODULE_NAME/notify_auto.php");
    $this->regevent("logOn", "$MODULE_NAME/notify_auto.php");
    $this->regevent("logOff", "$MODULE_NAME/notify_auto.php");
	$this->command("guild", "$MODULE_NAME/notify.php", "notify", MODERATOR, "Adding a member man. to the notify list");
	$this->command("msg", "$MODULE_NAME/notify.php", "notify", MODERATOR, "Adding a member man. to the notify list");
	$this->command("priv", "$MODULE_NAME/notify.php", "notify", MODERATOR, "Adding a member man. to the notify list");

	$this->addsetting("bot_notify", "Show/Hide Logoffs in Org Chat (Spam Prevention)", "edit", "1", "Show Logoffs;Hide Logoffs", '1;0', MODERATOR, "$MODULE_NAME/botnotify.txt");
	
	//Helpfile
    $this->help("notify", "$MODULE_NAME/notify.txt", MODERATOR, "Add or remove a player from the notify list.");
?>