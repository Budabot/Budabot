<?php
	require_once 'Rally.class.php';
	require_once 'chatsay.class.php';
	require_once 'ChatAssistController.class.php';

	$chatBot->registerInstance($MODULE_NAME, 'Rally', new Rally);
	$chatBot->registerInstance($MODULE_NAME, 'ChatSay', new ChatSay);
	$chatBot->registerInstance($MODULE_NAME, 'ChatAssistController', new ChatAssistController);

	// Check macros
	$command->register($MODULE_NAME, "", "check.php", "check", "all", "Checks who of the raidgroup is in the area", "check.txt");

	// Topic set/show
	$event->register($MODULE_NAME, "joinPriv", "topic_logon.php", "Show topic when someone joins the private channel");
	$event->register($MODULE_NAME, "logOn", "topic_logon.php", "Show Topic on logon of members");
	$command->register($MODULE_NAME, "", "topic.php", "topic", "all", "Show Topic", "topic.txt");
	$subcommand->register($MODULE_NAME, "", "topic_change.php", "topic (.+)", "rl", "topic", "Change Topic");
	$setting->add($MODULE_NAME, "topic", "Topic for Priv Channel", "noedit", "text", '');
	$setting->add($MODULE_NAME, "topic_setby", "Character who set the topic", "noedit", "text", '');
	$setting->add($MODULE_NAME, "topic_time", "Time the topic was set", "noedit", "text", '');

	// Leader/Leader echo
	$command->register($MODULE_NAME, "priv", "leader.php", "leader", "all", "Sets the Leader of the raid", "leader.txt");
	$subcommand->register($MODULE_NAME, "priv", "leader_set.php", "leader (.+)", "rl", "leader", "Set a specific Leader");
	$command->register($MODULE_NAME, "", "leaderecho_cmd.php", "leaderecho", "rl", "Set if the text of the leader will be repeated", "leader.txt");
	$event->register($MODULE_NAME, "priv", "leaderecho.php", "Repeats what the leader says in the color of leaderecho_color setting");
	$event->register($MODULE_NAME, "leavePriv", "leader_leave.php", "Removes leader when the leader leaves the channel");
	$setting->add($MODULE_NAME, "leaderecho", "Repeat the text of the leader", "edit", "options", "1", "true;false", "1;0");
	$setting->add($MODULE_NAME, "leaderecho_color", "Color for leader echo", "edit", "color", "<font color=#FFFF00>");

	// Tell
	$command->register($MODULE_NAME, "", "tell.php", "tell", "rl", "Repeats a message 3 times", "tell.txt");
	$command->register($MODULE_NAME, "", "cmd.php", "cmd", "rl", "Creates a highly visible messaage", "cmd.txt");
?>
