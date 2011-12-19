<?php
	// Check macros
	Command::register($MODULE_NAME, "", "check.php", "check", "rl", "Checks who of the raidgroup is in the area");

	// Topic set/show
	Event::register($MODULE_NAME, "joinPriv", "topic_logon.php", "Show Topic when someone joins PrivChat");
	Event::register($MODULE_NAME, "logOn", "topic_logon.php", "Show Topic on logon of members");
	Command::register($MODULE_NAME, "", "topic.php", "topic", "all", "Show Topic");
	Subcommand::register($MODULE_NAME, "", "topic_change.php", "topic (.+)", "leader", "topic", "Change Topic");
	Setting::add($MODULE_NAME, "topic", "Topic for Priv Channel", "noedit", "text", '');
	Setting::add($MODULE_NAME, "topic_setby", "Character who set the topic", "noedit", "text", '');
	Setting::add($MODULE_NAME, "topic_time", "Time the topic was set", "noedit", "text", '');

	// Leader
	Command::register($MODULE_NAME, "priv", "leader.php", "leader", "all", "Sets the Leader of the raid");
	Subcommand::register($MODULE_NAME, "priv", "leader_set.php", "leader (.+)", "leader", "leader", "Set a specific Leader");
	Command::register($MODULE_NAME, "", "leaderecho_cmd.php", "leaderecho", "leader", "Set if the text of the leader will be repeated", 'leader');
	Event::register($MODULE_NAME, "priv", "leaderecho.php", "Repeats what the leader says in the color of leaderecho_color setting");
	Event::register($MODULE_NAME, "leavePriv", "leader_leave.php", "Removes leader when the leader leaves the channel", 'leader');
	Setting::add($MODULE_NAME, "leaderecho", "Repeat the text of the leader", "edit", "options", "1", "true;false", "1;0");
	Setting::add($MODULE_NAME, "leaderecho_color", "Color for leader echo", "edit", "color", "<font color=#FFFF00>");

	// Assist
	Command::register($MODULE_NAME, "", "assist.php", "assist", "all", "Shows an Assist macro");
	CommandAlias::register($MODULE_NAME, "assist", "callers");
	Subcommand::register($MODULE_NAME, "", "assist_set.php", "assist (.+)", "leader", "assist", "Set a new assist");
	Command::register($MODULE_NAME, "", "healassist.php", "heal", "all", "Creates/showes an Doc Assist macro");
	Subcommand::register($MODULE_NAME, "", "healassist_set.php", "heal (.+)", "leader", "heal", "Set a new Doc assist");
	CommandAlias::register($MODULE_NAME, "heal", "healassist");

	// Tell
	Command::register($MODULE_NAME, "", "tell.php", "tell", "all", "Repeats a message 3 times");
	Command::register($MODULE_NAME, "", "cmd.php", "cmd", "rl", "Creates a highly visible messaage");

	// Helpfiles
	Help::register($MODULE_NAME, "assist", "assist.txt", "all", "Creating an Assist Macro");
	Help::register($MODULE_NAME, "check", "check.txt", "all", "See of the ppls are in the area");
	Help::register($MODULE_NAME, "heal", "healassist.txt", "all", "Creating an Healassist Macro");
	Help::register($MODULE_NAME, "leader", "leader.txt", "all", "Set a Leader of a Raid/Echo on/off");
	Help::register($MODULE_NAME, "tell", "tell.txt", "leader", "How to use tell");
	Help::register($MODULE_NAME, "topic", "topic.txt", "leader", "Set the Topic of the raid");
	Help::register($MODULE_NAME, "cmd", "cmd.txt", "leader", "How to use cmd");
?>
