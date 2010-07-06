<?php 
	$MODULE_NAME = "BASIC_CHAT_MODULE";
	$PLUGIN_VERSION = 0.1;

	//Invite/Leave/lock commands
	bot::addsetting("topic_guild_join", "Show Topic in guild on join", "edit", "0", "ON;OFF", "1;0", MODERATOR, "$MODULE_NAME/topic_show_guild.txt");
	bot::addsetting("priv_status", "no", "hide", "open");
	bot::addsetting("priv_status_reason", "no", "hide", "not set");	

	//Check macros
	bot::command("priv", "$MODULE_NAME/check.php", "check", RAIDLEADER, "Checks who of the raidgroup is in the area");	
	
	//Topic set/show
	bot::event("joinPriv", "$MODULE_NAME/topic.php", "topic", "Show Topic when someone joins PrivChat");
	bot::event("logOn", "$MODULE_NAME/topic_logon.php", "none", "Show Topic on logon of members");
	bot::command("", "$MODULE_NAME/topic.php", "topic", ALL, "Show Topic");
	bot::subcommand("", "$MODULE_NAME/topic.php", "topic (.+)", LEADER, "topic", "Change Topic");
	bot::addsetting("topic", "Topic for Priv Channel", "noedit", "No Topic set.");	
	bot::addsetting("topic_setby", "no", "hide", "none");
	bot::addsetting("topic_time", "no", "hide", time());

    //Afk Check
	bot::event("priv", "$MODULE_NAME/afk_check.php", "afk");
	bot::command("priv", "$MODULE_NAME/afk.php", "afk", ALL, "Sets a member afk");

	//Leader
	bot::command("priv", "$MODULE_NAME/leader.php", "leader", ALL, "Sets the Leader of the raid");
	bot::subcommand("priv", "$MODULE_NAME/leader.php", "leader (.+)", LEADER, "leader", "Set a specific Leader");
	bot::command("priv", "$MODULE_NAME/leaderecho_cmd.php", "leaderecho", LEADER, "Set if the text of the leader will be repeated");
	bot::event("priv", "$MODULE_NAME/leaderecho.php", "leader");
	bot::addsetting("leaderecho", "Repeat the text of the raidleader", "edit", "1", "ON;OFF", "1;0");
	bot::addsetting("leaderecho_color", "Color for Raidleader echo", "edit", "<font color=#FFFF00>", "color");

	//Assist
	bot::command("", "$MODULE_NAME/assist.php", "assist", ALL, "Creates/shows an Assist macro");
	bot::subcommand("", "$MODULE_NAME/assist.php", "assist (.+)", LEADER, "assist", "Set a new assist");
	bot::command("", "$MODULE_NAME/heal_assist.php", "heal", ALL, "Creates/showes an Doc Assist macro");
	bot::subcommand("", "$MODULE_NAME/heal_assist.php", "heal (.+)", LEADER, "heal", "Set a new Doc assist");

	//Tell
	bot::command("priv", "$MODULE_NAME/tell.php", "tell", ALL, "Repeats a Message 3times");
	
	//updateme
	bot::command("", "$MODULE_NAME/updateme.php", "updateme", ALL, "Updates Charinfos from a player");

	//Set admin and user news
	bot::command("", "$MODULE_NAME/set_news.php", "privnews", RAIDLEADER, "Set news that are shown on privjoin");
	bot::command("", "$MODULE_NAME/set_news.php", "adminnews", MODERATOR, "Set adminnews that are shown on privjoin");
	bot::addsetting("news", "no", "hide", "Not set.");
	bot::addsetting("adminnews", "no", "hide", "Not set.");	
	
	//Help files
	bot::help("afk_priv", "$MODULE_NAME/afk.txt", ALL, "Going AFK");
	bot::help("assist", "$MODULE_NAME/assist.txt", ALL, "Creating an Assist Macro");
	bot::help("check", "$MODULE_NAME/check.txt", ALL, "See of the ppls are in the area");
	bot::help("heal_assist", "$MODULE_NAME/healassist.txt", ALL, "Creating an Healassist Macro");
	bot::help("leader", "$MODULE_NAME/leader.txt", ALL, "Set a Leader of a Raid/Echo on/off");
	bot::help("priv_news", "$MODULE_NAME/priv_news.txt", RAIDLEADER, "Set Privategroup News");
	bot::help("tell", "$MODULE_NAME/tell.txt", LEADER, "Repeating of a msg 3times");
	bot::help("topic", "$MODULE_NAME/topic.txt", RAIDLEADER, "Set the Topic of the raid");
	bot::help("updateme", "$MODULE_NAME/updateme.txt", ALL, "Update your character infos");
?>
