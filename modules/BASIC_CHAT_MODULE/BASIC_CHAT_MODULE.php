<?php 
	$MODULE_NAME = "BASIC_CHAT_MODULE";
	$PLUGIN_VERSION = 0.1;

	//Invite/Leave/lock commands
	$this->addsetting("topic_guild_join", "Show Topic in guild on join", "edit", "0", "ON;OFF", "1;0", MODERATOR, "$MODULE_NAME/topic_show_guild.txt");
	$this->addsetting("priv_status", "no", "hide", "open");
	$this->addsetting("priv_status_reason", "no", "hide", "not set");	

	//Check macros
	$this->command("priv", "$MODULE_NAME/check.php", "check", RAIDLEADER, "Checks who of the raidgroup is in the area");	
	
	//Topic set/show
	$this->event("joinPriv", "$MODULE_NAME/topic.php", "topic", "Show Topic when someone joins PrivChat");
	$this->event("logOn", "$MODULE_NAME/topic_logon.php", "none", "Show Topic on logon of members");
	$this->command("", "$MODULE_NAME/topic.php", "topic", ALL, "Show Topic");
	$this->subcommand("", "$MODULE_NAME/topic.php", "topic (.+)", LEADER, "topic", "Change Topic");
	$this->addsetting("topic", "Topic for Priv Channel", "noedit", "No Topic set.");	
	$this->addsetting("topic_setby", "no", "hide", "none");
	$this->addsetting("topic_time", "no", "hide", time());

    //Afk Check
	$this->event("priv", "$MODULE_NAME/afk_check.php", "afk");
	$this->command("priv", "$MODULE_NAME/afk.php", "afk", ALL, "Sets a member afk");

	//Leader
	$this->command("priv", "$MODULE_NAME/leader.php", "leader", ALL, "Sets the Leader of the raid");
	$this->subcommand("priv", "$MODULE_NAME/leader.php", "leader (.+)", LEADER, "leader", "Set a specific Leader");
	$this->command("priv", "$MODULE_NAME/leaderecho_cmd.php", "leaderecho", LEADER, "Set if the text of the leader will be repeated");
	$this->event("priv", "$MODULE_NAME/leaderecho.php", "leader");
	$this->addsetting("leaderecho", "Repeat the text of the raidleader", "edit", "1", "ON;OFF", "1;0");
	$this->addsetting("leaderecho_color", "Color for Raidleader echo", "edit", "<font color=#FFFF00>", "color");

	//Assist
	$this->command("", "$MODULE_NAME/assist.php", "assist", ALL, "Creates/shows an Assist macro");
	$this->subcommand("", "$MODULE_NAME/assist.php", "assist (.+)", LEADER, "assist", "Set a new assist");
	$this->command("", "$MODULE_NAME/heal_assist.php", "heal", ALL, "Creates/showes an Doc Assist macro");
	$this->subcommand("", "$MODULE_NAME/heal_assist.php", "heal (.+)", LEADER, "heal", "Set a new Doc assist");

	//Tell
	$this->command("priv", "$MODULE_NAME/tell.php", "tell", ALL, "Repeats a Message 3times");
	
	//updateme
	$this->command("", "$MODULE_NAME/updateme.php", "updateme", ALL, "Updates Charinfos from a player");

	//Set admin and user news
	$this->command("", "$MODULE_NAME/set_news.php", "privnews", RAIDLEADER, "Set news that are shown on privjoin");
	$this->command("", "$MODULE_NAME/set_news.php", "adminnews", MODERATOR, "Set adminnews that are shown on privjoin");
	$this->addsetting("news", "no", "hide", "Not set.");
	$this->addsetting("adminnews", "no", "hide", "Not set.");	
	
	//Help files
	$this->help("afk_priv", "$MODULE_NAME/afk.txt", ALL, "Going AFK");
	$this->help("assist", "$MODULE_NAME/assist.txt", ALL, "Creating an Assist Macro");
	$this->help("check", "$MODULE_NAME/check.txt", ALL, "See of the ppls are in the area");
	$this->help("heal_assist", "$MODULE_NAME/healassist.txt", ALL, "Creating an Healassist Macro");
	$this->help("leader", "$MODULE_NAME/leader.txt", ALL, "Set a Leader of a Raid/Echo on/off");
	$this->help("priv_news", "$MODULE_NAME/priv_news.txt", RAIDLEADER, "Set Privategroup News");
	$this->help("tell", "$MODULE_NAME/tell.txt", LEADER, "Repeating of a msg 3times");
	$this->help("topic", "$MODULE_NAME/topic.txt", RAIDLEADER, "Set the Topic of the raid");
	$this->help("updateme", "$MODULE_NAME/updateme.txt", ALL, "Update your character infos");
?>
