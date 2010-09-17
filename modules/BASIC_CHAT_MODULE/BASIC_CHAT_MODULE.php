<?php 
	$MODULE_NAME = "BASIC_CHAT_MODULE";

	//Invite/Leave/lock commands
	bot::addsetting("topic_guild_join", "Show Topic in guild on join", "edit", "0", "ON;OFF", "1;0", "mod", "$MODULE_NAME/topic_show_guild.txt");
	bot::addsetting("priv_status", "no", "hide", "open");
	bot::addsetting("priv_status_reason", "no", "hide", "not set");	

	//Check macros
	bot::command("priv", "$MODULE_NAME/check.php", "check", "rl", "Checks who of the raidgroup is in the area");	
	
	//Topic set/show
	bot::event("joinPriv", "$MODULE_NAME/topic.php", "topic", "Show Topic when someone joins PrivChat");
	bot::event("logOn", "$MODULE_NAME/topic_logon.php", "none", "Show Topic on logon of members");
	bot::command("", "$MODULE_NAME/topic.php", "topic", "all", "Show Topic");
	bot::subcommand("", "$MODULE_NAME/topic.php", "topic (.+)", "leader", "topic", "Change Topic");
	bot::addsetting("topic", "Topic for Priv Channel", "noedit", "No Topic set.");	
	bot::addsetting("topic_setby", "no", "hide", "none");
	bot::addsetting("topic_time", "no", "hide", time());

    // Afk Check
	bot::event("priv", "$MODULE_NAME/afk_check.php", "none", "Afk check");
	bot::command("priv", "$MODULE_NAME/afk.php", "afk", "all", "Sets a member afk");

	//Leader
	bot::command("priv", "$MODULE_NAME/leader.php", "leader", "all", "Sets the Leader of the raid");
	bot::subcommand("priv", "$MODULE_NAME/leader.php", "leader (.+)", "raidleader", "leader", "Set a specific Leader");
	bot::command("priv", "$MODULE_NAME/leaderecho_cmd.php", "leaderecho", "leader", "Set if the text of the leader will be repeated");
	bot::event("priv", "$MODULE_NAME/leaderecho.php", "leader", "leader echo");
	bot::addsetting("leaderecho", "Repeat the text of the raidleader", "edit", "1", "ON;OFF", "1;0");
	bot::addsetting("leaderecho_color", "Color for Raidleader echo", "edit", "<font color=#FFFF00>", "color");

	//Assist
	bot::command("", "$MODULE_NAME/assist.php", "assist", "all", "Creates/shows an Assist macro");
	bot::subcommand("", "$MODULE_NAME/assist.php", "assist (.+)", "leader", "assist", "Set a new assist");
	bot::command("", "$MODULE_NAME/heal_assist.php", "heal", "all", "Creates/showes an Doc Assist macro");
	bot::subcommand("", "$MODULE_NAME/heal_assist.php", "heal (.+)", "leader", "heal", "Set a new Doc assist");

	//Tell
	bot::command("priv", "$MODULE_NAME/tell.php", "tell", "all", "Repeats a message 3 times");
	bot::command("", "$MODULE_NAME/cmd.php", "cmd", "rl", "Creates a highly visible messaage");
	
	//updateme
	bot::command("", "$MODULE_NAME/updateme.php", "updateme", "all", "Updates Charinfos from a player");

	//Helpfiles
	bot::help("afk_priv", "$MODULE_NAME/afk.txt", "all", "Going AFK");
	bot::help("assist", "$MODULE_NAME/assist.txt", "all", "Creating an Assist Macro");
	bot::help("check", "$MODULE_NAME/check.txt", "all", "See of the ppls are in the area");
	bot::help("heal_assist", "$MODULE_NAME/healassist.txt", "all", "Creating an Healassist Macro");
	bot::help("leader", "$MODULE_NAME/leader.txt", "all", "Set a Leader of a Raid/Echo on/off");
	bot::help("tell", "$MODULE_NAME/tell.txt", "leader", "How to use tell");
	bot::help("topic", "$MODULE_NAME/topic.txt", "raidleader", "Set the Topic of the raid");
	bot::help("updateme", "$MODULE_NAME/updateme.txt", "all", "Update your character infos");
	bot::help("cmd", "$MODULE_NAME/cmd.txt", "leader", "How to use cmd");
?>
