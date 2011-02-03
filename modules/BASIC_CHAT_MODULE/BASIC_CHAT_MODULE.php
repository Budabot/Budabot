<?php
	$MODULE_NAME = "BASIC_CHAT_MODULE";

	//Invite/Leave/lock commands
	Setting::add($MODULE_NAME, "priv_status", "no", "hide", "open");
	Setting::add($MODULE_NAME, "priv_status_reason", "no", "hide", "not set");	

	//Check macros
	bot::command("", "$MODULE_NAME/check.php", "check", "rl", "Checks who of the raidgroup is in the area");

	//Topic set/show
	Event::register($MODULE_NAME, "joinPriv", "topic.php", "topic", "Show Topic when someone joins PrivChat");
	Event::register($MODULE_NAME, "logOn", "topic_logon.php", "none", "Show Topic on logon of members");
	bot::command("", "$MODULE_NAME/topic.php", "topic", "all", "Show Topic");
	bot::subcommand("", "$MODULE_NAME/topic.php", "topic (.+)", "leader", "topic", "Change Topic");
	Setting::add($MODULE_NAME, "topic", "Topic for Priv Channel", "noedit", "");
	Setting::add($MODULE_NAME, "topic_setby", "no", "hide", "none");
	Setting::add($MODULE_NAME, "topic_time", "no", "hide", time());

    // Afk Check
	Event::register($MODULE_NAME, "priv", "afk_check.php", "none", "Afk check");
	bot::command("priv", "$MODULE_NAME/afk.php", "afk", "all", "Sets a member afk");

	//Leader
	bot::command("priv", "$MODULE_NAME/leader.php", "leader", "all", "Sets the Leader of the raid");
	bot::subcommand("priv", "$MODULE_NAME/leader.php", "leader (.+)", "raidleader", "leader", "Set a specific Leader");
	bot::command("", "$MODULE_NAME/leaderecho_cmd.php", "leaderecho", "leader", "Set if the text of the leader will be repeated");
	Event::register($MODULE_NAME, "priv", "leaderecho.php", "leader", "leader echo");
	Event::register($MODULE_NAME, "leavePriv", "leader.php", "none", "Removes leader when the leader leaves the channel");
	Setting::add($MODULE_NAME, "leaderecho", "Repeat the text of the raidleader", "edit", "1", "ON;OFF", "1;0");
	Setting::add($MODULE_NAME, "leaderecho_color", "Color for Raidleader echo", "edit", "<font color=#FFFF00>", "color");

	//Assist
	bot::command("", "$MODULE_NAME/assist.php", "assist", "all", "Shows an Assist macro");
	bot::command("", "$MODULE_NAME/assist.php", "callers", "all", "Shows an Assist macro");
	bot::subcommand("", "$MODULE_NAME/assist.php", "assist (.+)", "leader", "assist", "Set a new assist");
	bot::command("", "$MODULE_NAME/heal_assist.php", "heal", "all", "Creates/showes an Doc Assist macro");
	bot::subcommand("", "$MODULE_NAME/heal_assist.php", "heal (.+)", "leader", "heal", "Set a new Doc assist");

	//Tell
	bot::command("", "$MODULE_NAME/tell.php", "tell", "all", "Repeats a message 3 times");
	bot::command("", "$MODULE_NAME/cmd.php", "cmd", "rl", "Creates a highly visible messaage");

	//Helpfiles
	bot::help($MODULE_NAME, "afk_priv", "afk.txt", "all", "Going AFK");
	bot::help($MODULE_NAME, "assist", "assist.txt", "all", "Creating an Assist Macro");
	bot::help($MODULE_NAME, "check", "check.txt", "all", "See of the ppls are in the area");
	bot::help($MODULE_NAME, "heal", "healassist.txt", "all", "Creating an Healassist Macro");
	bot::help($MODULE_NAME, "leader", "leader.txt", "all", "Set a Leader of a Raid/Echo on/off");
	bot::help($MODULE_NAME, "tell", "tell.txt", "leader", "How to use tell");
	bot::help($MODULE_NAME, "topic", "topic.txt", "raidleader", "Set the Topic of the raid");
	bot::help($MODULE_NAME, "cmd", "cmd.txt", "leader", "How to use cmd");
?>
