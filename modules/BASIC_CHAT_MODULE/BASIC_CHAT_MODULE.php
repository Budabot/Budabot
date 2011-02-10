<?php
	$MODULE_NAME = "BASIC_CHAT_MODULE";

	//Invite/Leave/lock commands
	Setting::add($MODULE_NAME, "priv_status", "no", "hide", "open");
	Setting::add($MODULE_NAME, "priv_status_reason", "no", "hide", "not set");	

	//Check macros
	Command::register($MODULE_NAME, "", "check.php", "check", "rl", "Checks who of the raidgroup is in the area");

	//Topic set/show
	Event::register($MODULE_NAME, "joinPriv", "topic.php", "topic", "Show Topic when someone joins PrivChat");
	Event::register($MODULE_NAME, "logOn", "topic_logon.php", "none", "Show Topic on logon of members");
	Command::register($MODULE_NAME, "", "topic.php", "topic", "all", "Show Topic");
	Subcommand::register($MODULE_NAME, "", "topic.php", "topic (.+)", "leader", "topic", "Change Topic");
	Setting::add($MODULE_NAME, "topic", "Topic for Priv Channel", "noedit", "");
	Setting::add($MODULE_NAME, "topic_setby", "no", "hide", "none");
	Setting::add($MODULE_NAME, "topic_time", "no", "hide", time());

    // Afk Check
	Event::register($MODULE_NAME, "priv", "afk_check.php", "none", "Afk check");
	Command::register($MODULE_NAME, "", "afk.php", "afk", "all", "Sets a member afk");
	Command::register($MODULE_NAME, "", "afk.php", "kiting", "all", "Sets a member kiting");

	//Leader
	Command::register($MODULE_NAME, "priv", "leader.php", "leader", "all", "Sets the Leader of the raid");
	Subcommand::register($MODULE_NAME, "priv", "leader.php", "leader (.+)", "raidleader", "leader", "Set a specific Leader");
	Command::register($MODULE_NAME, "", "leaderecho_cmd.php", "leaderecho", "leader", "Set if the text of the leader will be repeated");
	Event::register($MODULE_NAME, "priv", "leaderecho.php", "leader", "leader echo");
	Event::register($MODULE_NAME, "leavePriv", "leader.php", "none", "Removes leader when the leader leaves the channel");
	Setting::add($MODULE_NAME, "leaderecho", "Repeat the text of the raidleader", "edit", "1", "ON;OFF", "1;0");
	Setting::add($MODULE_NAME, "leaderecho_color", "Color for Raidleader echo", "edit", "<font color=#FFFF00>", "color");

	//Assist
	Command::register($MODULE_NAME, "", "assist.php", "assist", "all", "Shows an Assist macro");
	Command::register($MODULE_NAME, "", "assist.php", "callers", "all", "Shows an Assist macro");
	Subcommand::register($MODULE_NAME, "", "assist.php", "assist (.+)", "leader", "assist", "Set a new assist");
	Command::register($MODULE_NAME, "", "heal_assist.php", "heal", "all", "Creates/showes an Doc Assist macro");
	Subcommand::register($MODULE_NAME, "", "heal_assist.php", "heal (.+)", "leader", "heal", "Set a new Doc assist");

	//Tell
	Command::register($MODULE_NAME, "", "tell.php", "tell", "all", "Repeats a message 3 times");
	Command::register($MODULE_NAME, "", "cmd.php", "cmd", "rl", "Creates a highly visible messaage");

	//Helpfiles
	Help::register($MODULE_NAME, "afk_priv", "afk.txt", "all", "Going AFK");
	Help::register($MODULE_NAME, "assist", "assist.txt", "all", "Creating an Assist Macro");
	Help::register($MODULE_NAME, "check", "check.txt", "all", "See of the ppls are in the area");
	Help::register($MODULE_NAME, "heal", "healassist.txt", "all", "Creating an Healassist Macro");
	Help::register($MODULE_NAME, "leader", "leader.txt", "all", "Set a Leader of a Raid/Echo on/off");
	Help::register($MODULE_NAME, "tell", "tell.txt", "leader", "How to use tell");
	Help::register($MODULE_NAME, "topic", "topic.txt", "raidleader", "Set the Topic of the raid");
	Help::register($MODULE_NAME, "cmd", "cmd.txt", "leader", "How to use cmd");
?>
